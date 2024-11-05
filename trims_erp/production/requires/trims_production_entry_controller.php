<? 
include('../../../includes/common.php');
session_start();

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }



if($action=="chk_po_level_variable")
{
	
    $sql =  sql_select("select production_update_area,id from variable_setting_trim_prod where company_name = $data and variable_list =1 and is_deleted = 0 and status_active = 1");
	$return_data="";
    if(count($sql)>0)
	{
		$return_data=$sql[0][csf('production_update_area')];
	}
	else
	{ 
		$return_data=0; 
	}
	
	echo $return_data;
	die;
}


if ($action=="load_drop_down_location")
{
	//echo $data; //die;
	$data=explode("_",$data);
	if($data[1]==1) $dropdown_name="cbo_location_name";
	else $dropdown_name="cbo_party_location";
	
	$location_arr=return_library_array( "select id, location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name",'id','location_name');
	if(count($location_arr)==1) $selected = key($location_arr); else $selected=0;
	echo create_drop_down( $dropdown_name, 150, $location_arr,"", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/trims_production_entry_controller',$data[0]+'_'+this.value, 'load_drop_down_machine_group', 'group_td');" );
	exit();
}




if ($action=="load_drop_down_machine_group")
{
	//echo $data;
	$data=explode("_",$data);
	//print_r($data);
	
	$location_id = $data[1];
	if ($location_id == 0 || $location_id == "") $location_cond = ""; else $location_cond = " and location_id=$location_id";
	echo create_drop_down( "cbo_mc_group", 150, "select DISTINCT  machine_group from lib_machine_name where company_id='$data[0]' $location_cond and status_active =1 and is_deleted=0 and category_id=25 order by machine_group","machine_group,machine_group", 1, "-- Select Group --", $selected, "load_drop_down( 'requires/trims_production_entry_controller',$data[0]+'_'+$location_id+'_'+this.value, 'load_drop_down_floor', 'floor_td');fnc_load_drop($data[0],document.getElementById('cbo_location_name').value,this.value,document.getElementById('cbo_floor_id').value);" );
	
	exit();
}

if ($action == "load_drop_down_floor")
{
	$data = explode("_", $data);
	//print_r($data);
	
		$company_id = $data[0];
		$location_id = $data[1];
		$machine_no = trim($data[2]);
		
		$floor_id_array=array();
		$sql = sql_select("select listagg(floor_id,',') within group (order by floor_id ) as floor_id ,machine_group  from lib_machine_name where category_id=25 and machine_group='$machine_no'  group by machine_group"); 
		foreach($sql as $result)
		{
			$floor_id_array[]=$result[csf("floor_id")]; 
		}
		$floor_idId=implode(",",$floor_id_array);
		if ($company_id == 0 || $company_id == "") $company_cond = ""; else $company_cond = " and a.company_id=$company_id";
		if ($location_id == 0 || $location_id == "") $location_cond = ""; else $location_cond = " and a.location_id=$location_id";
		if ($floor_idId == 0 || $floor_idId == "") $machine_no_cond = ""; else $machine_no_cond = " and a.id in ($floor_idId)";

	if(empty($machine_no)=="")
	{
		$sql_floor="select a.id, a.floor_name from lib_prod_floor a where a.production_process=18 $company_cond $location_cond $machine_no_cond  group by a.id, a.floor_name order by a.floor_name";
		echo create_drop_down("cbo_floor_id", 150, $sql_floor, "id,floor_name", 1, "-- Select Floor --", 0, "fnc_load_drop($data[0],document.getElementById('cbo_location_name').value,document.getElementById('cbo_mc_group').value,this.value);", "");
	}
	else
	{
		echo create_drop_down( "cbo_floor_id", 150, $blank_array,"", 1, "-- Select Floor --", $selected, "",1 );
	}
	
	
	/*echo create_drop_down("cbo_floor_id", 150, $sql_floor, "id,floor_name", 1, "-- Select Floor --", 0, "load_drop_down( 'requires/trims_production_entry_controller',$data[0]+'_'+$location_id+'_'+document.getElementById('cbo_mc_group').value+'_'+this.value, 'load_drop_machine', 'machine_td');fnc_load_drop($data[0],document.getElementById('cbo_location_name').value,document.getElementById('cbo_mc_group').value,this.value);", "");*/
	exit();
}

/*if ($action == "load_drop_machine")
{
		$data = explode("_", $data);
		$company_id = $data[0];
		$location_id = $data[1];
		$machine_no = trim($data[2]);
		$floor_id = trim($data[3]);
		$machine_id_array=array();
		$sql = sql_select("select listagg(id,',') within group (order by id ) as id ,machine_group  from lib_machine_name where category_id=25 and machine_group='$machine_no'  group by machine_group"); 
		foreach($sql as $result)
		{
			$machine_id_array[]=$result[csf("id")]; 
		}
		$machine_Id=implode(",",$machine_id_array);
		if ($company_id == 0 || $company_id == "") $company_cond = ""; else $company_cond = " and company_id=$company_id";
		if ($location_id == 0 || $location_id == "") $location_cond = ""; else $location_cond = " and location_id=$location_id";
		if ($machine_Id == 0 || $machine_Id == "") $machine_no_cond = ""; else $machine_no_cond = " and id in ($machine_Id)";
		if ($floor_id == 0 || $floor_id == "") $floor_cond = ""; else $floor_cond = " and floor_id=$floor_id";


		if(empty($machine_no)=="")
		{
			$sql_machine="select id, machine_no as machine_name from lib_machine_name where status_active=1 and is_deleted=0 $company_cond  $location_cond $machine_no_cond $floor_cond and category_id=25  and is_locked=0 $floor_cond order by seq_no";
		}
	echo create_drop_down("cboMachineName_1", 80, $sql_machine, "id,machine_name", 1, "-- Select Machine --", 0,"","",'','','','','','',"cboMachineName[]"); 
	exit();
}
*/

if($action=="machine_load")
{	
    $data = explode("_", $data);
	$company_id = $data[0];
	$location_id = $data[1];
	$machine_no = trim($data[2]);
	$floor_id = trim($data[3]);
	$machine_id_array=array();
	$sql = sql_select("select listagg(id,',') within group (order by id ) as id ,machine_group  from lib_machine_name where category_id=25 and machine_group='$machine_no'  group by machine_group"); 
	foreach($sql as $result)
	{
		$machine_id_array[]=$result[csf("id")]; 
	}
	$machine_Id=implode(",",$machine_id_array);
	if ($company_id == 0 || $company_id == "") $company_cond = ""; else $company_cond = " and company_id=$company_id";
	if ($location_id == 0 || $location_id == "") $location_cond = ""; else $location_cond = " and location_id=$location_id";
	if ($machine_Id == 0 || $machine_Id == "") $machine_no_cond = ""; else $machine_no_cond = " and id in ($machine_Id)";
	if ($floor_id == 0 || $floor_id == "") $floor_cond = ""; else $floor_cond = " and floor_id=$floor_id";
	$machine_dropdown="";

	if(empty($machine_no)==""){
		$sql_machine="select id, machine_no as machine_name from lib_machine_name where status_active=1 and is_deleted=0 $company_cond  $location_cond $machine_no_cond $floor_cond and category_id=25  and is_locked=0 $floor_cond order by seq_no";
	}
	for ($k=0; $k<=$data[4]; $k++)
	{
		if($machine_dropdown=="") $machine_dropdown=create_drop_down("cboMachineName_".$k, 80, $sql_machine, "id,machine_name", 1, "-- Select Machine --", 0,"","",'','','','','','',"cboMachineName[]");
		else $machine_dropdown.='&!&!'.create_drop_down("cboMachineName_".$k, 80, $sql_machine, "id,machine_name", 1, "-- Select Machine --", 0,"","",'','','','','','',"cboMachineName[]");
	}
	echo $machine_dropdown;
	die;
}

if($action=="load_drop_down_embl_type")
{
	$data=explode('_',$data);
	
	if($data[0]==1) $emb_type=$emblishment_print_type;
	else if($data[0]==2) $emb_type=$emblishment_embroy_type;
	else if($data[0]==3) $emb_type=$emblishment_wash_type;
	else if($data[0]==4) $emb_type=$emblishment_spwork_type;
	else if($data[0]==5) $emb_type=$emblishment_gmts_type;
	
	echo create_drop_down( "cboembtype_".$data[1], 80,$emb_type,"", 1, "-- Select --", "", "","","" ); 
	
	exit();
}

if ($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);

	if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
	else $load_function="";
	
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $data[2], "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );
	}	
	exit();	 
} 

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	/*echo '<pre>';
	print_r($cbo_company_name);die;*/
	$user_id=$_SESSION['logic_erp']['user_id'];

	$po_status_sql =  sql_select("select production_update_area,id from variable_setting_trim_prod where company_name = $cbo_company_name and variable_list =1 and is_deleted = 0 and status_active = 1");
	$chk_po_level_variable="";
    if(count($po_status_sql)>0)
	{
		$chk_po_level_variable=$po_status_sql[0][csf('production_update_area')];
	}
	else
	{ 
		$chk_po_level_variable=0; 
	}
	
	$variable_status=str_replace(",",'',$txt_variable_status);
	
	
	
	
	///////////validation//////////////////////
	
 //echo "10**select  id, sub_section,material_color,job_quantity from trims_job_card_dtls where status_active=1  and is_deleted = 0 and  job_no_mst='$txt_job_no'";die;
	
		$jobDtls_result =sql_select("select  id, sub_section,material_color,job_quantity from trims_job_card_dtls where status_active=1  and is_deleted = 0 and  job_no_mst='$txt_job_no'");
		$jobDtls_arr=array();
		foreach($jobDtls_result as $row)
		{
			//$jobDtls_arr[$row[csf('id')]]['sub_section']=$row[csf('sub_section')];
			//$jobDtls_arr[$row[csf('id')]]['material_color']=$row[csf('material_color')];
			$jobDtls_arr[$row[csf('id')]]['job_quantity']=$row[csf('job_quantity')];
		}
	
	
		//echo "10**select a.job_dtls_id,sum(a.production_qty) as production_qty, sum(a.qc_qty) as qc_qty from trims_production_dtls a,trims_production_mst b  where  a.mst_id=b.id and b.entry_form=269 and b.job_id=$hid_job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_dtls_id"; sie;
		
		
		
		 
			
		$up_conds="";
		if($update_id>0) 
		{
			 $up_conds=" and b.id not in($update_id)";
		}
	
		// echo "10**";
		 //echo "select a.job_dtls_id,sum(a.production_qty) as production_qty, sum(a.qc_qty) as qc_qty from trims_production_dtls a,trims_production_mst b  where  a.mst_id=b.id and b.entry_form=269 and b.job_id=$hid_job_id $up_conds and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_dtls_id"; die;
	
		$prod_result =sql_select("select a.job_dtls_id,sum(a.production_qty) as production_qty, sum(a.qc_qty) as qc_qty from trims_production_dtls a,trims_production_mst b  where  a.mst_id=b.id and b.entry_form=269 and b.job_id=$hid_job_id $up_conds and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_dtls_id");
		$cumelative_arr=array();
		foreach($prod_result as $row)
		{
			$cumelative_arr[$row[csf('job_dtls_id')]]['cum']=$row[csf('production_qty')];  
			$cumelative_arr[$row[csf('job_dtls_id')]]['qc']=$row[csf('qc_qty')];
			//$jobDtls_arr[$row[csf('id')]]['material_color']=$row[csf('material_color')];
		}
		
		
		

	
	
	
	//echo "10**".$variable_status; die;
	
	if ($operation==0) // Insert Start Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
		else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";
		
		$new_prod_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'TPE', date("Y",time()), 5, "select prod_no_prefix,prod_no_prefix_num from trims_production_mst where entry_form=269 and company_id=$cbo_company_name $insert_date_con order by id desc ", "prod_no_prefix", "prod_no_prefix_num" ));
		if($db_type==0)
		{
			$txt_prod_date=change_date_format(str_replace("'",'',$txt_prod_date),'yyyy-mm-dd');
		}
		else
		{
			$txt_prod_date=change_date_format(str_replace("'",'',$txt_prod_date), "", "",1);
		}
		$id=return_next_id("id","trims_production_mst",1);
		$id1=return_next_id( "id", "trims_production_dtls",1) ;
		$id3=return_next_id( "id", "trims_prod_order_breakdown", 1 );
		$rID3=true;
		$field_array="id, entry_form, trims_production, prod_no_prefix, prod_no_prefix_num, company_id,  location_id , within_group,  party_id, party_location ,production_date,  order_id, received_id, job_id, item, quantity, section_id, machine_group, floor, inserted_by, insert_date";
		
		$data_array="(".$id.", 269, '".$new_prod_no[0]."', '".$new_prod_no[1]."', '".$new_prod_no[2]."', '".$cbo_company_name."', '".$cbo_location_name."', '".$cbo_within_group."', '".$cbo_party_name."', '".$cbo_party_location."', '".$txt_prod_date."', '".$hid_order_id."', '".$hid_recv_id."', '".$hid_job_id."', '".$txt_item."', '".$txt_order_qty."', '".$cbo_section."', '".$cbo_mc_group."', '".$cbo_floor_id."', ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."')";
		
		$txt_job_no=$new_prod_no[0];
		$field_array2="id, mst_id, machine_id, item_description, gmts_color_id, gmts_size_id, color_id, size_id, uom,  impression, material_color_id, qty_reel,  total_head, qc_qty, production_qty, comp_prod, reject_qty, prod_time, remarks, booking_dtls_id, receive_dtls_id, job_dtls_id, break_id, production_level, inserted_by, insert_date";
 
  		$field_array3="id,mst_id,qc_pass_qty,reject_qty,remarks,buyer_po_no,receive_details_id, break_dtls_id,buyer_po_id,order_id,job_id, job_dtls_id,receive_id";
		
		
		
		
		
		/*$color_library_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
		$size_library_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");*/
		
		$data_array2= "";$data_array3="";  $add_commaa=0;$add_commadtls=0; $new_array_color=array();  $new_array_size=array();
		for($i=1; $i<=$total_row; $i++)
		{	
			$txtItem				= "txtItem_".$i; 
			$cboMachineName			= "cboMachineName_".$i; 
			$txtMcNo				= "txtMcNo_".$i; 
			$txtMcNoID				= "txtMcNoID_".$i;
			$cbosubProcess			= "cbosubProcess_".$i;
			$txtgmtscolorId			= "txtgmtscolorId_".$i;
			$txtgmtssizeId			= "txtgmtssizeId_".$i;
			$txtcolor				= "txtcolor_".$i;
			$txtsize				= "txtsize_".$i;
			$cboUom					= "cboUom_".$i;
			$txtImpression 			= "txtImpression_".$i;
			$cborawColor			= "cborawColor_".$i;
			$txtQtyReel 			= "txtQtyReel_".$i;
			$txtTotalHead 			= "txtTotalHead_".$i;			
			$txtProdQty 			= "txtProdQty_".$i;
			$txtQcPassQty 			= "txtQcPassQty_".$i;
			$txtCompProd 			= "txtCompProd_".$i;
			$txtRejectQty 			= "txtRejectQty_".$i;
			$cboProdTime 			= "cboProdTime_".$i;
			$txtRemarks 			= "txtRemarks_".$i;
			$hdnDtlsdata 			= "hdnDtlsdata_".$i;
			
			$hdnbookingDtlsId 		= "hdnbookingDtlsId_".$i;
			$hdnRcvDtlsId 			= "hdnRcvDtlsId_".$i;
			$hdnjobDtlsId 			= "hdnjobDtlsId_".$i;
			$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
			$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
			$hdnBreakIDs 			= "hdnBreakIDs_".$i;
			
			$txtBalQty 			= "txtBalQty_".$i;
			$hidBalQty 			= "hidBalQty_".$i;
			
			
			$txtcolorID			= "txtcolorID_".$i;
			$txtsizeID			= "txtsizeID_".$i;
			
			$job_quantity=$jobDtls_arr[str_replace("'",'',$$hdnjobDtlsId)]['job_quantity'];
			$cumelative_qty=$cumelative_arr[str_replace("'",'',$$hdnjobDtlsId)]['qc'];
 			$balance=$job_quantity-($cumelative_qty);
			
			//echo "10**";
			//echo "<pre>";
			//print_r($cumelative_arr);
			
			
		 //echo "10**".$balance."==".$job_quantity."==".$cumelative_qty; die;
		
		   if(($balance*1)<=0)
			{
				echo "40**Production Qty can't exceed Production Balance Qty.";
				disconnect($con);
				die;
			}
			
			//echo "10**".$balance."==".str_replace("'",'',$$hidBalQty)."==".str_replace("'",'',$$hidBalQty); die;
			
				/*if(hdnDtlsUpdateId!='')
				{
					if((txtCompProd*1+txtProdQty*1)<hdnCumDelQty*1)
					{
						alert("Cumulative Production Qty can't Less Cumulative Delivery Qty");
						check_field=1 ;
						return;
					}
				}*/
				
			
			
			

			if($chk_po_level_variable==3)
			{
				$txtcolor = 0;
				$txtsize = 0;
			}
			else
			{
				$txtcolor = $$txtcolor;
				$txtsize = $$txtsize;
			}

			//if(str_replace("'",'',$$txtbuyerPoId)=="") $txtbuyerPoId=0; else $txtbuyerPoId=str_replace("'",'',$$txtbuyerPoId);
			if ($add_commaa!=0) $data_array2 .=","; $add_comma=0;
			
			$data_array2 .="(".$id1.",".$id.",".$$cboMachineName.",".$$txtItem.",".$$txtgmtscolorId.",".$$txtgmtssizeId.",".$$txtcolorID.",".$$txtsizeID.",".$$cboUom.",".$$txtImpression.",".$$cborawColor.",".$$txtQtyReel.",".$$txtTotalHead.",".str_replace(",",'',$$txtQcPassQty).",".str_replace(",",'',$$txtProdQty).",".$$txtCompProd.",".$$txtRejectQty.",".$$cboProdTime.",".$$txtRemarks.",".$$hdnbookingDtlsId.",".$$hdnRcvDtlsId.",".$$hdnjobDtlsId.",".$$hdnBreakIDs.",'".$variable_status."','".$user_id."','".$pc_date_time."')";
			
				$field_array2="id, mst_id, machine_id, item_description, gmts_color_id, gmts_size_id, color_id, size_id, uom,  impression, material_color_id, qty_reel,  total_head, qc_qty, production_qty, comp_prod, reject_qty, prod_time, remarks, booking_dtls_id, receive_dtls_id, job_dtls_id, break_id, production_level, inserted_by, insert_date";
			
			
			$dtlsData=explode("*******",str_replace("'",'',$$hdnDtlsdata));
			$dtls_data=explode("***",$dtlsData[0]);
			//echo "10**<pre>";
			//print_r($dtls_data);
			//echo "**10".$dtls_data; die;
			
			if($variable_status==2)
			{
				for($j=0; $j<count($dtls_data); $j++)
				{
					$exdata=explode("_",$dtls_data[$j]);
					$poNo=$exdata[0];
					$txtQcPassQty=$exdata[1];
					$txtRejectQty=$exdata[2];
					$txtRemark=$exdata[3];
					$subconBrkId=$exdata[4];
					$subconDtlsId=$exdata[5];
					//$prodBrkID=$exdata[6];
					$recvid=$exdata[7];
					$buyerpoid=$exdata[8];
					if ($add_commadtls!=0) $data_array3 .=",";
					
					
				$data_array3.="(".$id3.",".$id1.",'".$txtQcPassQty."','".$txtRejectQty."','".$txtRemark."','".$poNo."','".$subconDtlsId."','".$subconBrkId."','".$buyerpoid."','".$hid_order_id."','".$hid_job_id."',".$$hdnjobDtlsId.",'".$recvid."')";
				$id3=$id3+1; $add_commadtls++; 
				}
			}
			
			$id1=$id1+1; $add_commaa++;
		}
		
		
		$flag=1; $rID1=true;
		$rID1=sql_insert("trims_production_mst",$field_array,$data_array,0);
		//echo "10**INSERT INTO trims_prod_order_breakdown (".$field_array3.") VALUES ".$data_array3; die;
		//echo "10**INSERT INTO trims_production_dtls (".$field_array2.") VALUES ".$data_array2; die;
		//echo "10**INSERT INTO trims_production_mst (".$field_array.") VALUES ".$data_array; die;
		if($rID1==1) $flag=1; else $flag=0;
		if($flag==1)
		{
			$rID2=sql_insert("trims_production_dtls",$field_array2,$data_array2,1);
			if($rID2==1) $flag=1; else $flag=0;
		}
		if($variable_status==2)
		{
			if($data_array3!="" && $flag==1)
			{
				$rID3=sql_insert("trims_prod_order_breakdown",$field_array3,$data_array3,1);
				if($rID3==1) $flag=1; else $flag=0;
			}
		}
		//echo "10**".$rID1."**".$rID2."**".$rID3; die;
	
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$hid_recv_id)."**".str_replace("'",'',$hid_job_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$hid_recv_id)."**".str_replace("'",'',$hid_job_id);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$hid_recv_id)."**".str_replace("'",'',$hid_job_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$hid_recv_id)."**".str_replace("'",'',$hid_job_id);
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

		/*$color_library_arr=return_library_array( "select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name"  );
		$size_library_arr=return_library_array( "select id,size_name from lib_size  where status_active=1 and is_deleted=0", "id", "size_name"  );*/
		/*$prod_result =sql_select("select job_dtls_id,sum(production_qty) as production_qty, sum(qc_qty) as qc_qty from trims_production_dtls  where status_active=1 and is_deleted=0 group by job_dtls_id");
		$cumelative_arr=array();
		foreach($prod_result as $row)
		{
			$cumelative_arr[$row[csf('job_dtls_id')]]['cum']=$row[csf('production_qty')];
			$cumelative_arr[$row[csf('job_dtls_id')]]['qc']=$row[csf('qc_qty')];
			//$jobDtls_arr[$row[csf('id')]]['material_color']=$row[csf('material_color')];
		}*/
		if($db_type==0) $txt_prod_date=change_date_format(str_replace("'",'',$txt_prod_date),'yyyy-mm-dd'); else $txt_prod_date=change_date_format(str_replace("'",'',$txt_prod_date), "", "",1);
		$field_array="production_date*order_id*received_id*job_id*item*quantity*section_id*machine_group*floor*updated_by*update_date";
		$data_array="'".$txt_prod_date."'*'".$hid_order_id."'*'".$hid_recv_id."'*'".$hid_job_id."'*'".$txt_item."'*'".$txt_order_qty."'*'".$cbo_section."'*'".$cbo_mc_group."'*'".$cbo_floor_id."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$field_array2="machine_id*gmts_color_id*gmts_size_id*color_id*size_id*uom*impression*material_color_id*qty_reel*total_head*qc_qty*production_qty*comp_prod*reject_qty*prod_time*remarks*production_level*updated_by*update_date";
		$field_array3="id, mst_id, machine_id, item_description,gmts_color_id,gmts_size_id,color_id, size_id, uom,  impression, material_color_id, qty_reel,  total_head, qc_qty, production_qty, comp_prod, reject_qty, prod_time, remarks, booking_dtls_id, receive_dtls_id, job_dtls_id, break_id, production_level, inserted_by, insert_date";
   
   		//$field_array4="id, mst_id,qc_pass_qty,reject_qty,remarks,buyer_po_no,receive_details_id, break_dtls_id";
		$field_array4="id,mst_id,qc_pass_qty,reject_qty,remarks,buyer_po_no,receive_details_id, break_dtls_id,buyer_po_id,order_id,job_id, job_dtls_id,receive_id";
		
		$field_array5="qc_pass_qty*reject_qty*remarks";
		$id1=return_next_id( "id", "trims_production_dtls",1) ;
		$id3=return_next_id( "id", "trims_prod_order_breakdown", 1) ;
		$add_comma=0;	$flag="";
		for($i=1; $i<=$total_row; $i++)
		{			
			$txtItem				= "txtItem_".$i; 
			$cboMachineName			= "cboMachineName_".$i; 
			$txtMcNo				= "txtMcNo_".$i; 
			$txtMcNoID				= "txtMcNoID_".$i;
			$cbosubProcess			= "cbosubProcess_".$i;
			$txtgmtscolorId			= "txtgmtscolorId_".$i;
			$txtgmtssizeId			= "txtgmtssizeId_".$i;
			$txtcolor				= "txtcolor_".$i;
			$txtsize				= "txtsize_".$i;
			$cboUom					= "cboUom_".$i;
			$txtImpression 			= "txtImpression_".$i;
			$cborawColor			= "cborawColor_".$i;
			$txtQtyReel 			= "txtQtyReel_".$i;
			$txtTotalHead 			= "txtTotalHead_".$i;			
			$txtProdQty 			= "txtProdQty_".$i;
			$txtQcPassQty 			= "txtQcPassQty_".$i;
			$txtCompProd 			= "txtCompProd_".$i;
			$txtRejectQty 			= "txtRejectQty_".$i;
			$cboProdTime 			= "cboProdTime_".$i;
			$txtRemarks 			= "txtRemarks_".$i;
			
			$hdnbookingDtlsId 		= "hdnbookingDtlsId_".$i;
			$hdnRcvDtlsId 			= "hdnRcvDtlsId_".$i;
			$hdnjobDtlsId 			= "hdnjobDtlsId_".$i;
			$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
			$hdnBreakIDs 			= "hdnBreakIDs_".$i;
			$hdnDtlsdata 			= "hdnDtlsdata_".$i;
			$txtDeletedId 			= "txtDeletedId_".$i;

			$txtBalQty 			= "txtBalQty_".$i;
			$hidBalQty 			= "hidBalQty_".$i;
			$txtcolorID			= "txtcolorID_".$i;
			$txtsizeID			= "txtsizeID_".$i;
			
			
			$job_quantity=$jobDtls_arr[str_replace("'",'',$$hdnjobDtlsId)]['job_quantity'];
			$cumelative_qty=$cumelative_arr[str_replace("'",'',$$hdnjobDtlsId)]['qc'];
 			$balance=$job_quantity-($cumelative_qty);
			
			//echo "10**";
			//echo "<pre>";
			//print_r($cumelative_arr);
			
			
		  // echo "10**".$balance."==".$job_quantity."==".$cumelative_qty; die;
		
		   if(($balance*1)<=0)
			{
				echo "40**Production Qty can't exceed Production Balance Qty.";
				disconnect($con);
				die;
			}
			
 			 

			if($chk_po_level_variable==3)
			{
				$txtcolor = 0;
				$txtsize = 0;
			}
			else
			{
				$txtcolor = $$txtcolor;
				$txtsize = $$txtsize;
			}

			
			//if(str_replace("'",'',$$txtbuyerPoId)=="") $txtbuyerPoId=0; else $txtbuyerPoId=str_replace("'",'',$$txtbuyerPoId);
			if(str_replace("'",'',$$hdnDtlsUpdateId)!=""){
				if(str_replace(",",'',$$txtCompProd)<str_replace(",",'',$$hdnCumDelQty)){
					echo "555**".$$hdnCumDelQty; disconnect($con); die;
				} else {
					$aa	=str_replace("'",'',$$hdnDtlsUpdateId);  
					$data_array2[$aa]=explode("*",("".$$cboMachineName."*".$$txtgmtscolorId."*".$$txtgmtssizeId."*".$$txtcolorID."*".$$txtsizeID."*".$$cboUom."*".$$txtImpression."*".$$cborawColor."*".str_replace(",",'',$$txtQtyReel)."*".str_replace(",",'',$$txtTotalHead)."*".str_replace(",",'',$$txtQcPassQty)."*".str_replace(",",'',$$txtProdQty)."*".str_replace(",",'',$$txtCompProd)."*".str_replace(",",'',$$txtRejectQty)."*".$$cboProdTime."*".$$txtRemarks."*".$variable_status."*".$user_id."*'".$pc_date_time."'"));
					$hdn_dtls_id_arr[]=str_replace("'",'',$$hdnDtlsUpdateId);
					//echo "10**".$$hdnDtlsdata.'==';
					$dtlsData=explode("*******",str_replace("'",'',$$hdnDtlsdata));
					$dtls_data=explode("***",$dtlsData[0]);
					if($variable_status==2){
						for($j=0; $j<count($dtls_data); $j++){
							$exdata=explode("_",$dtls_data[$j]);
							$poNo=$exdata[0];
							$txtQcPassQty=$exdata[1];
							$txtRejectQty=$exdata[2];
							$txtRemark=$exdata[3];
							$subconBrkId=$exdata[4];
							$subconDtlsId=$exdata[5];
							$prodBrkID=$exdata[6];
							$recvid=$exdata[7];
							$buyerpoid=$exdata[8];
							if($prodBrkID!=0 && $prodBrkID!=''){
								$data_array5[$prodBrkID]=explode("*",("'".$txtQcPassQty."'*'".$txtRejectQty."'*'".$txtRemark."'"));
								$hdn_break_id_arr[]	=$prodBrkID;
								//echo "10**".$prodBrkID.'==';
							} else {
								if($txtQcPassQty!=0 && $txtQcPassQty!=''){
									if ($add_commadtls!=0) $data_array4 .=",";
									$data_array4.="(".$id3.",".str_replace("'",'',$$hdnDtlsUpdateId).",'".$txtQcPassQty."','".$txtRejectQty."','".$txtRemark."','".$poNo."','".$subconDtlsId."','".$subconBrkId."','".$buyerpoid."','".$hid_order_id."','".$hid_job_id."',".$$hdnjobDtlsId.",'".$hid_recv_id."')";
									$id3=$id3+1; $add_commadtls++;
								}
							}
						}
					}
				}
			} else {
				if ($add_commaa!=0) $data_array3 .=","; $add_comma=0;
				$data_array3 .="(".$id1.",".$update_id.",".$$cboMachineName.",".$$txtItem.",".$$txtgmtscolorId.",".$$txtgmtssizeId.",".$$txtcolorID.",".$$txtsizeID.",".$$cboUom.",".$$txtImpression.",".$$cborawColor.",".$$txtQtyReel.",".$$txtTotalHead.",".str_replace(",",'',$$txtQcPassQty).",".str_replace(",",'',$$txtProdQty).",".$$txtCompProd.",".$$txtRejectQty.",".$$cboProdTime.",".$$txtRemarks.",".$$hdnbookingDtlsId.",".$$hdnRcvDtlsId.",".$$hdnjobDtlsId.",".$$hdnBreakIDs.",'".$variable_status."','".$user_id."','".$pc_date_time."')";
				$add_commaa++;
				//$hdn_dtls_id_arr[]=str_replace("'",'',$$hdnDtlsUpdateId);
				//echo "10**".str_replace("'",'',$$hdnDtlsdata);
				$dtlsData=explode("*******",str_replace("'",'',$$hdnDtlsdata));
				$dtls_data=explode("***",$dtlsData[0]);
				if($variable_status==2){
					for($j=0; $j<count($dtls_data); $j++){
						$exdata=explode("_",$dtls_data[$j]);
						$poNo=$exdata[0];
						$txtQcPassQty=$exdata[1];
						$txtRejectQty=$exdata[2];
						$txtRemark=$exdata[3];
						$subconBrkId=$exdata[4];
						$subconDtlsId=$exdata[5];
						$prodBrkID=$exdata[6];
						$recvid=$exdata[7];
						$buyerpoid=$exdata[8];
						if($prodBrkID==0 || $prodBrkID==''){
							if ($add_commadtls!=0) $data_array4 .=",";
							$data_array4.="(".$id3.",".$id1.",'".$txtQcPassQty."','".$txtRejectQty."','".$txtRemark."','".$poNo."','".$subconDtlsId."','".$subconBrkId."','".$buyerpoid."','".$hid_order_id."','".$hid_job_id."',".$$hdnjobDtlsId.",'".$hid_recv_id."')";
							$id3=$id3+1; $add_commadtls++;
						}
					}
				}
				$id1=$id1+1;
			}
		}
		//echo "10**".$data_array4; die;
		//echo "10**".bulk_update_sql_statement( "trims_production_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr); die;
		$rID=sql_update("trims_production_mst",$field_array,$data_array,"id",$update_id,0);  
		if($rID) $flag=1; else $flag=0;

		if($data_array2!=""){
			//echo bulk_update_sql_statement( "trims_production_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr); die;
			$rID2=execute_query(bulk_update_sql_statement( "trims_production_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr),1);
			if($rID2) $flag=1; else $flag=0;
		}

		if($data_array3!=""){
			//echo "10**INSERT INTO trims_production_dtls (".$field_array3.") VALUES ".$data_array3; die;
			$rID3=sql_insert("trims_production_dtls",$field_array3,$data_array3,1);
			if($rID3) $flag=1; else $flag=0;
		}

		if($variable_status==2){
			if($data_array4!=""  && $flag==1){
				//echo "10**INSERT INTO trims_prod_order_breakdown (".$field_array4.") VALUES ".$data_array4; die;
				$rID4=sql_insert("trims_prod_order_breakdown",$field_array4,$data_array4,1);
				if($rID4) $flag=1; else $flag=0;		
			}
		
			if($data_array5!=""  && $flag==1){
				//echo "10**".$variable_status; die;
				//echo bulk_update_sql_statement( "trims_prod_order_breakdown", "id",$field_array5,$data_array5,$hdn_break_id_arr); die;
				$rID5=execute_query(bulk_update_sql_statement( "trims_prod_order_breakdown", "id",$field_array5,$data_array5,$hdn_break_id_arr),1);
				if($rID5) $flag=1; else $flag=0;
			}
			//echo "10**".print_r($data_array5); die;
		}
		//echo bulk_update_sql_statement( "trims_prod_order_breakdown", "id",$field_array5,$data_array5,$hdn_break_id_arr); die;
		//1=1==1=1 1=1=1=0=
		//echo "10**".$rID.'='.$rID2.'='.$rID3.'='.$rID4.'='.$rID5.'='.$variable_status; die;
		//if($rID4) $flag=1; else $flag=0;
		if($db_type==0){
			if($flag==1){
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txt_production_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$hid_recv_id)."**".str_replace("'",'',$hid_job_id);
			} else {
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_production_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$hid_recv_id)."**".str_replace("'",'',$hid_job_id);
			}
		} else if($db_type==2) {  
			if($flag==1) {
				oci_commit($con);
				echo "1**".str_replace("'",'',$txt_production_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$hid_recv_id)."**".str_replace("'",'',$hid_job_id);
			} else {
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_production_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$hid_recv_id)."**".str_replace("'",'',$hid_job_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // delete here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");  
		}
			
		$mrrsql= sql_select("select  a.id,a.trims_del,b.order_no from  trims_delivery_mst a,trims_delivery_dtls b where a.id=b.mst_id and b.order_no='$txt_order_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.trims_del,b.order_no");
		$mrr_data=array();
		foreach($mrrsql as $row)
		{
			$all_recv_trans_id.=$row[csf('trims_del')].",";
		}
		$all_recv_trans_id=chop($all_recv_trans_id,",");
		$all_trims_del_id_count=count($mrrsql)	;
		//echo "10**".$all_trims_del_id_count; die;
		if($all_trims_del_id_count)
		{
			if($all_trims_del_id_count>0)
			{
				echo "18**Delete restricted, This Information is used in another Table.".$do_rcv_number=str_replace("'","",$all_recv_trans_id); disconnect($con); oci_rollback($con); die;
			}
		}
		
			for($i=1; $i<=$total_row; $i++)
			{	
				$hdnRcvDtlsId 			= "hdnRcvDtlsId_".$i;
				$hdnjobDtlsId 			= "hdnjobDtlsId_".$i;
				$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
				$hdnBreakIDs 			= "hdnBreakIDs_".$i;
				$hdnDtlsdata 			= "hdnDtlsdata_".$i;
				$dtlsData=explode("*******",str_replace("'",'',$$hdnDtlsdata));
				$dtls_data=explode("***",$dtlsData[0]);
					
						for($j=0; $j<count($dtls_data); $j++)
						{
							$exdata=explode("_",$dtls_data[$j]);
							$prodBrkID=$exdata[6];
							if($prodBrkID!=0 && $prodBrkID!='')
							{
								$break_data_arr[str_replace("'",'',$prodBrkID)]=explode("*",("0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
								$hdnBrkIdArr[]	=$prodBrkID;
								//echo "10**".$prodBrkID.'==';
							} 
						}
						
			}
			
			$field_array="status_active*is_deleted*updated_by*update_date";
			$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			$rID=sql_update("trims_production_mst",$field_array,$data_array,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; 
			$rID1=sql_update("trims_production_dtls",$field_array,$data_array,"mst_id",$update_id,1); 
			if($rID1) $flag=1; else $flag=0;
			
			if($variable_status==2)
			{
				if($field_array!="")
				{
					//echo "10**".bulk_update_sql_statement( "trims_prod_order_breakdown", "id",$field_array,$break_data_arr,$hdnBrkIdArr);
					$rID5=execute_query(bulk_update_sql_statement( "trims_prod_order_breakdown", "id",$field_array,$break_data_arr,$hdnBrkIdArr),1);
					if($rID5) $flag=1; else $flag=0;
				}
			}
			//if($field_array!="")
			//{
				//echo "10**".bulk_update_sql_statement( "trims_prod_order_breakdown", "id",$field_array,$break_data_arr,$hdnBrkIdArr);
				//$rID5=execute_query(bulk_update_sql_statement( "trims_prod_order_breakdown", "id",$field_array,$break_data_arr,$hdnBrkIdArr),1);
				//if($rID5) $flag=1; else $flag=0;
			//}
			//echo "10**".$rID."**".$rID1."**".$rID5."**".$flag; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txt_production_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$hid_recv_id)."**".str_replace("'",'',$hid_job_id);
			} else 
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_production_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$hid_recv_id)."**".str_replace("'",'',$hid_job_id);
			}
		} 
		else if($db_type==2)
		 {  
			if($flag==1) 
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$txt_production_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$hid_recv_id)."**".str_replace("'",'',$hid_job_id);
			} 
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_production_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$hid_recv_id)."**".str_replace("'",'',$hid_job_id);
			}
		}
		disconnect($con);
		die;
	}
}

function sql_updates($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit)
{

	$strQuery = "UPDATE ".$strTable." SET ";
	$arrUpdateFields=explode("*",$arrUpdateFields);
	$arrUpdateValues=explode("*",$arrUpdateValues);

	if(count($arrUpdateFields)!=count($arrUpdateValues)){
		return "0";
	}

	if(is_array($arrUpdateFields))
	{
		$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
		$Arraysize = count($arrayUpdate);
		$i = 1;
		foreach($arrayUpdate as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value;
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrUpdateFields."=".$arrUpdateValues;
	}
	$strQuery .=" WHERE ";

	$arrRefFields=explode("*",$arrRefFields);
	$arrRefValues=explode("*",$arrRefValues);
	if(is_array($arrRefFields))
	{
		$arrayRef = array_combine($arrRefFields,$arrRefValues);
		$Arraysize = count($arrayRef);
		$i = 1;
		foreach($arrayRef as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value." AND ":$key."=".$value."";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrRefFields."=".$arrRefValues."";
	}

	global $con;
	if( strpos($strQuery, "WHERE")==false)  return "0";
	echo "10**".$strQuery; die;
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	if ($exestd)
		return "1";
	else
		return "0";

	die;
	if ( $commit==1 )
	{
		if (!oci_error($stid))
		{
			oci_commit($con);
			return "1";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else
		return 1;
	die;
}


if ($action=="work_order_popup")
{
	echo load_html_head_contents("Job Popup Info","../../../", 1, 1, $unicode,'','');
	?>
	<script>
		/*function js_set_value(id)
		{ 
			$("#hidden_mst_id").val(id);
			document.getElementById('selected_job').value=id;
			parent.emailwindow.hide();
		}*/

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		} 
		var selected_id = new Array;
		var selected_id_dtls = new Array;
		var selected_id_break = new Array;
		var selected_qty = new Array;
		function js_set_value(str) 
		{  // alert(str);
			var subcon_job = $('#hidden_subcon_job'+str).val();
			var dtls_id = $('#hidden_dtls_id'+str).val();
			var breaks_id = $('#hidden_breaks_id'+str).val();
			var hidden_qty = $('#hidden_qty'+str).val();
			alert(hidden_qty);
			/*var buyer = $('#hidden_buyer'+str).val();
			var lcsc = $('#hidden_lc_sc'+str).val();
			var ready_post = $('#hidden_ready_to_post'+str).val();
			var negoBank = $('#hidden_bank'+str).val();*/
			
			//ls sc mix check-------------------------------//
			
			/*if(ready_post==2)
			{
				alert(" Make inoviec Ready for accounts ''Yes'' First");
				return;
			}
			
			if(lcScArr.length==0)
			{
				lcScArr.push( lcsc );
			}
			else if( jQuery.inArray( lcsc, lcScArr )==-1 &&  lcScArr.length>0)
			{
				alert("LC or SC Mixed is Not Allow");
				return;
			}
			
			//currency mix check-------------------------------//
			if(currencyArr.length==0)
			{
				currencyArr.push( currency );
			}
			else if( jQuery.inArray( currency, currencyArr )==-1 &&  currencyArr.length>0)
			{
				alert("Currency Mixed is Not Allow");
				return;
			}
			
			//buyer mix check--------------------------------//
			if(buyerArr.length==0)
			{
				buyerArr.push( buyer );
				//alert(buyer);
			}
			else if( jQuery.inArray( buyer, buyerArr )==-1 &&  buyerArr.length>0)
			{
				alert("Buyer Mixed is Not Allow");
				return;
			}
			
			if(bankArr.length==0)
			{
				bankArr.push( negoBank );
			}
			else if( jQuery.inArray( negoBank, bankArr )==-1 &&  bankArr.length>0)
			{
				alert("Nego Bank Mixed is Not Allow");
				return;
			}*/

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( subcon_job, selected_id ) == -1 ) {
				selected_id.push( subcon_job );
				selected_id_dtls.push( dtls_id );
				selected_id_break.push( breaks_id );
				selected_qty.push( hidden_qty );

			}
			else 
			{
				for( var i = 0; i < selected_id.length; i++ ) 
				{
					if( selected_id[i] == subcon_job ) break;
				}
				selected_id.splice( i, 1 );
				selected_id_dtls.splice( i, 1 );
				selected_id_break.splice( i, 1 );
				selected_qty.splice( i, 1 );
			}
			var id ='';  var id_dtls = ''; var id_break = ''; var qty = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				id_dtls += selected_id_dtls[i] + '_';
				id_break += selected_id_break[i] + '_';
				qty += selected_qty[i];
				alert(qty);
				/*if(selected_id_dtls[i]>0)
				{
					id_dtls += selected_id_dtls[i] + '_';
				}
				if(selected_id_break[i]>0)
				{
					id_break += selected_id_break[i] + '_';
				}*/
			}
			id 		= id.substr( 0, id.length - 1 );
			if(id_dtls!=""&& id_dtls !=null) id_dtls= id_dtls.substr( 0, id_dtls.length - 1 );
			if(id_break!=""&& id_break !=null) id_break= id_break.substr( 0, id_break.length - 1 );
			//if(qty!=""&& qty !=null) qty= qty.substr( 0, qty.length - 1 );
			//alert(qty);
			$('#all_subcon_job').val( id );	
			$('#all_sub_dtls_id').val( id_dtls );
			$('#all_sub_break_id').val( id_break );
			$('#total_order_qty').val( qty );

		}
			
		function fnc_load_party_popup(type,within_group)
		{
			var company = $('#cbo_company_name').val();
			var party_name = $('#cbo_party_name').val();
			var location_name = $('#cbo_location_name').val();
			load_drop_down( 'trims_production_entry_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0)
			{
				$('#search_by_td').html('System ID');
			}
			else if(val==2)
			{
				$('#search_by_td').html('W/O No');
			}
			else if(val==3)
			{
				$('#search_by_td').html('Buyer Job');
			}
			else if(val==4)
			{
				$('#search_by_td').html('Buyer Po');
			}
			else if(val==5)
			{
				$('#search_by_td').html('Buyer Style');
			}
		}
	</script>
</head>
<body>
<div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="940" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead> 
                <tr>
                    <th colspan="8"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>               	 
                    <th width="140" class="must_entry_caption">Company Name</th>
                    <th width="100">Within Group</th>                           
                    <th width="140">Party Name</th>
                    <th width="100">Search By</th>
                    <th width="100" id="search_by_td">System ID</th>
                    <th width="100">Year</th>
                    <th width="170">Date Range</th>                            
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                </tr>           
            </thead>
            <tbody>
                <tr class="general">
                    <td><input type="hidden" id="selected_job"><? $data=explode("_",$data); ?>  <!--  echo $data;-->
                        <? 
                        echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "fnc_load_party_popup(1,document.getElementById('cbo_within_group').value);",1); ?>
                    </td>
                    <td>
                        <?php echo create_drop_down( "cbo_within_group", 100, $yes_no,"", 0, "--  --", $data[3], "fnc_load_party_popup(1,this.value);" ); ?>
                    </td>
                    <td id="buyer_td">
                        <? echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );   	 
                        ?>
                    </td>
                    <td>
						<?
                            $search_by_arr=array(1=>"System ID",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
                            echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                        ?>
                    </td>
                    <td align="center">
                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                    </td>
                    <td align="center"><? echo create_drop_down( "cbo_year_selection", 100, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value+'_'+<? echo $data[4]; ?>, 'create_order_search_list_view', 'search_div', 'trims_production_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center" valign="middle">
                            <? echo load_month_buttons();  ?>
                            <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                             
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center" valign="top" id=""><div id="search_div"></div></td>
                    </tr>
                </tbody>
            </table>    
            </form>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}
	
if($action=="create_order_search_list_view")
{	
	$data=explode('_',$data);
	$party_id=str_replace("'","",$data[1]);
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	$within_group =$data[7];
	$section_id =$data[9];
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[8]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";}

	if($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if($section_id!='') $section=" and section=$section_id"; else { $section=''; }
	//echo $search_type; die;
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $po_cond=" and b.po_number = '$search_str' ";
			else if ($search_by==5) $style_cond=" and a.style_ref_no = '$search_str' ";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str%'"; 
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str%'";   
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '$search_str%'";
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '$search_str%'";  
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str'";
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str'";  
		}
	}	

	if($party_id!=0) $party_id_cond=" and a.party_id='$party_id'"; else $party_id_cond="";

	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $order_rcv_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $order_rcv_date ="";
	}
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	if($within_group==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}
	
	$po_ids='';
	
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
	if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
	}
	//echo $po_ids;
	if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$buyer_po_arr=array();
	if($within_group==1)
	{
		$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
			$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		}
		unset($po_sql_res);
	}
	$buyer_po_id_str=""; $buyer_po_no_str=""; $buyer_po_style_str="";
	if($db_type==0) 
	{
		$ins_year_cond="year(a.insert_date)";
		$color_id_str=",group_concat(c.color_id) as color_id";
		if($within_group==1)
		{
			$buyer_po_id_str=",group_concat(b.buyer_po_id) as buyer_po_id";
		}
		else
		{
			$buyer_po_no_str=",group_concat(b.buyer_po_no) as buyer_po_id";
			$buyer_po_style_str=",group_concat(b.buyer_style_ref) as buyer_style";
		}
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		$color_id_str=",listagg(c.color_id,',') within group (order by c.color_id) as color_id";
		
		if($within_group==1)
		{
			$buyer_po_id_str=",listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)  as buyer_po_id";
		}
		else
		{
			$buyer_po_no_str=",listagg(b.buyer_po_no,',') within group (order by b.id) as buyer_po_no";
			$buyer_po_style_str=",listagg(b.buyer_style_ref,',') within group (order by b.id) as buyer_style";
		}
	}
	 /*$sql= "select a.id, a.subcon_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date ,b.section ,b.item_group $color_id_str $buyer_po_id_str $buyer_po_no_str $buyer_po_style_str
	 from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
	 where a.entry_form=255 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond $withinGroup and b.id=c.mst_id  
	 group by a.id, a.subcon_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date
	 order by a.id DESC";*/

	 $sql= "select a.id, a.subcon_job, a.job_no_prefix_num, a.company_id, a.location_id, a.party_id, a.receive_date, a.delivery_date ,a.order_no,b.section ,b.item_group, c.description, listagg(b.id,',') within group (order by b.id) as dtls_id, listagg(c.id,',') within group (order by c.id) as breaks_id, sum(c.qnty) as qnty
	 from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
	 where a.entry_form=255 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $order_rcv_date $company $buyer $withinGroup $search_com_cond $section and b.id=c.mst_id  
	 group by a.id, a.subcon_job, a.job_no_prefix_num, a.company_id, a.location_id, a.party_id, a.receive_date, a.delivery_date, a.order_no ,b.section ,b.item_group, c.description
	 order by a.id DESC";
	 //echo $sql;
	 $data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="885" >
        <thead>
            <th width="30">SL</th>
            <th width="110">Receive No</th>
            <th width="100">W/O No</th>
            <th width="80">Section</th>
            <th width="150">Item Group</th>
            <th width="150">Description</th>
            <th width="80">Quantity</th>
            <th width="80">Ord Receive Date</th>
            <th>Delivery Date</th>
        </thead>
        </table>
        <div style="width:885px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="865" class="rpt_table" id="tbl_po_list">
        <tbody>
            <? 
            $i=1;
            
            $group_arr=return_library_array( "select id, item_name from lib_item_group where status_active=1 and item_category=4", "id", "item_name");
            foreach($data_array as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				/*$excolor_id=array_unique(explode(",",$row[csf('color_id')]));
				$color_name="";	
				foreach ($excolor_id as $color_id)
				{
					if($color_name=="") $color_name=$color_arr[$color_id]; else $color_name.=','.$color_arr[$color_id];
				}
				if($within_group==1)
				{
					$buyer_po=""; $buyer_style="";
					$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
					foreach($buyer_po_id as $po_id)
					{
						if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
						if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
					}
					$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
					$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				}
				else
				{
					$buyer_po=$row[csf('buyer_po_no')];
					$buyer_style=$row[csf('buyer_style')];
				}*/
                
                	if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
					?>	
					<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $i;?>)" > 
                    <td width="30"><? echo $i; ?></td>
                    <td width="110" align="center"><? echo $row[csf('subcon_job')]; ?></td>
                    <td width="100"><? echo $row[csf('order_no')]; ?></td>
                    <td width="80"><? echo $trims_section[$row[csf('section')]]; ?></td>
                    <td width="150"><? echo $group_arr[$row[csf('item_group')]]; ?></td>
                    <td width="150" style="word-break:break-all" ><? echo $row[csf('description')]; ?></td>
                    <td width="80"><? echo $row[csf('qnty')]; ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                    <td style="text-align:center;"><? echo change_date_format($row[csf('delivery_date')]); ?>
                    <input type="hidden" name="hidden_subcon_job<? echo $i; ?>" id="hidden_subcon_job<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>" style="width:70px">
                    <input type="hidden" name="hidden_dtls_id<? echo $i; ?>" id="hidden_dtls_id<? echo $i; ?>" value="<? echo $row[csf('dtls_id')]; ?>" style="width:70px">
                    <input type="hidden" name="hidden_breaks_id<? echo $i; ?>" id="hidden_breaks_id<? echo $i; ?>" value="<? echo $row[csf('breaks_id')]; ?>" style="width:70px">
                    <input type="hidden" name="hidden_qty<? echo $i; ?>" id="hidden_qty<? echo $i; ?>" value="<? echo $row[csf('qnty')]; ?>" style="width:70px">	

                    </td>	
                </tr>
				<? 
                $i++; 
            } 
            ?>
        </tbody>
    </table><div style="width:40%; float:left" align="left">
    	<input type="button" class="formbutton" id="close" style="width:80px" onClick="parent.emailwindow.hide();" value="Close" />
    	<input type="hidden"  id="all_subcon_job" />
    	<input type="hidden"  id="all_sub_dtls_id" />
    	<input type="hidden"  id="all_sub_break_id" />
    	<input type="hidden"  id="total_order_qty" />
    </div>
	` 
	<?    
	exit();
}
 
if ($action=="load_php_data_to_form")
{
	//echo "select a.id, a.subcon_job, a.company_id, a.location_id, a.party_id, a.currency_id, a.party_location, a.delivery_date, a.within_group, a.party_location, a.order_id, a.order_no,b.section from subcon_ord_mst a ,subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.id in($data) and a.status_active=1 and b.status_active=1 group by  a.id, a.subcon_job, a.company_id, a.location_id, a.party_id, a.currency_id, a.party_location, a.delivery_date, a.within_group, a.party_location, a.order_id, a.order_no,b.section" ; 
	$nameArray=sql_select( "select a.id, a.subcon_job, a.company_id, a.location_id, a.party_id, a.currency_id, a.party_location, a.delivery_date, a.within_group, a.party_location, a.order_id, a.order_no,b.section from subcon_ord_mst a ,subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.id in($data) and a.status_active=1 and b.status_active=1 group by  a.id, a.subcon_job, a.company_id, a.location_id, a.party_id, a.currency_id, a.party_location, a.delivery_date, a.within_group, a.party_location, a.order_id, a.order_no,b.section" );
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_recv_no').value 				= '".$row[csf("subcon_job")]."';\n";
		echo "document.getElementById('hid_recv_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_company_name').value 		= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_within_group').value 		= '".$row[csf("within_group")]."';\n";  
		echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		echo "fnc_load_party(1,".$row[csf("within_group")].");\n";	
	//echo "load_drop_down( 'requires/trims_production_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";	
		echo "document.getElementById('cbo_location_name').value 		= '".$row[csf("location_id")]."';\n";
	//echo "load_drop_down( 'requires/trims_production_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_buyer', 'buyer_td' );\n";
		echo "document.getElementById('cbo_party_name').value			= '".$row[csf("party_id")]."';\n";
		//cbo_company_name cbo_location_name cbo_within_group cbo_party_name cbo_party_location txt_delivery_date cbo_section txt_order_no txt_order_qty
		echo "fnc_load_party(2,".$row[csf("within_group")].");\n";	 
		echo "document.getElementById('cbo_party_location').value		= '".$row[csf("party_location")]."';\n";	
		echo "document.getElementById('txt_delivery_date').value		= '".change_date_format($row[csf("delivery_date")])."';\n";  
		echo "document.getElementById('hid_order_id').value          	= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('txt_order_no').value         	= '".$row[csf("order_no")]."';\n";
		echo "document.getElementById('cbo_section').value        		= '".$row[csf("section")]."';\n";
		//echo "document.getElementById('txt_order_qty').value         	= '".$row[csf("remarks")]."';\n";
		echo "$('#txt_order_no').attr('disabled','true')".";\n";
		echo "$('#cbo_within_group').attr('disabled','true')".";\n";
		echo "$('#cbo_party_name').attr('disabled','true')".";\n";
		//echo "document.getElementById('update_id').value          		= '".$row[csf("id")]."';\n";	
		//echo "set_button_status(1,'".$_SESSION['page_permission']."', 'fnc_production_entry',1);\n";	
	}
	exit();	
}

if ($action=="order_popup")
{	
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
	function js_set_value(id)
	{
		//alert(booking_no); 
		document.getElementById('hidd_booking_data').value=id;
		parent.emailwindow.hide();
	}

	function fnc_load_party_order_popup(company,party_name)
	{   	
		load_drop_down( 'trims_production_entry_controller', company+'_'+1+'_'+party_name, 'load_drop_down_buyer', 'buyer_td' );
		$('#cbo_party_name').attr('disabled',true);
	}
	
	function search_by(val,type)
	{
		if(type==1)
		{
			if(val==1 || val==0)
			{
				$('#txt_search_common').val('');
				$('#search_td').html('W/O No');
			}
			else if(val==2)
			{
				$('#txt_search_common').val('');
				$('#search_td').html('Job NO');
			}
			else if(val==3)
			{
				$('#txt_search_common').val('');
				$('#search_td').html('Style Ref.');
			}
			else if(val==4)
			{
				$('#txt_search_common').val('');
				$('#search_td').html('Buyer Po');
			}
		}
	}
</script>
</head>
<body onLoad="fnc_load_party_order_popup(<? echo $company;?>,<? echo $party_name;?>)">
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                <thead>
                    <tr>
                        <th colspan="7" align="center">
                            <? echo create_drop_down( "cbo_search_category", 110, $string_search_type,'', 1, "-- Search Catagory --" ); ?>
                        </th>
                    </tr>
                    <tr>                	 
                        <th width="150">Party Name</th>
                        <th width="80">Search Type</th>
                        <th width="100" id="search_td">W/O No</th>
                        <th width="60">W/O Year</th>
                        <th colspan="2" width="120">W/O Date Range</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                            <input type="hidden" id="hidd_booking_data">
                        </th>
                    </tr>                                 
                </thead>
                <tr class="general">
                    <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 150, $blank_array,"", 1, "-- Select Buyer --" ); ?></td>
                    <td>
                        <? 
                            $searchtype_arr=array(1=>"W/O No",2=>"Buyer Job",3=>"Buyer Style Ref.",4=>"Buyer Po");
                            echo create_drop_down( "cbo_search_type", 80, $searchtype_arr,"", 0, "", 1, "search_by(this.value,1)",0,"" );
                        ?>
                    </td>
                    <td><input name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:90px"></td>
                    <td><? echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                    <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From"></td>
                    <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To"></td> 
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_search_category').value+'_'+<? echo $company;?>+'_'+document.getElementById('cbo_search_type').value, 'create_booking_search_list_view', 'search_div', 'trims_production_entry_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                    <td colspan="7"align="center" height="30" valign="middle"><?  echo load_month_buttons(); ?></td>
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

if($action=="create_booking_search_list_view")
{	
	$data=explode('_',$data);
	$search_type=$data[7];
	
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Party First."; die; }
	//if ($data[0]!=0 && ) $buyer=" and buyer_id='$data[1]'"; else  $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[4]"; } else if($db_type==2) { $year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]"; }
	$master_company=$data[6];

	$woorder_cond=""; $job_cond=""; $style_cond=""; $po_cond="";
	if($data[5]==1)
	{
		if ($data[1]!="")
		{
			if ($search_type==1) $woorder_cond=" and a.booking_no = '$data[1]' ";
			if ($search_type==2) $job_cond=" and a.job_no_prefix_num = '$data[1]' ";
			if ($search_type==3) $style_cond=" and a.style_ref_no = '$data[1]' ";
			if ($search_type==4) $po_cond=" and b.po_number = '$data[1]' ";
		}
	}
	if($data[5]==2)
	{
		if ($data[1]!="")
		{
			if ($search_type==1) $woorder_cond=" and a.booking_no like '$data[1]%' ";
			if ($search_type==2) $job_cond=" and a.job_no_prefix_num like '$data[1]%' ";
			if ($search_type==3) $style_cond=" and a.style_ref_no like '$data[1]%' ";
			if ($search_type==4) $po_cond=" and b.po_number like '$data[1]%' ";
		}
	}
	if($data[5]==3)
	{
		if ($data[1]!="")
		{
			if ($search_type==1) $woorder_cond=" and a.booking_no like '%$data[1]' ";
			if ($search_type==2) $job_cond=" and a.job_no_prefix_num like '%$data[1]' ";
			if ($search_type==3) $style_cond=" and a.style_ref_no like '%$data[1]' ";
			if ($search_type==4) $po_cond=" and b.po_number like '%$data[1]' ";
		}	
	}
	if($data[5]==4 || $data[5]==0)
	{
		if ($data[1]!="")
		{
			if ($search_type==1) $woorder_cond=" and a.booking_no like '%$data[1]%' ";
			if ($search_type==2) $job_cond=" and a.job_no_prefix_num like '%$data[1]%' ";
			if ($search_type==3) $style_cond=" and a.style_ref_no like '%$data[1]%' ";
			if ($search_type==4) $po_cond=" and b.po_number like '%$data[1]%' ";
		}
	}
	
	$po_ids='';
	
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	if(($job_cond!="" && $search_type==2) || ($style_cond!="" && $search_type==3)|| ($po_cond!="" && $search_type==4))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
		if ($po_ids=="")
		{
			$po_idsCond="";
			echo "Not Found."; die;
		}
	}
	
	if ($po_ids!="") $po_idsCond=" and b.po_break_down_id in ($po_ids)"; else $po_idsCond="";
	$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
	}
	unset($po_sql_res);
	
	$pre_cost_trims_arr=array();
	$pre_sql ="Select id, emb_name, emb_type, body_part_id  from wo_pre_cost_embe_cost_dtls where status_active=1 and is_deleted=0";
	$pre_sql_res=sql_select($pre_sql);
	foreach ($pre_sql_res as $row)
	{
		$pre_cost_trims_arr[$row[csf("id")]]['emb_name']=$row[csf("emb_name")];
		$pre_cost_trims_arr[$row[csf("id")]]['emb_type']=$row[csf("emb_type")];
		$pre_cost_trims_arr[$row[csf("id")]]['body_part_id']=$row[csf("body_part_id")];
	}
	unset($pre_sql_res);
	
	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date = "and a.booking_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
		$wo_year="YEAR(a.insert_date)";
		$pre_cost_trims_cond="group_concat(b.pre_cost_fabric_cost_dtls_id)";
		$gmts_item_cond="group_concat(b.gmt_item)";
		$po_id_cond="group_concat(b.po_break_down_id)";
	}
	else if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date = "and a.booking_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
		$wo_year="to_char(a.insert_date,'YYYY')";
		$pre_cost_trims_cond="listagg(b.pre_cost_fabric_cost_dtls_id,',') within group (order by b.pre_cost_fabric_cost_dtls_id)";
		$gmts_item_cond="listagg(b.gmt_item,',') within group (order by b.gmt_item)";
		$po_id_cond="listagg(b.po_break_down_id,',') within group (order by b.po_break_down_id)";
	} 
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (0=>$comp,1=>$buyer_arr);
	//$sql= "select $wo_year as year, id, booking_type, booking_no, company_id, buyer_id, job_no, booking_date from  wo_booking_mst where booking_type=6 and status_active=1 and is_deleted=0 $booking_date $company $woorder_cond $year_cond order by booking_no"; 
	//$sql= "select $wo_year as year, a.id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, $pre_cost_trims_cond as pre_cost_trims_id, $gmts_item_cond as gmts_item, $po_id_cond as po_id from  wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=6 and a.status_active=1 and a.lock_another_process!=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $booking_date $company $woorder_cond $year_cond $po_idsCond group by a.insert_date, a.id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id order by a.id DESC";

	$sql= "select a.id, a.subcon_job, a.company_id, a.location_id, a.party_id, a.party_location, a.currency_id, a.receive_date, a.delivery_date,a.rec_start_date, a.order_id, a.order_no, a.exchange_rate, b.id, b.mst_id, b.order_id, b.order_no, b.order_uom,b.delivery_date, b.buyer_po_no, b.buyer_style_ref, b.buyer_buyer,  b.section, b.item_group  from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=255 group by a.id, a.subcon_job, a.company_id, a.location_id, a.party_id, a.party_location, a.currency_id, a.receive_date, a.delivery_date, a.rec_start_date, a.order_id, a.order_no, a.exchange_rate,b.id, b.mst_id, b.order_id, b.order_no, b.order_uom,b.delivery_date, b.buyer_po_no, b.buyer_style_ref, b.buyer_buyer, b.section, b.item_group";
	// a.subcon_job=job_no_mst a
	$data_array=sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="640" >
        <thead>
            <th width="30">SL</th>
            <th width="60">W/O Year</th>
            <th width="60">W/O No</th>
            <th width="70">W/O Date</th>
            <th width="100">Buyer</th>
            <th width="100">Buyer Po</th>
            <th width="100">Buyer Style</th>
            <th width="100">Buyer Job</th>
        </thead>
        </table>
        <div style="width:640px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="620" class="rpt_table" id="list_view">
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('booking_no')].'_'.$row[csf('currency_id')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60" align="center"><? echo $row[csf('year')]; ?></td>
                    <td width="60" align="center"><? echo $row[csf('booking_no_prefix_num')]; ?></td>
                    <td width="70"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_name; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $po_no; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_style; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_job; ?></td>
                </tr>
				<? 
                $i++; 
            } 
            ?>
        </tbody>
    </table>
	<?
	exit();
}

if ($action=="populate_data_from_search_popup")
{
	//echo $action."nazim"; die;
	$data=explode('_',$data);
	$nameArray=sql_select( "select id, booking_type, booking_no, company_id, buyer_id, job_no, booking_date,currency_id from  wo_booking_mst where booking_type=6 and status_active=1 and is_deleted=0 and id='$data[0]'" );
	//$sql= "select to_char(insert_date,'YYYY') as year, id, booking_type, booking_no, company_id, buyer_id, job_no, booking_date from  wo_booking_mst where booking_type=6 and status_active=1 and is_deleted=0 $booking_date $company $order_cond order by booking_no";
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_order_no').value 	= '".$row[csf("booking_no")]."';\n";  
		echo "document.getElementById('cbo_party_name').value 	= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('hid_order_id').value		= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_currency').value		= '".$row[csf("currency_id")]."';\n";
	}
	exit();	
}
if($action=="qc_qty_popup")
{
	//echo 11; 
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$desc=return_field_value("item_description"," trims_job_card_dtls","id='$jobDtlsId'");
	//$txtItemmmm=trim($txtItem);
	//$txtItemmmm=str_replace('<pre>', '', $txtItem);
	//$txtItemmmm=trim($txtItem);
	//$txtItemmmm=trim($txtItem,"\n");
	//$txtItemmmm=trim(preg_replace('/\s+/', '', $txtItem));
	//$txtItemmmm = preg_replace('/[\r\n]+/', "\n", $txtItem);
	//$txtItemmmm = trim(preg_replace('/\s\s+/', ' ', $txtItem));
	//echo  '='.preg_replace('/[ \t]+/', ' ', $txtItemmmm).'=';
	//echo  '='.$txtItemmmm.'=';

	$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
	$size_arr=return_library_array( "select id,size_name from  lib_size",'id','size_name');
	$item_group_arr=return_library_array( "select id,item_name from  lib_item_group",'id','item_name');
    ?>
    <script>
	
	function fnc_close()
	{
		var tot_row=$('#tbl_share_details_entry tbody tr').length;
		var data_break_down="";
		for(var i=1; i<=tot_row; i++)
		{
			/*if (form_validation('txtQcPassQty_'+i,'QC. Pass Qty')==false)
			{
				return;
			}*/
			//alert($("#txtsize_"+i).val());
			if($("#txtQcPassQty_"+i).val()=="") $("#txtQcPassQty_"+i).val(0)
			if($("#txtRejectQty_"+i).val()=="") $("#txtRejectQty_"+i).val(0);
			if($("#txtRemark_"+i).val()=="") $("#txtRemark_"+i).val('');
			if($("#subconBrkId_"+i).val()=="") $("#subconBrkId_"+i).val(0);
			if($("#subconDtlsId_"+i).val()=="") $("#subconDtlsId_"+i).val(0);
			if($("#poNo_"+i).val()=="") $("#poNo_"+i).val('');
			if($("#prodBrkID_"+i).val()=="") $("#prodBrkID_"+i).val(0);
			if($("#recvid_"+i).val()=="") $("#recvid_"+i).val(0);
			if($("#buyerpoid_"+i).val()=="") $("#buyerpoid_"+i).val(0);
			//alert($('#txtQcPassQty_'+i).val());
			if(data_break_down==""){
				data_break_down+=$('#poNo_'+i).val()+'_'+$('#txtQcPassQty_'+i).val()+'_'+$('#txtRejectQty_'+i).val()+'_'+$('#txtRemark_'+i).val()+'_'+$('#subconBrkId_'+i).val()+'_'+$('#subconDtlsId_'+i).val()+'_'+$('#prodBrkID_'+i).val()+'_'+$('#recvid_'+i).val()+'_'+$('#buyerpoid_'+i).val();
			}else{
				data_break_down+="***"+$('#poNo_'+i).val()+'_'+$('#txtQcPassQty_'+i).val()+'_'+$('#txtRejectQty_'+i).val()+'_'+$('#txtRemark_'+i).val()+'_'+$('#subconBrkId_'+i).val()+'_'+$('#subconDtlsId_'+i).val()+'_'+$('#prodBrkID_'+i).val()+'_'+$('#recvid_'+i).val()+'_'+$('#buyerpoid_'+i).val();
			}
		}
		
		data_break_down+="*******"+$('#txt_total_Qcpass_qnty').val()+'_'+$('#txt_total_reject_qnty').val()+'_'+$('#txt_total_Prod_qnty').html()+'_'+$('#txt_total_Comp_qnty').html()+'_'+$('#txt_total_Bal_qnty').html();
		//alert(data_break_down);
		$('#hidden_break_tot_row').val( data_break_down );
		//alert(tot_row);//return;
		parent.emailwindow.hide();
	}

	function sum_total_qnty(status,rowId,sumId,rowNum)
	{
		//alert(status);
		//hdnAllowdPercent_
		
		var ddd={ dec_type:5, comma:0, currency:''};
		 var prodQty=0; var balQty=0; var jobQtyWithOver=0;
		var tot_row=$('#tbl_share_details_entry tbody tr').length;
		if (status==1) 
		{
			math_operation( 'txt_total_Qcpass_qnty', 'txtQcPassQty_', "+", tot_row,ddd );
			math_operation( 'txt_total_reject_qnty', 'txtRejectQty_', "+", tot_row,ddd );
			var ordQty=0; var jobQty=0; var cumProdQty=0; 
			for(var i=1; i<=tot_row; i++)
			{
				ordQty 	+= $('#txtOrdQty_'+i).html()*1;
				jobQty	+= $('#txtjobQty_'+i).html()*1;

				allowdPercent=$('#hdnAllowdPercent_'+i).val()*1;
				if(allowdPercent!=0 || allowdPercent!=''){
					jobQtyWithOver =((($('#txtjobQty_'+i).html()*1)* (allowdPercent))/100)+($('#txtjobQty_'+i).html()*1);
				}
				else{
					jobQtyWithOver=$('#txtjobQty_'+i).html()*1;
				}
				

				$('#txtProdQty_'+i).html( ($("#txtQcPassQty_"+i).val()*1)+($("#txtRejectQty_"+i).val()*1) );
				//$('#txtBalQty_'+i).html( (($("#txtjobQty_"+i).html()*1)-(($("#txtQcPassQty_"+i).val()*1)+($("#txtRejectQty_"+i).val()*1)))-$('#txtCumProdQty_'+i).html()*1 );
				$('#txtBalQty_'+i).html( ((jobQtyWithOver)-($("#txtQcPassQty_"+i).val()*1))-$('#txtCumProdQty_'+i).html()*1 );
				//$('#txtBalQty_'+i).html( (($("#txtjobQty_"+i).html()*1)-$("#txtQcPassQty_"+i).val()*1)-$('#txtCumProdQty_'+i).html()*1 );
				prodQty 	+= $('#txtProdQty_'+i).html()*1;
				cumProdQty 	+= $('#txtCumProdQty_'+i).html()*1;
				balQty		+= $('#txtBalQty_'+i).html()*1;
			}
			$('#txtTotOrdQty').html( ordQty.toFixed(4) );
			$('#txtjobOrdQty').html( jobQty.toFixed(4) );
			$('#txt_total_Prod_qnty').html( prodQty.toFixed(4) );
			$('#txt_total_Comp_qnty').html( cumProdQty.toFixed(4) );
			$('#txt_total_Bal_qnty').html( balQty.toFixed(4) );
		}
		else
		{
			//jobQtyWithOver=(("#txtjobQty_"+rowNum).html()*1*("#hdnAllowdPercent_"+rowNum).val()*1 )/100;

			allowdPercent=$('#hdnAllowdPercent_'+rowNum).val()*1;
			if(allowdPercent!=0 || allowdPercent!=''){
				jobQtyWithOver =((($('#txtjobQty_'+rowNum).html()*1)* (allowdPercent))/100)+($('#txtjobQty_'+rowNum).html()*1);
			}
			else{
				jobQtyWithOver=$('#txtjobQty_'+rowNum).html()*1;
			}

			


			prodQty=($("#txtQcPassQty_"+rowNum).val()*1)+($("#txtRejectQty_"+rowNum).val()*1);
			//var balQty=(($("#txtjobQty_"+rowNum).html()*1)-(($("#txtQcPassQty_"+rowNum).val()*1)+($("#txtRejectQty_"+rowNum).val()*1))-$('#txtCumProdQty_'+rowNum).html()*1);
			balQty=(jobQtyWithOver-($("#txtQcPassQty_"+rowNum).val()*1)-$('#txtCumProdQty_'+rowNum).html()*1);
			//var balQty=(($("#txtjobQty_"+rowNum).html()*1)-($("#txtQcPassQty_"+rowNum).val()*1)-$('#txtCumProdQty_'+rowNum).html()*1);
			var qc_rej_balQty=(jobQtyWithOver-(($("#txtQcPassQty_"+rowNum).val()*1)+($("#txtRejectQty_"+rowNum).val()*1))-$('#txtCumProdQty_'+rowNum).html()*1);
			
			//alert(balQty);
			if(qc_rej_balQty<0){
				alert("Production Qty can't exceed Production Balance Qty");
				$('#txtProdQty_'+rowNum).html('');
				$('#txtQcPassQty_'+rowNum).val('');
				$('#txtRejectQty_'+rowNum).val('');
			}else{
				$('#txtProdQty_'+rowNum).html( prodQty.toFixed(4) );
				$('#txtBalQty_'+rowNum).html( balQty.toFixed(4) );
			}
			var totalProdQty=0; var totalBalQty=0;var totalCumProdQty=0;
			for(var i=1; i<=tot_row; i++)
			{
				var pQty= $('#txtProdQty_'+i).html()*1
				var cumQty= $('#txtCumProdQty_'+i).html()*1
				var balQty= $('#txtBalQty_'+i).html()*1
				if(pQty!='' && pQty>0){
					totalProdQty +=pQty;
				}
				if(cumQty!='' && cumQty>0){
					totalCumProdQty +=cumQty;
				}
				if(balQty!='' && balQty>0){
					totalBalQty +=balQty;
				}
			}
			math_operation( sumId, rowId, "+", tot_row,ddd );
			$('#txt_total_Prod_qnty').html( totalProdQty.toFixed(4) );
			$('#txt_total_Comp_qnty').html( totalCumProdQty.toFixed(4) );
			$('#txt_total_Bal_qnty').html( totalBalQty.toFixed(4) );
		}
	}
		
	function copy_values()
	{
		var copy_val=document.getElementById('copy_basis').checked;
		var row_num=$('#tbl_share_details_entry tbody tr').length;
		if(copy_val==true){
			for (var j=1; j<=row_num; j++)
			{
				var txtOrdQty 		= $('#txtjobQty_'+j).html()*1;
				var txtQcBalQty 	= $('#txtBalQty_'+j).html()*1;
				$('#txtQcPassQty_'+j).val((txtQcBalQty).toFixed(4));
				sum_total_qnty(1,1,1,1);
			}
		}else{
			for (var j=1; j<=row_num; j++)
			{
				var txtOrdQty 		= $('#txtjobQty_'+j).html()*1;
				$('#txtQcPassQty_'+j).val('');
				sum_total_qnty(1,1,1,1);
			}
		}
		//var copy_basis=$('input[name="copy_basis"]:checked').val()
	}
	/*function cal_values(rowNo)
	{
		var balance='';
		var txtOrdQty 		= $('#txtOrdQty_'+rowNo).html()*1;
		var txtjobQty 		= $('#txtjobQty_'+rowNo).html()*1;
		var txtCumProdQty	= $('#txtCumProdQty_'+rowNo).html()*1;
		var txtBalQty		= $('#txtBalQty_'+rowNo).html()*1;
		var txtQcPassQty 	= $('#txtQcPassQty_'+rowNo).val()*1;
		var txtRejectQty 	= $('#txtRejectQty_'+rowNo).val()*1;
		
		$('#txtProdQty_'+rowNo).html( ($("#txtQcPassQty_"+rowNo).val()*1)+($("#txtRejectQty_"+rowNo).val()*1) );
		//$('#txtBalQty_'+rowNo).html( ($("#txtjobQty_"+rowNo).html()*1)-(($("#txtQcPassQty_"+rowNo).val()*1)+($("#txtRejectQty_"+rowNo).val()*1)) );
		$('#txtBalQty_'+rowNo).html( (($("#txtjobQty_"+rowNo).html()*1)-(($("#txtQcPassQty_"+rowNo).val()*1)+($("#txtRejectQty_"+rowNo).val()*1))-$('#txtCumProdQty_'+rowNo).html()*1) );
		
		if(txtBalQty<0)
		{
			alert("Production Qty can't exceed Production Balance Qty");
			$('#txtProdQty_'+rowNo).val('');
			$('#txtQcPassQty_'+rowNo).val('');
			//$('#txtBalQty_'+rowNo).val((hidBalQty).toFixed(4));

			//$('#txtCompProd_'+rowNo).val((txtOrdQty-hidBalQty).toFixed(4));
			return;
		}
		else
		{
			$('#txtBalQty_'+rowNo).val((balance).toFixed(4));
			$('#txtCompProd_'+rowNo).val((hidCompProd+txtQcPassQty).toFixed(4));
		}
		var txtCompProd 	= $('#txtCompProd_'+rowNo).val()*1;
		if(hdnDtlsUpdateId!='')
		{//alert(txtCompProd+'='+hdnCumDelQty);
			if(txtCompProd<hdnCumDelQty) 
			{
				alert("Cumulative Production Qty can't Less Cumulative Delivery Qty");
				$('#txtProdQty_'+rowNo).val('');
				$('#txtQcPassQty_'+rowNo).val('');
				$('#txtRejectQty_'+rowNo).val('');
				$('#txtCompProd_'+rowNo).val((hidCompProd).toFixed(4));
				return;
				//cal_values(rowNo);
			}
		}
	}
	*/

	</script>
</head>
<body>
	<div align="center" style="width:100%;" >
		<form name="qntypopup_1"  id="qntypopup_1" autocomplete="off">
			<table class="rpt_table" width="1100px" cellspacing="0" cellpadding="0" rules="all" id="tbl_share_details_entry">
				<thead>
                	<th width="80">PO NO</th>
					<? 
					if($within_group==2){
						?>
						 <th width="60">Trims Ref</th>
						<?
					}else{
						?>
						 <th width="60">Internal Ref.</th>
						<?
					}?>
                    <th width="120">Buyers Style Ref.</th>
                    <th width="60">Delv Date</th>
                    <th width="140">Item Group</th>				                  
                    <th width="60">Order UOM</th>
					<th width="70">WO Qty</th>
                    <th width="50">BKD UOM</th>
					<th width="80">Job Qty</th>
					<th width="80" >QC. Pass Qty<input id="copy_basis" name="copy_basis" type="checkbox" onChange="copy_values()" /></th>
                    <th width="80">Reject Qty</th>
                    <th width="80">Current Prod. Qty.</th>
                    <th width="80">Cum. Prod. Qty</th>
                    <th width="80">Balance</th>
                    <th>Remarks</th>
				</thead>
				<tbody>
					<input type="hidden" name="txtDeletedId" id="txtDeletedId" class="text_boxes_numeric" style="width:90px" readonly />
					<input type="hidden" name="hidden_break_tot_row" id="hidden_break_tot_row" class="text_boxes" style="width:90px" />
					<?
					//echo $data_break.'=='; 
					if($data_break!='')
					{
						$data_break=explode('*******',$data_break);
						$breakDtls=explode('***',$data_break[0]);
						$poData=array();
						for($i=0;$i<count($breakDtls);$i++)
						{
							$breakDatas=explode('_',$breakDtls[$i]);
							for($j=0;$j<count($breakDatas);$j++)
							{
								//echo $breakDatas[$j]."==";
								$poData[$breakDatas[0]]['qcpass']=$breakDatas[1];
								$poData[$breakDatas[0]]['reject']=$breakDatas[2];
								$poData[$breakDatas[0]]['remark']=$breakDatas[3];
								$poData[$breakDatas[0]]['currprodqty']=$breakDatas[1]+$breakDatas[2];
							}
						}
					}
					/*echo "<pre>";
					print_r($poData);*/
					
					// $cumelative_sql ="select id, mst_id, order_id, buyer_po_no, buyer_po_id, order_no,job_id, curr_prod_qty, job_dtls_id, break_dtls_id, booking_dtls_id, receive_id,receive_details_id, qc_pass_qty, reject_qty, remarks  from trims_prod_order_breakdown  where job_id=$job_id and status_active=1 and is_deleted=0";
					// $prod_result =sql_select($cumelative_sql);
					// $cumelative_arr=array();
					// foreach($prod_result as $row)
					// {
					// 	$cumelative_arr[$row[csf('buyer_po_no')]][$row[csf('buyer_po_id')]][$row[csf('receive_details_id')]][$row[csf('break_dtls_id')]]['qc_pass_qty']+=$row[csf('qc_pass_qty')];
					// 	$cumelative_arr[$row[csf('buyer_po_no')]][$row[csf('buyer_po_id')]][$row[csf('receive_details_id')]][$row[csf('break_dtls_id')]]['reject_qty']+=$row[csf('reject_qty')];
					// }
					//echo "<pre>";
					//print_r($cumelative_arr);

			    	$cumelative_sql ="SELECT a.id,a.mst_id,a.order_id,a.buyer_po_no,a.buyer_po_id,a.order_no, a.job_id,a.curr_prod_qty,a.job_dtls_id, a.break_dtls_id,a.booking_dtls_id, a.receive_id,a.receive_details_id,
				    a.qc_pass_qty,a.reject_qty,a.remarks,d.internal_ref from trims_prod_order_breakdown a 
					left join wo_po_break_down b on a.buyer_po_id=b.id 
					left join wo_po_details_master c on c.id=b.job_id 
                    left join wo_order_entry_internal_ref d on c.JOB_NO = d.job_no  
		            where  a.job_id=$job_id and  a.status_active=1 and  a.is_deleted=0";
					$prod_result =sql_select($cumelative_sql);
					$cumelative_arr=array();
					foreach($prod_result as $row)
					{
						$cumelative_arr[$row[csf('buyer_po_no')]][$row[csf('buyer_po_id')]][$row[csf('receive_details_id')]][$row[csf('break_dtls_id')]]['qc_pass_qty']+=$row[csf('qc_pass_qty')];
						$cumelative_arr[$row[csf('buyer_po_no')]][$row[csf('buyer_po_id')]][$row[csf('receive_details_id')]][$row[csf('break_dtls_id')]]['reject_qty']+=$row[csf('reject_qty')];
						$cumelative_arr[$row[csf('buyer_po_no')]][$row[csf('buyer_po_id')]][$row[csf('receive_details_id')]][$row[csf('break_dtls_id')]]['internal_ref'] =$row[csf('internal_ref')];
					}

					if($within_group==1)$ord_cond="and a.order_id='$order_id'"; else $ord_cond='';	
					if($within_group==1)$buyer_po_cond="and b.buyer_po_id=e.buyer_po_id "; else $buyer_po_cond='';	
					if($itemGroup!='' && $itemGroup!=0) $itemGroup_cond="and b.item_group in ($itemGroup) "; else $itemGroup_cond='';	
					if($hdnDtlsUpdateId!=""){
						if($data_break!=""){
							$subcon_sql ="SELECT b.buyer_po_no,b.buyer_po_id,a.delivery_date,b.item_group,b.order_uom,c.qnty,booked_uom,c.booked_qty, b.id as subconDtlsId , c.id as subconBrkId,e.id as prodBrkID, e.qc_pass_qty, 
							e.reject_qty, e.remarks,a.id as receive_id, b.booked_conv_fac, a.within_group, a.trims_ref, b.buyer_style_ref
							from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c,trims_prod_order_breakdown e
							where a.entry_form=255 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.subcon_job=c.job_no_mst and b.buyer_po_no=e.buyer_po_no and c.color_id=$colorID and c.size_id=$sizeID and c.description='$desc' and b.sub_section=$subSection $ord_cond $buyer_po_cond and a.status_active=1 and b.status_active=1 and b.id=c.mst_id and a.id=$recv_id and a.within_group=$within_group and e.mst_id=$hdnDtlsUpdateId $itemGroup_cond
							group by  b.buyer_po_no,b.buyer_po_id,a.delivery_date,b.item_group,b.order_uom,c.qnty,booked_uom,c.booked_qty, b.id , c.id,e.id , e.qc_pass_qty, e.reject_qty, e.remarks,a.id, b.booked_conv_fac,a.within_group, a.trims_ref, b.buyer_style_ref
							order by a.id,c.id ASC";
						}else{
							$subcon_sql ="SELECT b.buyer_po_no,b.buyer_po_id,a.delivery_date,b.item_group,b.order_uom,c.qnty,booked_uom,c.booked_qty, b.id as subconDtlsId , c.id as subconBrkId,a.id as receive_id, b.booked_conv_fac,a.within_group, a.trims_ref, b.buyer_style_ref
							from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c,trims_job_card_mst d , trims_job_card_dtls e 
							where a.entry_form=255 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.subcon_job=c.job_no_mst and a.id=d.received_id and b.id=c.mst_id and  d.id=e.mst_id and b.booked_conv_fac=e.conv_factor and e.id=$jobDtlsId and c.color_id=$colorID and c.size_id=$sizeID and c.description='$desc' and b.sub_section=$subSection $ord_cond and a.status_active=1 and b.status_active=1 and b.id=c.mst_id and a.id=$recv_id and a.within_group=$within_group $itemGroup_cond
							group by b.buyer_po_no,b.buyer_po_id,a.delivery_date,b.item_group,b.order_uom,c.qnty,booked_uom,c.booked_qty, b.id , c.id,a.id , b.booked_conv_fac,a.within_group, a.trims_ref, b.buyer_style_ref
							order by a.id,c.id ASC"; 
						} 
					}else{ //echo "dgvdsg";
						$subcon_sql ="SELECT b.buyer_po_no,b.buyer_po_id,a.delivery_date,b.item_group,b.order_uom,c.qnty,booked_uom,c.booked_qty, b.id as subconDtlsId , c.id as subconBrkId,a.id as receive_id, b.booked_conv_fac,a.within_group, a.trims_ref, b.buyer_style_ref
						from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c,trims_job_card_mst d, trims_job_card_dtls e 
						where a.entry_form=255 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.subcon_job=c.job_no_mst and a.id=d.received_id and b.id=c.mst_id  and  d.id=e.mst_id and b.booked_conv_fac=e.conv_factor and e.id=$jobDtlsId and c.color_id='$colorID' and c.size_id=$sizeID and c.description='$desc' and b.sub_section=$subSection $ord_cond and a.status_active=1 and b.status_active=1 and b.id=c.mst_id and a.id=$recv_id and a.within_group=$within_group $itemGroup_cond
						group by b.buyer_po_no,b.buyer_po_id,a.delivery_date,b.item_group,b.order_uom,c.qnty,booked_uom,c.booked_qty, b.id , c.id,a.id, b.booked_conv_fac, a.within_group, a.trims_ref, b.buyer_style_ref
						order by a.id,c.id ASC"; //
					}
					//echo $subcon_sql;

					$dataArray=sql_select($subcon_sql); 


					//echo "<pre>"; print_r($dataArray); die;
					$dtls_arr=array();
					foreach ($dataArray as  $row) {
						//echo "nnnn"; die;
						$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["delivery_date"]		=change_date_format($row[csf("delivery_date")]);
						$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["item_group"]		.=$item_group_arr[$row[csf("item_group")]].',';
						$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["order_uom"]			.=$row[csf("order_uom")].',';
						$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["booked_uom"]			.=$row[csf("booked_uom")].',';
						$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["qnty"]					+=$row[csf("qnty")];
						$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["booked_qty"]			+=$row[csf("booked_qty")];
						$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["subconDtlsId"]			.=$row[csf("subconDtlsId")].',';
						$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["subconBrkId"]			.=$row[csf("subconBrkId")].',';
						$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["buyer_po_id"]			=$row[csf("buyer_po_id")];
						$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["buyer_style_ref"]			=$row[csf("buyer_style_ref")];
						$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["within_group"]			=$row[csf("within_group")];
						$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["trims_ref"]			=$row[csf("trims_ref")];
						$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["recv_id"]				=$row[csf("receive_id")];
						
						if($hdnDtlsUpdateId!=""){
							//$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["prodBrkID"]			=$row[csf("prodBrkID")];
							$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["prodBrkID"]			=$row[csf("prodBrkID")];
							$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["qc_pass_qty"]		=$row[csf("qc_pass_qty")];
							$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["reject_qty"]		=$row[csf("reject_qty")];
							$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["remarks"]			=$row[csf("remarks")];


							/*$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID][$desc][$subSection][$row[csf(booked_conv_fac"")]]["prodBrkID"]			=$row[csf("prodBrkID")];
							$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID][$desc][$subSection][$row[csf(booked_conv_fac"")]]["qc_pass_qty"]			=$row[csf("qc_pass_qty")];
							$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID][$desc][$subSection][$row[csf(booked_conv_fac"")]]["reject_qty"]			=$row[csf("reject_qty")];
							$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID][$desc][$subSection][$row[csf(booked_conv_fac"")]]["remarks"]				=$row[csf("remarks")];*/
						}
					}
					//echo "<pre>"; print_r($dtls_arr);
					$data_array=explode("***",$datconva_break);
					if(count($dtls_arr)>0)
					{ 
						$k=1; $subconBrkId=0;
						foreach($dtls_arr as $buyerPoNo=> $poNo_arr)
						{
							foreach($poNo_arr as $colorID=> $color_arr)
							{
								foreach($color_arr as $sizeID=> $size_arr)
								{
									foreach($size_arr as $desc=> $desc_arr)
									{
										foreach($desc_arr as $subSection=> $row)
										{
											//echo $desc;
											$subconBrkId=chop(implode(',',array_unique(explode(",",$row["subconBrkId"]))),',');
											
											//echo $subconBrkId."**";
											$subconDtlsId=chop(implode(',',array_unique(explode(",",$row["subconDtlsId"]))),',');
											$order_uom=chop(implode(',',array_unique(explode(",",$row["order_uom"]))),',');
											$booked_uom=chop(implode(',',array_unique(explode(",",$row["booked_uom"]))),',');
											$item_group=chop(implode(',',array_unique(explode(",",$row["item_group"]))),',');
											$prodBrkId=$qc_pass_qty=$reject_qty=$remarks=$recv_id=$buyer_po_id=$currprodqty='';
											if($hdnDtlsUpdateId!=""){
												$prodBrkId=$row['prodBrkID'];
												$qc_pass_qty=$row['qc_pass_qty'];
												$reject_qty=$row['reject_qty'];
												$remarks=$row['remarks'];
												$currprodqty=$row['qc_pass_qty']+$row['reject_qty'];
											}else{
												$qc_pass_qty=$poData[$buyerPoNo]['qcpass'];
												$reject_qty=$poData[$buyerPoNo]['reject'];
												$remarks=$poData[$buyerPoNo]['remark'];
												$currprodqty=$poData[$buyerPoNo]['currprodqty'];
											}
											$recv_id=$row['recv_id'];
											$buyer_po_id=$row['buyer_po_id'];
											$prvqc_pass_qty=$cumelative_arr[$buyerPoNo][$buyer_po_id][$subconDtlsId][$subconBrkId]['qc_pass_qty'];
											$prvreject_qty=$cumelative_arr[$buyerPoNo][$buyer_po_id][$subconDtlsId][$subconBrkId]['reject_qty'];
											$internal_ref=$cumelative_arr[$buyerPoNo][$buyer_po_id][$subconDtlsId][$subconBrkId]['internal_ref'];
											//echo $prvqc_pass_qty.'=='.$prvreject_qty.'++'; 
											if($hdnDtlsUpdateId!=""){
												//$curent_qty=$reject_qty+$qc_pass_qty;
												$curent_qty=$qc_pass_qty;
												//$prevCumProdQty=($prvqc_pass_qty+$prvreject_qty)-$curent_qty;
												$prevCumProdQty=($prvqc_pass_qty)-$curent_qty;
												//echo $prevCumProdQty.'=='.$prvqc_pass_qty.'=='.$prvreject_qty.'=='.$curent_qty;
												//0==2228306.85==0==2228306.85 
											}else{
												//$prevCumProdQty=$prvqc_pass_qty+$prvreject_qty;
												$prevCumProdQty=$prvqc_pass_qty;
											}
											 
											//987dkfdfjk38324dkfdfjk3200dkfdfjk6862 
											?>
											<tr>
												<td><pre><? echo $buyerPoNo; ?></pre></td>
												<?if($row['within_group']==2){
													?>
													<td><? echo $row["trims_ref"];?></td>
													<?
												}else{
													?>
													<td><? echo $internal_ref ;?></td>
													<?
												}?>
												<td align="center" ><? echo $row["buyer_style_ref"] ?></td>
												<td><? echo $row['delivery_date']; ?></td>
												<td><? echo $item_group; ?></td>												
												<td><? echo $unit_of_measurement[$order_uom]; ?></td>
												<td id="txtOrdQty_<? echo $k;?>" align="right"><? echo number_format($row['qnty'],4,'.',''); ?></td>
												<td><? echo $unit_of_measurement[$booked_uom]; ?></td>
												<td id="txtjobQty_<? echo $k;?>" align="right"><? echo  number_format($row['booked_qty'],4,'.','');  ?></td>
												<td><input type="text" id="txtQcPassQty_<? echo $k;?>" name="txtQcPassQty[]" onKeyUp="sum_total_qnty(2,'txtQcPassQty_','txt_total_Qcpass_qnty',<? echo $k;?>);" onChange="sum_total_qnty(2,'txtQcPassQty_','txt_total_Qcpass_qnty',<? echo $k;?>);" class="text_boxes_numeric" value="<? echo number_format($qc_pass_qty,4,'.','');?>"  style="width:70px" /> 
				                                <input type="hidden" id="txtQcBalQty_<? echo $k;?>" name="txtQcBalQty[]" class="text_boxes_numeric" value="<? echo $qc_pass_qty; ?>"   style="width:70px" />
				                                </td>
												<td><input type="text" id="txtRejectQty_<? echo $k;?>" name="txtRejectQty[]" onKeyUp="sum_total_qnty(2,'txtRejectQty_','txt_total_reject_qnty',<? echo $k;?>);"  onChange="sum_total_qnty(2,'txtQcPassQty_','txt_total_Qcpass_qnty',<? echo $k;?>);" class="text_boxes_numeric" value="<? echo number_format($reject_qty,4,'.','');  ?>" style="width:70px" /></td>
												<td id="txtProdQty_<? echo $k;?>" align="right"></td>
												<td id="txtCumProdQty_<? echo $k;?>" align="right"><? echo number_format($prevCumProdQty,4,'.','');?> </td>
												<td id="txtBalQty_<? echo $k;?>" align="right">  </td>
												<td><input type="text" id="txtRemark_<? echo $k;?>" name="txtRemark[]"  value="<? echo $remarks; ?>" class="text_boxes" />
													<input type="hidden" id="subconBrkId_<? echo $k;?>" name="subconBrkId[]" value="<? echo $subconBrkId; ?>"  />
													<input type="hidden" id="subconDtlsId_<? echo $k;?>" name="subconDtlsId[]" value="<? echo $subconDtlsId; ?>"  />
													<input type="hidden" id="poNo_<? echo $k;?>" name="poNo[]" value="<? echo $buyerPoNo; ?>"  />
				                                    <input type="hidden" id="prodBrkID_<? echo $k;?>" name="prodBrkID[]" value="<? echo $prodBrkId; ?>"  />
				                                    <input type="hidden" id="recvid_<? echo $k;?>" name="recvid[]" value="<? echo  $row['recv_id']; ?>"  />
				                                    <input type="hidden" id="buyerpoid_<? echo $k;?>" name="buyerpoid[]" value="<? echo $row['buyer_po_id'];  ?>"  />
				                                    <input type="hidden" id="hidBalQty_<? echo $k;?>" name="hidBalQty[]" value="<?  ?>"  />
				                                    <input type="hidden" id="hidCompProd_<? echo $k;?>" name="hidCompProd[]" value="<?  ?>"  />
				                                    <input type="hidden" id="hdnCumDelQty_<? echo $k;?>" name="hdnCumDelQty[]" value="<?  ?>"  />
				                                    <input type="hidden" id="hdnDtlsUpdateId_<? echo $k;?>" name="hdnDtlsUpdateId[]" value="<? echo $hdnDtlsUpdateId;  ?>"  />
				                                    <input type="hidden" id="hdnAllowedQty_<? echo $k;?>" name="hdnAllowedQty[]" value="<? echo $hdnDtlsUpdateId;  ?>"  />
				                                    <input type="hidden" id="hdnAllowdPercent_<? echo $k;?>" name="hdnAllowdPercent[]" value="<? echo $over_ctrl_var_sts;  ?>"  />
												</td>
											</tr>
											<?
											$k++;
										}
									}
								}
							}
						}
					}
					?>
					</tbody>
					<tfoot>
						<th colspan="6">Total</th> 
						<th id="txtTotOrdQty"></th>
						<th>&nbsp;</th> 
						<th id="txtjobOrdQty"></th>
						<th><input type="text" id="txt_total_Qcpass_qnty" name="txt_total_Qcpass_qnty" class="text_boxes_numeric" readonly style="width:70px" value=""; /></th>
						<th><input type="text" id="txt_total_reject_qnty" name="txt_total_reject_qnty" class="text_boxes_numeric" readonly style="width:70px" value=""; /></th>
                        <th id="txt_total_Prod_qnty" align="right"></th>
						<th id="txt_total_Comp_qnty" align="right"></th>
						<th id="txt_total_Bal_qnty" align="right"></th>
						<th>&nbsp;</th> 
					</tfoot>
				</table> 
				<table>
					<tr>
						<td align="center"><input type="button" name="main_close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" /></td>
					</tr>
				</table>
		</form>
	</div>
</body>
<script>sum_total_qnty(1,1,1,1)</script>        
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="qc_qty_popupbackup_m")
{
	//echo 11; 
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$desc=return_field_value("item_description"," trims_job_card_dtls","id='$jobDtlsId'");
	//$txtItemmmm=trim($txtItem);
	//$txtItemmmm=str_replace('<pre>', '', $txtItem);
	//$txtItemmmm=trim($txtItem);
	//$txtItemmmm=trim($txtItem,"\n");
	//$txtItemmmm=trim(preg_replace('/\s+/', '', $txtItem));
	//$txtItemmmm = preg_replace('/[\r\n]+/', "\n", $txtItem);
	//$txtItemmmm = trim(preg_replace('/\s\s+/', ' ', $txtItem));
	//echo  '='.preg_replace('/[ \t]+/', ' ', $txtItemmmm).'=';
	//echo  '='.$txtItemmmm.'=';

	$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
	$size_arr=return_library_array( "select id,size_name from  lib_size",'id','size_name');
	$item_group_arr=return_library_array( "select id,item_name from  lib_item_group",'id','item_name');
    ?>
    <script>
	
	function fnc_close()
	{
		var tot_row=$('#tbl_share_details_entry tbody tr').length;
		var data_break_down="";
		for(var i=1; i<=tot_row; i++)
		{
			/*if (form_validation('txtQcPassQty_'+i,'QC. Pass Qty')==false)
			{
				return;
			}*/
			//alert($("#txtsize_"+i).val());
			if($("#txtQcPassQty_"+i).val()=="") $("#txtQcPassQty_"+i).val(0)
			if($("#txtRejectQty_"+i).val()=="") $("#txtRejectQty_"+i).val(0);
			if($("#txtRemark_"+i).val()=="") $("#txtRemark_"+i).val('');
			if($("#subconBrkId_"+i).val()=="") $("#subconBrkId_"+i).val(0);
			if($("#subconDtlsId_"+i).val()=="") $("#subconDtlsId_"+i).val(0);
			if($("#poNo_"+i).val()=="") $("#poNo_"+i).val('');
			if($("#prodBrkID_"+i).val()=="") $("#prodBrkID_"+i).val(0);
			if($("#recvid_"+i).val()=="") $("#recvid_"+i).val(0);
			if($("#buyerpoid_"+i).val()=="") $("#buyerpoid_"+i).val(0);
			//alert($('#txtQcPassQty_'+i).val());
			if(data_break_down==""){
				data_break_down+=$('#poNo_'+i).val()+'_'+$('#txtQcPassQty_'+i).val()+'_'+$('#txtRejectQty_'+i).val()+'_'+$('#txtRemark_'+i).val()+'_'+$('#subconBrkId_'+i).val()+'_'+$('#subconDtlsId_'+i).val()+'_'+$('#prodBrkID_'+i).val()+'_'+$('#recvid_'+i).val()+'_'+$('#buyerpoid_'+i).val();
			}else{
				data_break_down+="***"+$('#poNo_'+i).val()+'_'+$('#txtQcPassQty_'+i).val()+'_'+$('#txtRejectQty_'+i).val()+'_'+$('#txtRemark_'+i).val()+'_'+$('#subconBrkId_'+i).val()+'_'+$('#subconDtlsId_'+i).val()+'_'+$('#prodBrkID_'+i).val()+'_'+$('#recvid_'+i).val()+'_'+$('#buyerpoid_'+i).val();
			}
		}
		
		data_break_down+="*******"+$('#txt_total_Qcpass_qnty').val()+'_'+$('#txt_total_reject_qnty').val()+'_'+$('#txt_total_Prod_qnty').html()+'_'+$('#txt_total_Comp_qnty').html()+'_'+$('#txt_total_Bal_qnty').html();
		//alert(data_break_down);
		$('#hidden_break_tot_row').val( data_break_down );
		//alert(tot_row);//return;
		parent.emailwindow.hide();
	}

	function sum_total_qnty(status,rowId,sumId,rowNum)
	{
		//alert(status);
		//hdnAllowdPercent_
		
		var ddd={ dec_type:5, comma:0, currency:''};
		 var prodQty=0; var balQty=0; var jobQtyWithOver=0;
		var tot_row=$('#tbl_share_details_entry tbody tr').length;
		if (status==1) 
		{
			math_operation( 'txt_total_Qcpass_qnty', 'txtQcPassQty_', "+", tot_row,ddd );
			math_operation( 'txt_total_reject_qnty', 'txtRejectQty_', "+", tot_row,ddd );
			var ordQty=0; var jobQty=0; var cumProdQty=0; 
			for(var i=1; i<=tot_row; i++)
			{
				ordQty 	+= $('#txtOrdQty_'+i).html()*1;
				jobQty	+= $('#txtjobQty_'+i).html()*1;

				allowdPercent=$('#hdnAllowdPercent_'+i).val()*1;
				if(allowdPercent!=0 || allowdPercent!=''){
					jobQtyWithOver =((($('#txtjobQty_'+i).html()*1)* (allowdPercent))/100)+($('#txtjobQty_'+i).html()*1);
				}
				else{
					jobQtyWithOver=$('#txtjobQty_'+i).html()*1;
				}
				

				$('#txtProdQty_'+i).html( ($("#txtQcPassQty_"+i).val()*1)+($("#txtRejectQty_"+i).val()*1) );
				//$('#txtBalQty_'+i).html( (($("#txtjobQty_"+i).html()*1)-(($("#txtQcPassQty_"+i).val()*1)+($("#txtRejectQty_"+i).val()*1)))-$('#txtCumProdQty_'+i).html()*1 );
				$('#txtBalQty_'+i).html( ((jobQtyWithOver)-($("#txtQcPassQty_"+i).val()*1))-$('#txtCumProdQty_'+i).html()*1 );
				//$('#txtBalQty_'+i).html( (($("#txtjobQty_"+i).html()*1)-$("#txtQcPassQty_"+i).val()*1)-$('#txtCumProdQty_'+i).html()*1 );
				prodQty 	+= $('#txtProdQty_'+i).html()*1;
				cumProdQty 	+= $('#txtCumProdQty_'+i).html()*1;
				balQty		+= $('#txtBalQty_'+i).html()*1;
			}
			$('#txtTotOrdQty').html( ordQty.toFixed(4) );
			$('#txtjobOrdQty').html( jobQty.toFixed(4) );
			$('#txt_total_Prod_qnty').html( prodQty.toFixed(4) );
			$('#txt_total_Comp_qnty').html( cumProdQty.toFixed(4) );
			$('#txt_total_Bal_qnty').html( balQty.toFixed(4) );
		}
		else
		{
			//jobQtyWithOver=(("#txtjobQty_"+rowNum).html()*1*("#hdnAllowdPercent_"+rowNum).val()*1 )/100;

			allowdPercent=$('#hdnAllowdPercent_'+rowNum).val()*1;
			if(allowdPercent!=0 || allowdPercent!=''){
				jobQtyWithOver =((($('#txtjobQty_'+rowNum).html()*1)* (allowdPercent))/100)+($('#txtjobQty_'+rowNum).html()*1);
			}
			else{
				jobQtyWithOver=$('#txtjobQty_'+rowNum).html()*1;
			}

			


			prodQty=($("#txtQcPassQty_"+rowNum).val()*1)+($("#txtRejectQty_"+rowNum).val()*1);
			//var balQty=(($("#txtjobQty_"+rowNum).html()*1)-(($("#txtQcPassQty_"+rowNum).val()*1)+($("#txtRejectQty_"+rowNum).val()*1))-$('#txtCumProdQty_'+rowNum).html()*1);
			balQty=(jobQtyWithOver-($("#txtQcPassQty_"+rowNum).val()*1)-$('#txtCumProdQty_'+rowNum).html()*1);
			//var balQty=(($("#txtjobQty_"+rowNum).html()*1)-($("#txtQcPassQty_"+rowNum).val()*1)-$('#txtCumProdQty_'+rowNum).html()*1);
			var qc_rej_balQty=(jobQtyWithOver-(($("#txtQcPassQty_"+rowNum).val()*1)+($("#txtRejectQty_"+rowNum).val()*1))-$('#txtCumProdQty_'+rowNum).html()*1);
			
			//alert(balQty);
			if(qc_rej_balQty<0){
				alert("Production Qty can't exceed Production Balance Qty");
				$('#txtProdQty_'+rowNum).html('');
				$('#txtQcPassQty_'+rowNum).val('');
				$('#txtRejectQty_'+rowNum).val('');
			}else{
				$('#txtProdQty_'+rowNum).html( prodQty.toFixed(4) );
				$('#txtBalQty_'+rowNum).html( balQty.toFixed(4) );
			}
			var totalProdQty=0; var totalBalQty=0;var totalCumProdQty=0;
			for(var i=1; i<=tot_row; i++)
			{
				var pQty= $('#txtProdQty_'+i).html()*1
				var cumQty= $('#txtCumProdQty_'+i).html()*1
				var balQty= $('#txtBalQty_'+i).html()*1
				if(pQty!='' && pQty>0){
					totalProdQty +=pQty;
				}
				if(cumQty!='' && cumQty>0){
					totalCumProdQty +=cumQty;
				}
				if(balQty!='' && balQty>0){
					totalBalQty +=balQty;
				}
			}
			math_operation( sumId, rowId, "+", tot_row,ddd );
			$('#txt_total_Prod_qnty').html( totalProdQty.toFixed(4) );
			$('#txt_total_Comp_qnty').html( totalCumProdQty.toFixed(4) );
			$('#txt_total_Bal_qnty').html( totalBalQty.toFixed(4) );
		}
	}
		
	function copy_values()
	{
		var copy_val=document.getElementById('copy_basis').checked;
		var row_num=$('#tbl_share_details_entry tbody tr').length;
		if(copy_val==true){
			for (var j=1; j<=row_num; j++)
			{
				var txtOrdQty 		= $('#txtjobQty_'+j).html()*1;
				var txtQcBalQty 	= $('#txtBalQty_'+j).html()*1;
				$('#txtQcPassQty_'+j).val((txtQcBalQty).toFixed(4));
				sum_total_qnty(1,1,1,1);
			}
		}else{
			for (var j=1; j<=row_num; j++)
			{
				var txtOrdQty 		= $('#txtjobQty_'+j).html()*1;
				$('#txtQcPassQty_'+j).val('');
				sum_total_qnty(1,1,1,1);
			}
		}
		//var copy_basis=$('input[name="copy_basis"]:checked').val()
	}
	/*function cal_values(rowNo)
	{
		var balance='';
		var txtOrdQty 		= $('#txtOrdQty_'+rowNo).html()*1;
		var txtjobQty 		= $('#txtjobQty_'+rowNo).html()*1;
		var txtCumProdQty	= $('#txtCumProdQty_'+rowNo).html()*1;
		var txtBalQty		= $('#txtBalQty_'+rowNo).html()*1;
		var txtQcPassQty 	= $('#txtQcPassQty_'+rowNo).val()*1;
		var txtRejectQty 	= $('#txtRejectQty_'+rowNo).val()*1;
		
		$('#txtProdQty_'+rowNo).html( ($("#txtQcPassQty_"+rowNo).val()*1)+($("#txtRejectQty_"+rowNo).val()*1) );
		//$('#txtBalQty_'+rowNo).html( ($("#txtjobQty_"+rowNo).html()*1)-(($("#txtQcPassQty_"+rowNo).val()*1)+($("#txtRejectQty_"+rowNo).val()*1)) );
		$('#txtBalQty_'+rowNo).html( (($("#txtjobQty_"+rowNo).html()*1)-(($("#txtQcPassQty_"+rowNo).val()*1)+($("#txtRejectQty_"+rowNo).val()*1))-$('#txtCumProdQty_'+rowNo).html()*1) );
		
		if(txtBalQty<0)
		{
			alert("Production Qty can't exceed Production Balance Qty");
			$('#txtProdQty_'+rowNo).val('');
			$('#txtQcPassQty_'+rowNo).val('');
			//$('#txtBalQty_'+rowNo).val((hidBalQty).toFixed(4));

			//$('#txtCompProd_'+rowNo).val((txtOrdQty-hidBalQty).toFixed(4));
			return;
		}
		else
		{
			$('#txtBalQty_'+rowNo).val((balance).toFixed(4));
			$('#txtCompProd_'+rowNo).val((hidCompProd+txtQcPassQty).toFixed(4));
		}
		var txtCompProd 	= $('#txtCompProd_'+rowNo).val()*1;
		if(hdnDtlsUpdateId!='')
		{//alert(txtCompProd+'='+hdnCumDelQty);
			if(txtCompProd<hdnCumDelQty) 
			{
				alert("Cumulative Production Qty can't Less Cumulative Delivery Qty");
				$('#txtProdQty_'+rowNo).val('');
				$('#txtQcPassQty_'+rowNo).val('');
				$('#txtRejectQty_'+rowNo).val('');
				$('#txtCompProd_'+rowNo).val((hidCompProd).toFixed(4));
				return;
				//cal_values(rowNo);
			}
		}
	}
	*/

	</script>
</head>
<body>
	<div align="center" style="width:100%;" >
		<form name="qntypopup_1"  id="qntypopup_1" autocomplete="off">
			<table class="rpt_table" width="1100px" cellspacing="0" cellpadding="0" rules="all" id="tbl_share_details_entry">
				<thead>
                	<th width="80">PO NO</th>
                    <th width="60">Delv Date</th>
                    <th width="170">Item Group</th>
                    <th width="60">Order UOM</th>
					<th width="70">WO Qty</th>
                    <th width="50">BKD UOM</th>
					<th width="80">Job Qty</th>
					<th width="80" >QC. Pass Qty<input id="copy_basis" name="copy_basis" type="checkbox" onChange="copy_values()" /></th>
                    <th width="80">Reject Qty</th>
                    <th width="80">Current Prod. Qty.</th>
                    <th width="80">Cum. Prod. Qty</th>
                    <th width="80">Balance</th>
                    <th>Remarks</th>
				</thead>
				<tbody>
					<input type="hidden" name="txtDeletedId" id="txtDeletedId" class="text_boxes_numeric" style="width:90px" readonly />
					<input type="hidden" name="hidden_break_tot_row" id="hidden_break_tot_row" class="text_boxes" style="width:90px" />
					<?
					//echo $data_break.'=='; 
					if($data_break!='')
					{
						$data_break=explode('*******',$data_break);
						$breakDtls=explode('***',$data_break[0]);
						$poData=array();
						for($i=0;$i<count($breakDtls);$i++)
						{
							$breakDatas=explode('_',$breakDtls[$i]);
							for($j=0;$j<count($breakDatas);$j++)
							{
								//echo $breakDatas[$j]."==";
								$poData[$breakDatas[0]]['qcpass']=$breakDatas[1];
								$poData[$breakDatas[0]]['reject']=$breakDatas[2];
								$poData[$breakDatas[0]]['remark']=$breakDatas[3];
								$poData[$breakDatas[0]]['currprodqty']=$breakDatas[1]+$breakDatas[2];
							}
						}
					}
					/*echo "<pre>";
					print_r($poData);*/
					
					$cumelative_sql ="select id, mst_id, order_id, buyer_po_no, buyer_po_id, order_no,job_id, curr_prod_qty, job_dtls_id, break_dtls_id, booking_dtls_id, receive_id,receive_details_id, qc_pass_qty, reject_qty, remarks  from trims_prod_order_breakdown  where job_id=$job_id and status_active=1 and is_deleted=0";
					$prod_result =sql_select($cumelative_sql);
					$cumelative_arr=array();
					foreach($prod_result as $row)
					{
						$cumelative_arr[$row[csf('buyer_po_no')]][$row[csf('buyer_po_id')]][$row[csf('receive_details_id')]][$row[csf('break_dtls_id')]]['qc_pass_qty']+=$row[csf('qc_pass_qty')];
						$cumelative_arr[$row[csf('buyer_po_no')]][$row[csf('buyer_po_id')]][$row[csf('receive_details_id')]][$row[csf('break_dtls_id')]]['reject_qty']+=$row[csf('reject_qty')];
					}
					//echo "<pre>";
					//print_r($cumelative_arr);

					$cumelative_sql ="select id, mst_id, order_id, buyer_po_no, buyer_po_id, order_no,job_id, curr_prod_qty, job_dtls_id, break_dtls_id, booking_dtls_id, receive_id,receive_details_id, qc_pass_qty, reject_qty, remarks  from trims_prod_order_breakdown  where job_id=$job_id and status_active=1 and is_deleted=0";
					$prod_result =sql_select($cumelative_sql);
					$cumelative_arr=array();
					foreach($prod_result as $row)
					{
						$cumelative_arr[$row[csf('buyer_po_no')]][$row[csf('buyer_po_id')]][$row[csf('receive_details_id')]][$row[csf('break_dtls_id')]]['qc_pass_qty']+=$row[csf('qc_pass_qty')];
						$cumelative_arr[$row[csf('buyer_po_no')]][$row[csf('buyer_po_id')]][$row[csf('receive_details_id')]][$row[csf('break_dtls_id')]]['reject_qty']+=$row[csf('reject_qty')];
					}

					if($within_group==1)$ord_cond="and a.order_id='$order_id'"; else $ord_cond='';	
					if($within_group==1)$buyer_po_cond="and b.buyer_po_id=e.buyer_po_id "; else $buyer_po_cond='';	
					if($itemGroup!='' && $itemGroup!=0) $itemGroup_cond="and b.item_group in ($itemGroup) "; else $itemGroup_cond='';	
					if($hdnDtlsUpdateId!=""){
						if($data_break!=""){
							$subcon_sql ="select b.buyer_po_no,b.buyer_po_id,a.delivery_date,b.item_group,b.order_uom,c.qnty,booked_uom,c.booked_qty, b.id as subconDtlsId , c.id as subconBrkId,e.id as prodBrkID, e.qc_pass_qty, 
							e.reject_qty, e.remarks,a.id as receive_id, b.booked_conv_fac
							from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c,trims_prod_order_breakdown e
							where a.entry_form=255 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.subcon_job=c.job_no_mst and b.buyer_po_no=e.buyer_po_no and c.color_id=$colorID and c.size_id=$sizeID and c.description='$desc' and b.sub_section=$subSection $ord_cond $buyer_po_cond and a.status_active=1 and b.status_active=1 and b.id=c.mst_id and a.id=$recv_id and a.within_group=$within_group and e.mst_id=$hdnDtlsUpdateId $itemGroup_cond
							group by  b.buyer_po_no,b.buyer_po_id,a.delivery_date,b.item_group,b.order_uom,c.qnty,booked_uom,c.booked_qty, b.id , c.id,e.id , e.qc_pass_qty, 
							e.reject_qty, e.remarks,a.id, b.booked_conv_fac
							order by a.id DESC";
						}else{
							$subcon_sql ="select b.buyer_po_no,b.buyer_po_id,a.delivery_date,b.item_group,b.order_uom,c.qnty,booked_uom,c.booked_qty, b.id as subconDtlsId , c.id as subconBrkId,a.id as receive_id, b.booked_conv_fac
							from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c,trims_job_card_mst d , trims_job_card_dtls e 
							where a.entry_form=255 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.subcon_job=c.job_no_mst and a.id=d.received_id and b.id=c.mst_id and  d.id=e.mst_id and b.booked_conv_fac=e.conv_factor and e.id=$jobDtlsId and c.color_id=$colorID and c.size_id=$sizeID and c.description='$desc' and b.sub_section=$subSection $ord_cond and a.status_active=1 and b.status_active=1 and b.id=c.mst_id and a.id=$recv_id and a.within_group=$within_group $itemGroup_cond
							group by b.buyer_po_no,b.buyer_po_id,a.delivery_date,b.item_group,b.order_uom,c.qnty,booked_uom,c.booked_qty, b.id , c.id,a.id , b.booked_conv_fac
							order by a.id DESC"; 
						} 
					}else{ //echo "dgvdsg";
						$subcon_sql ="select b.buyer_po_no,b.buyer_po_id,a.delivery_date,b.item_group,b.order_uom,c.qnty,booked_uom,c.booked_qty, b.id as subconDtlsId , c.id as subconBrkId,a.id as receive_id, b.booked_conv_fac
						from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c,trims_job_card_mst d, trims_job_card_dtls e 
						where a.entry_form=255 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.subcon_job=c.job_no_mst and a.id=d.received_id and b.id=c.mst_id  and  d.id=e.mst_id and b.booked_conv_fac=e.conv_factor and e.id=$jobDtlsId and c.color_id='$colorID' and c.size_id=$sizeID and c.description='$desc' and b.sub_section=$subSection $ord_cond and a.status_active=1 and b.status_active=1 and b.id=c.mst_id and a.id=$recv_id and a.within_group=$within_group $itemGroup_cond
						group by b.buyer_po_no,b.buyer_po_id,a.delivery_date,b.item_group,b.order_uom,c.qnty,booked_uom,c.booked_qty, b.id , c.id,a.id, b.booked_conv_fac
						order by a.id DESC"; //
					}
					//echo $subcon_sql;

					$dataArray=sql_select($subcon_sql); 


					//echo "<pre>"; print_r($dataArray); die;
					$dtls_arr=array();
					foreach ($dataArray as  $row) {
						//echo "nnnn"; die;
						$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["delivery_date"]		=change_date_format($row[csf("delivery_date")]);
						$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["item_group"]		.=$item_group_arr[$row[csf("item_group")]].',';
						$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["order_uom"]			.=$row[csf("order_uom")].',';
						$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["booked_uom"]			.=$row[csf("booked_uom")].',';
						$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["qnty"]					+=$row[csf("qnty")];
						$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["booked_qty"]			+=$row[csf("booked_qty")];
						$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["subconDtlsId"]			.=$row[csf("subconDtlsId")].',';
						$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["subconBrkId"]			.=$row[csf("subconBrkId")].',';
						$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["buyer_po_id"]			=$row[csf("buyer_po_id")];
						$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["recv_id"]				=$row[csf("receive_id")];
						
						if($hdnDtlsUpdateId!=""){
							//$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["prodBrkID"]			=$row[csf("prodBrkID")];
							$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["prodBrkID"]			=$row[csf("prodBrkID")];
							$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["qc_pass_qty"]		=$row[csf("qc_pass_qty")];
							$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["reject_qty"]		=$row[csf("reject_qty")];
							$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID]["'".$desc."'"][$subSection]["remarks"]			=$row[csf("remarks")];


							/*$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID][$desc][$subSection][$row[csf(booked_conv_fac"")]]["prodBrkID"]			=$row[csf("prodBrkID")];
							$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID][$desc][$subSection][$row[csf(booked_conv_fac"")]]["qc_pass_qty"]			=$row[csf("qc_pass_qty")];
							$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID][$desc][$subSection][$row[csf(booked_conv_fac"")]]["reject_qty"]			=$row[csf("reject_qty")];
							$dtls_arr[$row[csf("buyer_po_no")]][$colorID][$sizeID][$desc][$subSection][$row[csf(booked_conv_fac"")]]["remarks"]				=$row[csf("remarks")];*/
						}
					}
					//echo "<pre>"; print_r($dtls_arr);
					$data_array=explode("***",$datconva_break);
					if(count($dtls_arr)>0)
					{ 
						$k=1; $subconBrkId=0;
						foreach($dtls_arr as $buyerPoNo=> $poNo_arr)
						{
							foreach($poNo_arr as $colorID=> $color_arr)
							{
								foreach($color_arr as $sizeID=> $size_arr)
								{
									foreach($size_arr as $desc=> $desc_arr)
									{
										foreach($desc_arr as $subSection=> $row)
										{
											//echo $desc;
											$subconBrkId=chop(implode(',',array_unique(explode(",",$row["subconBrkId"]))),',');
											$subconDtlsId=chop(implode(',',array_unique(explode(",",$row["subconDtlsId"]))),',');
							
											$order_uom=chop(implode(',',array_unique(explode(",",$row["order_uom"]))),',');
											$booked_uom=chop(implode(',',array_unique(explode(",",$row["booked_uom"]))),',');
											$item_group=chop(implode(',',array_unique(explode(",",$row["item_group"]))),',');
											$prodBrkId=$qc_pass_qty=$reject_qty=$remarks=$recv_id=$buyer_po_id=$currprodqty='';
											if($hdnDtlsUpdateId!=""){
												$prodBrkId=$row['prodBrkID'];
												$qc_pass_qty=$row['qc_pass_qty'];
												$reject_qty=$row['reject_qty'];
												$remarks=$row['remarks'];
												$currprodqty=$row['qc_pass_qty']+$row['reject_qty'];
											}else{
												$qc_pass_qty=$poData[$buyerPoNo]['qcpass'];
												$reject_qty=$poData[$buyerPoNo]['reject'];
												$remarks=$poData[$buyerPoNo]['remark'];
												$currprodqty=$poData[$buyerPoNo]['currprodqty'];
											}
											$recv_id=$row['recv_id'];
											$buyer_po_id=$row['buyer_po_id'];
											$prvqc_pass_qty=$cumelative_arr[$buyerPoNo][$buyer_po_id][$subconDtlsId][$subconBrkId]['qc_pass_qty'];
											$prvreject_qty=$cumelative_arr[$buyerPoNo][$buyer_po_id][$subconDtlsId][$subconBrkId]['reject_qty'];
											//echo $prvqc_pass_qty.'=='.$prvreject_qty.'++'; 
											if($hdnDtlsUpdateId!=""){
												//$curent_qty=$reject_qty+$qc_pass_qty;
												$curent_qty=$qc_pass_qty;
												//$prevCumProdQty=($prvqc_pass_qty+$prvreject_qty)-$curent_qty;
												$prevCumProdQty=($prvqc_pass_qty)-$curent_qty;
												//echo $prevCumProdQty.'=='.$prvqc_pass_qty.'=='.$prvreject_qty.'=='.$curent_qty;
												//0==2228306.85==0==2228306.85 
											}else{
												//$prevCumProdQty=$prvqc_pass_qty+$prvreject_qty;
												$prevCumProdQty=$prvqc_pass_qty;
											}
											 
											//987dkfdfjk38324dkfdfjk3200dkfdfjk6862 
											?>
											<tr>
												<td><pre><? echo $buyerPoNo; ?></pre></td>
												<td><? echo $row['delivery_date']; ?></td>
												<td><? echo $item_group; ?></td>
												<td><? echo $unit_of_measurement[$order_uom]; ?></td>
												<td id="txtOrdQty_<? echo $k;?>" align="right"><? echo $row['qnty']; ?></td>
												<td><? echo $unit_of_measurement[$booked_uom]; ?></td>
												<td id="txtjobQty_<? echo $k;?>" align="right"><? echo  $row['booked_qty'];  ?></td>
												<td><input type="text" id="txtQcPassQty_<? echo $k;?>" name="txtQcPassQty[]" onKeyUp="sum_total_qnty(2,'txtQcPassQty_','txt_total_Qcpass_qnty',<? echo $k;?>);" onChange="sum_total_qnty(2,'txtQcPassQty_','txt_total_Qcpass_qnty',<? echo $k;?>);" class="text_boxes_numeric" value="<? echo $qc_pass_qty; ?>"  style="width:70px" /> 
				                                <input type="hidden" id="txtQcBalQty_<? echo $k;?>" name="txtQcBalQty[]" class="text_boxes_numeric" value="<? echo $qc_pass_qty; ?>"   style="width:70px" />
				                                </td>
												<td><input type="text" id="txtRejectQty_<? echo $k;?>" name="txtRejectQty[]" onKeyUp="sum_total_qnty(2,'txtRejectQty_','txt_total_reject_qnty',<? echo $k;?>);"  onChange="sum_total_qnty(2,'txtQcPassQty_','txt_total_Qcpass_qnty',<? echo $k;?>);" class="text_boxes_numeric" value="<? echo $reject_qty; ?>" style="width:70px" /></td>
												<td id="txtProdQty_<? echo $k;?>" align="right"></td>
												<td id="txtCumProdQty_<? echo $k;?>" align="right"><? echo  $prevCumProdQty; ?> </td>
												<td id="txtBalQty_<? echo $k;?>" align="right">  </td>
												<td><input type="text" id="txtRemark_<? echo $k;?>" name="txtRemark[]"  value="<? echo $remarks; ?>" class="text_boxes" />
													<input type="hidden" id="subconBrkId_<? echo $k;?>" name="subconBrkId[]" value="<? echo $subconBrkId; ?>"  />
													<input type="hidden" id="subconDtlsId_<? echo $k;?>" name="subconDtlsId[]" value="<? echo $subconDtlsId; ?>"  />
													<input type="hidden" id="poNo_<? echo $k;?>" name="poNo[]" value="<? echo $buyerPoNo; ?>"  />
				                                    <input type="hidden" id="prodBrkID_<? echo $k;?>" name="prodBrkID[]" value="<? echo $prodBrkId; ?>"  />
				                                    <input type="hidden" id="recvid_<? echo $k;?>" name="recvid[]" value="<? echo  $row['recv_id']; ?>"  />
				                                    <input type="hidden" id="buyerpoid_<? echo $k;?>" name="buyerpoid[]" value="<? echo $row['buyer_po_id'];  ?>"  />
				                                    <input type="hidden" id="hidBalQty_<? echo $k;?>" name="hidBalQty[]" value="<?  ?>"  />
				                                    <input type="hidden" id="hidCompProd_<? echo $k;?>" name="hidCompProd[]" value="<?  ?>"  />
				                                    <input type="hidden" id="hdnCumDelQty_<? echo $k;?>" name="hdnCumDelQty[]" value="<?  ?>"  />
				                                    <input type="hidden" id="hdnDtlsUpdateId_<? echo $k;?>" name="hdnDtlsUpdateId[]" value="<? echo $hdnDtlsUpdateId;  ?>"  />
				                                    <input type="hidden" id="hdnAllowedQty_<? echo $k;?>" name="hdnAllowedQty[]" value="<? echo $hdnDtlsUpdateId;  ?>"  />
				                                    <input type="hidden" id="hdnAllowdPercent_<? echo $k;?>" name="hdnAllowdPercent[]" value="<? echo $over_ctrl_var_sts;  ?>"  />
												</td>
											</tr>
											<?
											$k++;
										}
									}
								}
							}
						}
					}
					?>
					</tbody>
					<tfoot>
						<th colspan="4">Total</th> 
						<th id="txtTotOrdQty"></th>
						<th>&nbsp;</th> 
						<th id="txtjobOrdQty"></th>
						<th><input type="text" id="txt_total_Qcpass_qnty" name="txt_total_Qcpass_qnty" class="text_boxes_numeric" readonly style="width:70px" value=""; /></th>
						<th><input type="text" id="txt_total_reject_qnty" name="txt_total_reject_qnty" class="text_boxes_numeric" readonly style="width:70px" value=""; /></th>
                        <th id="txt_total_Prod_qnty" align="right"></th>
						<th id="txt_total_Comp_qnty" align="right"></th>
						<th id="txt_total_Bal_qnty" align="right"></th>
						<th>&nbsp;</th> 
					</tfoot>
				</table> 
				<table>
					<tr>
						<td align="center"><input type="button" name="main_close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" /></td>
					</tr>
				</table>
		</form>
	</div>
</body>
<script>sum_total_qnty(1,1,1,1)</script>        
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}


if( $action=='po_order_dtls_list_view' ) 
{
	//echo $data; die;
	$data=explode('**',$data);
	if($data[0]==1){
		$over_ctrl_var_sts=$data[5];
	}else{
		$over_ctrl_var_sts=$data[9];
	}
	
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_arr=return_library_array( "select id,size_name from lib_size",'id','size_name');
	$tblRow=0; $buyer_po_arr=array();
	
	$jobDtls_result =sql_select("select  id, sub_section,material_color,job_quantity from trims_job_card_dtls where status_active=1");
	$jobDtls_arr=array();
	foreach($jobDtls_result as $row){
		$jobDtls_arr[$row[csf('id')]]['sub_section']=$row[csf('sub_section')];
		$jobDtls_arr[$row[csf('id')]]['material_color']=$row[csf('material_color')];
		$jobDtls_arr[$row[csf('id')]]['job_quantity']=$row[csf('job_quantity')];
	}
	
	if($data[5]!='')
	{
		$prod_sql= "select a.id as prodDtlsId, a.mst_id, a.machine_id, a.item_description, a.color_id, a.size_id, a.uom,  a.impression, a.material_color_id, a.qty_reel,  a.total_head, a.qc_qty, a.production_qty, a.comp_prod, a.reject_qty, a.prod_time, a.remarks, a.booking_dtls_id, a.receive_dtls_id, a.job_dtls_id, a.book_con_dtls_id, a.break_id,c.received_id from trims_production_dtls a,trims_production_mst c where a.mst_id=c.id and a.mst_id=$data[5] and a.status_active=1 and a.status_active=1";
		$productionReasult =sql_select($prod_sql); $prodn_arr=array(); $prodn_jobDtls_arr=array();
		foreach($productionReasult as $row){
			$prodn_arr[$row[csf('job_dtls_id')]]['machine_id']=$row[csf('machine_id')];
			$prodn_arr[$row[csf('job_dtls_id')]]['prod_time']=$row[csf('prod_time')];
			$prodn_arr[$row[csf('job_dtls_id')]]['job_dtls_id']=$row[csf('job_dtls_id')];
			$prodn_arr[$row[csf('job_dtls_id')]]['id']=$row[csf('prodDtlsId')];
			$prodn_arr[$row[csf('job_dtls_id')]]['production_qty']=$row[csf('production_qty')];
			$prodn_arr[$row[csf('job_dtls_id')]]['qty_reel']=$row[csf('qty_reel')];
			$prodn_arr[$row[csf('job_dtls_id')]]['total_head']=$row[csf('total_head')];
			$prodn_arr[$row[csf('job_dtls_id')]]['reject_qty']=$row[csf('reject_qty')];
			$prodn_arr[$row[csf('job_dtls_id')]]['remarks']=$row[csf('remarks')];
			$prodn_arr[$row[csf('job_dtls_id')]]['material_color_id']=$row[csf('material_color_id')];
			$prodn_arr[$row[csf('job_dtls_id')]]['item_description']=$row[csf('item_description')];
			$prodn_arr[$row[csf('job_dtls_id')]]['sub_section']=$jobDtls_arr[$row[csf('job_dtls_id')]]['sub_section'];
			$prodn_arr[$row[csf('job_dtls_id')]]['material_color']=$jobDtls_arr[$row[csf('job_dtls_id')]]['material_color'];
			$prodn_arr[$row[csf('job_dtls_id')]]['qc_qty']=$row[csf('qc_qty')];
			$prodn_jobDtls_arr[]=$row[csf('job_dtls_id')];
			//$jobDtls_arr[$row[csf('id')]]['material_color']=$row[csf('material_color')];
		}
		unset($productionReasult); 
		
		$prod_brk_sql= "select a.job_dtls_id, a.receive_dtls_id, a.break_id, b.id as prodBrkId, b.qc_pass_qty,b.reject_qty,b.remarks,b.buyer_po_no,b.buyer_po_id,c.received_id from trims_production_dtls a,trims_prod_order_breakdown b, trims_production_mst c where a.mst_id=c.id and a.id=b.mst_id and a.mst_id=$data[5] and a.status_active=1 and a.status_active=1";
			//$sub_section=$row[csf('sub_section')];
		$prodBrkReasult =sql_select($prod_brk_sql);  $prodn_brk_arr=array();
		foreach($prodBrkReasult as $row){
			$prodn_brk_arr[$row[csf('job_dtls_id')]]['hdnDtlsdata'].=$row[csf('buyer_po_no')].'_'.$row[csf('qc_pass_qty')].'_'.$row[csf('reject_qty')].'_'.$row[csf('remarks')].'_'.$row[csf('break_id')].'_'.$row[csf('receive_dtls_id')].'_'.$row[csf('prodBrkId')].'_'.$row[csf('received_id')].'_'.$row[csf('buyer_po_id')]."***";
		}
		unset($prodBrkReasult);
	}

	$prod_result =sql_select("select job_dtls_id,sum(production_qty) as production_qty, sum(qc_qty) as qc_qty,sum(reject_qty) as  reject_qty  from trims_production_dtls  where status_active=1 and is_deleted=0  group by job_dtls_id");
	$cumelative_arr=array();
	foreach($prod_result as $row){
		$cumelative_arr[$row[csf('job_dtls_id')]]['cum']=$row[csf('production_qty')];
		$cumelative_arr[$row[csf('job_dtls_id')]]['qc']=$row[csf('qc_qty')];
		$cumelative_arr[$row[csf('job_dtls_id')]]['reject_qty']=$row[csf('reject_qty')];
	}
	unset($prod_result);

	$delevery_qty_trims_arr=array();
	$pre_sql ="Select break_down_details_id, sum(delevery_qty) as delevery_qty  from trims_delivery_dtls where status_active=1 and is_deleted=0 group by break_down_details_id";
	$pre_sql_res=sql_select($pre_sql);
	foreach ($pre_sql_res as $row){
		$delevery_qty_trims_arr[$row[csf("break_down_details_id")]]['delevery_qty']=$row[csf("delevery_qty")];
	}
	unset($pre_sql_res);

	

	//echo "<pre>";
	//print_r($cumelative_arr);
	$conv_fac_arr=return_library_array( "select id,conv_factor from trims_job_card_dtls  where status_active=1 and is_deleted=0 group by id,conv_factor", "id", "conv_factor"  );
	$sql= "select a.id, a.mst_id, a.job_no_mst, a.booking_dtls_id, a.receive_dtls_id , a.book_con_dtls_id, a.break_id, a.buyer_po_no, a.buyer_style_ref, a.item_description, a.gmts_color_id, a.gmts_size_id, a.color_id, a.size_id, a.uom, a.job_quantity,  a.impression,a.material_color,a.sub_section,a.item_group_id from trims_job_card_dtls a where a.status_active=1 and a.mst_id=$data[1]";
	$data_array=sql_select($sql);
	$tblRow=0; $break_qty_sum_arr=array(); 
	foreach($data_array as $row){
		$cum_del_qty=0; $data_break=''; $cumelative_rejqty=''; $cumelative_qty=''; $jobDtls_id='';
		if(in_array($row[csf('id')], $prodn_jobDtls_arr)){
			$machine_id=$prodn_arr[$row[csf('id')]]['machine_id'];
			$prod_time=$prodn_arr[$row[csf('id')]]['prod_time'];
			$jobDtls_id=$prodn_arr[$row[csf('id')]]['job_dtls_id'];
			$prodDtls_id=$prodn_arr[$row[csf('id')]]['id'];
			$production_qty=$prodn_arr[$row[csf('id')]]['production_qty'];
			$qty_reel=$prodn_arr[$row[csf('id')]]['qty_reel'];
			$total_head=$prodn_arr[$row[csf('id')]]['total_head'];
			$reject_qty=$prodn_arr[$row[csf('id')]]['reject_qty'];
			$remarks=$prodn_arr[$row[csf('id')]]['remarks'];
			$material_color_id=$prodn_arr[$row[csf('id')]]['material_color_id'];
			$item_description=$prodn_arr[$row[csf('id')]]['item_description'];
			$item=$prodn_arr[$row[csf('id')]]['item_description'];
			$sub_section=$prodn_arr[$row[csf('id')]]['sub_section'];
			$material_color=$prodn_arr[$row[csf('id')]]['material_color'];
			$qc_qty=$prodn_arr[$row[csf('id')]]['qc_qty'];

			//echo $qc_qty.'==';
			$data_break=chop($prodn_brk_arr[$row[csf('id')]]['hdnDtlsdata'],'***');
			$qty=$qc_qty+$reject_qty;
			$break_ids=explode(",",$row[csf("break_id")]);
			
			for($i=0; $i<=count($break_ids);$i++){
				$cum_del_qty +=($delevery_qty_trims_arr[$break_ids[$i]]['delevery_qty']);
			}
			$cum_del_qty=$cum_del_qty*$conv_fac_arr[$jobDtls_id];
		}else{
			$machine_id=$prod_time=$material_color_id="0";
			$jobDtls_id=$row[csf('id')];
			$prodDtls_id=$qty=$qty_reel=$total_head=$comp_prod=$reject_qty=$remarks=$qc_qty='';
			$material_color=$row[csf('material_color')];
			$item=$row[csf('item_description')];
			$sub_section=$row[csf('sub_section')];
			$break_ids=explode(",",$row[csf("break_id")]);
		}
		$rcvDtls_id=$row[csf('receive_dtls_id')];
		$bookDtls_id=$row[csf('booking_dtls_id')];
		$bookConsDtls_id=$row[csf('book_con_dtls_id')];
		$break_id=$row[csf('break_id')];
		$job_qty=$jobDtls_arr[$jobDtls_id]['job_quantity'];

		if($over_ctrl_var_sts!=0 || $over_ctrl_var_sts!='')
		{
			$jobQtyWithOver =(($job_qty*$over_ctrl_var_sts)/100)+$job_qty;
		}
		else{
			$jobQtyWithOver=$job_qty;
		}
		//echo $over_ctrl_var_sts.'='.$jobQtyWithOver.'='.$job_qty; 
		$cumelative_rejqty=$cumelative_arr[$jobDtls_id]['reject_qty'];
		
		if($prodDtls_id!=""){
			//$curent_qty=$qc_qty+$reject_qty;
			$curent_qty=$qc_qty;
			//$cumelative_qty=($cumelative_arr[$jobDtls_id]['qc']+$cumelative_rejqty)-$curent_qty;
			$cumelative_qty=($cumelative_arr[$jobDtls_id]['qc'])-$curent_qty;
		}else{
			//$cumelative_qty=$cumelative_arr[$jobDtls_id]['qc']+$cumelative_rejqty;
			$cumelative_qty=$cumelative_arr[$jobDtls_id]['qc'];
		}
		//$job_qty=
		$total_qc_qty=$cumelative_arr[$jobDtls_id]['qc'];
		$qc_bal=$jobQtyWithOver-$total_qc_qty;
		if(!in_array($row[csf('id')], $prodn_jobDtls_arr)){
			if($prodDtls_id!=""){
				$qc_bal=$jobQtyWithOver-$total_qc_qty;
				$bal=$jobQtyWithOver-($cumelative_qty)-$curent_qty;
				$hideBal=$bal;
				$hid_cumelative_qty=$cumelative_qty;
			}else{
				$qc_bal=$jobQtyWithOver-$total_qc_qty;
				$bal=$jobQtyWithOver-($cumelative_qty);
				$hideBal=$bal;
				$hid_cumelative_qty=$cumelative_qty;
			}
		}else{
			if($prodDtls_id!=""){
				$qc_bal=$jobQtyWithOver-$total_qc_qty+$qc_qty;
				$bal=$jobQtyWithOver-($cumelative_qty)-$curent_qty;
				$hideBal=$jobQtyWithOver-($cumelative_qty)+$qc_qty;
				$hid_cumelative_qty=$cumelative_qty-$qc_qty;
			}else{
				$qc_bal=$jobQtyWithOver-$total_qc_qty+$qc_qty;
				$bal=$jobQtyWithOver-($cumelative_qty);
				$hideBal=$jobQtyWithOver-($cumelative_qty)+$qc_qty;
				$hid_cumelative_qty=$cumelative_qty-$qc_qty;
			}
		}

		$tblRow++; $materialColor=array();
		$material_colors=explode('__',$material_color);
		if($material_colors[0]!='' && $material_colors[0]!=0){
			for($j=0; $j<count($material_colors); $j++){
				if($material_colors[$j]!=''){
					$materialColor[$material_colors[$j]]=$color_library[$material_colors[$j]];
				}
			}
		}
		$machine_id_array=array();
		$sql = sql_select("select listagg(id,',') within group (order by id ) as id ,machine_group  from lib_machine_name where category_id=25 and machine_group='$data[7]'  group by machine_group"); 
		foreach($sql as $result){
			$machine_id_array[]=$result[csf("id")]; 
		}
		$machine_Ids=implode(",",$machine_id_array);
		if ($data[3] == 0 || $data[3] == "") $company_cond = ""; else $company_cond = " and company_id=$data[3]";
		if ($data[6] == 0 || $data[6] == "") $location_cond = ""; else $location_cond = " and location_id=$data[6]";
		if ($data[8] == 0 || $data[8] == "") $floor_cond = ""; else $floor_cond = " and floor_id=$data[8]";
		if ($machine_Ids == 0 || $machine_Ids == "") $machine_no_cond = ""; else $machine_no_cond = " and id in ($machine_Ids)";
		$machine_dropdown="";
		if(empty($data[7])==""){
			$sql_machine="select id, machine_no  from lib_machine_name where status_active=1 and is_deleted=0 $company_cond  $location_cond $machine_no_cond $floor_cond and category_id=25  and is_locked=0 $floor_cond order by seq_no";
		}
		//echo $jobDtls_id.'++';
		?>
		<tr id="row_<? echo $tblRow; ?>">
			<td><? echo create_drop_down( "cboItemGroup_".$tblRow, 100, "select id, item_name from lib_item_group where item_category=4 and status_active=1","id,item_name", 1, "-- Select --",$row[csf('item_group_id')], "",1,'','','','','','',"cboItemGroup[]"); ?>	</td>
			<td title="<? echo  $data_break ; ?>"><input id="txtItem_<? echo $tblRow; ?>" name="txtItem[]" type="text" class="text_boxes" style="width:87px" placeholder="Display" value="<? echo $item ?>" readonly title="<? echo $item ?>" />
			</td>
			<td>
				<input id="txtgmtscolor_<? echo $tblRow; ?>" name="txtgmtscolor[]" type="text" value="<? echo $color_library[$row[csf('gmts_color_id')]] ?>" class="text_boxes" style="width:57px" placeholder="Display" disabled/>
				<input id="txtgmtscolorId_<? echo $tblRow; ?>" name="txtgmtscolorId[]" type="hidden" value="<? echo $row[csf('gmts_color_id')] ?>" class="text_boxes" style="width:57px" placeholder="Display" disabled/>
			</td>
			<td>
				<input id="txtgmtssize_<? echo $tblRow; ?>" name="txtgmtssize[]" type="text" value="<? echo $size_arr[$row[csf('gmts_size_id')]] ?>"  class="text_boxes" style="width:57px" placeholder="Display" disabled/>
				<input id="txtgmtssizeId_<? echo $tblRow; ?>" name="txtgmtssizeId[]" type="hidden" value="<? echo $row[csf('gmts_size_id')] ?>"  class="text_boxes" style="width:57px" placeholder="Display" disabled/>
			</td>
			<td>
				<input id="txtcolor_<? echo $tblRow; ?>" name="txtcolor[]" type="text" value="<? echo $color_library[$row[csf('color_id')]] ?>" class="text_boxes" style="width:57px" placeholder="Display" disabled/>
				<input id="txtcolorID_<? echo $tblRow; ?>" name="txtcolorID[]" type="hidden" value="<? echo $row[csf('color_id')] ?>" class="text_boxes" style="width:57px" placeholder="Display" disabled/>
			</td>
			<td>
				<input id="txtsize_<? echo $tblRow; ?>" name="txtsize[]" type="text" value="<? echo $size_arr[$row[csf('size_id')]] ?>"  class="text_boxes" style="width:57px" placeholder="Display" disabled/>
				<input id="txtsizeID_<? echo $tblRow; ?>" name="txtsizeID[]" type="hidden" value="<? echo $row[csf('size_id')] ?>"  class="text_boxes" style="width:57px" placeholder="Display" disabled/>
			</td>
			<td id="subSectionTd_<? echo $tblRow; ?>"><? echo create_drop_down( "cboSubSection_".$tblRow, 70, $trims_sub_section,"", 1, "-- Select Sub-Section --",$sub_section,'',1,'','','','','','',"cboSubSection[]"); ?></td>
			<td><? echo create_drop_down( "cboUom_<? echo $tblRow; ?>", 60, $unit_of_measurement,"", 1, "-- Select --",$row[csf('uom')],1, 1,'','','','','','',"cboUom[]"); ?>	</td>
			<td id="machinetd_<? echo $tblRow; ?>">
				<?
				echo create_drop_down("cboMachineName_".$tblRow, 80, $sql_machine, "id,machine_no", 1, "-- Select Machine --",$machine_id,"","",'','','','','','',"cboMachineName[]"); 
				?>
			</td>
			<td><input id="txtImpression_<? echo $tblRow; ?>" name="txtImpression[]" type="text" value="<? echo $row[csf('impression')]; ?>"  class="text_boxes" style="width:100px" placeholder="Display"/></td>
			<td><? echo create_drop_down( "cborawColor_<? echo $tblRow; ?>", 60, $materialColor,"", 1, "-- Select --",$material_color_id,1, 0,'','','','','','',"cborawColor[]"); ?></td>

			<td><input id="txtQtyReel_<? echo $tblRow; ?>" name="txtQtyReel[]" class="text_boxes_numeric" type="text"  style="width:47px" placeholder="Display"  value="<? echo $qty_reel; ?>" /></td>
			<td><input id="txtTotalHead_<? echo $tblRow; ?>" name="txtTotalHead[]" class="text_boxes_numeric" type="text"  style="width:47px" placeholder="Display" value="<? echo $total_head; ?>" /></td>

			<td><input id="txtOrdQty_<? echo $tblRow; ?>" name="txtOrdQty[]" class="text_boxes_numeric" type="text"  style="width:47px" placeholder="Display" readonly value="<? echo number_format($job_qty,4,".",""); ?>" /></td>

			<td><input id="txtQcPassQty_<? echo $tblRow; ?>" name="txtQcPassQty[]" class="text_boxes_numeric" type="text"  style="width:47px"  value="<? echo number_format($qc_qty,4,".",""); ?>"  onDblClick="openmypage_qc_qnty(<? echo $tblRow; ?>,<? echo $jobDtls_id; ?>)" placeholder="Write/Click To Search" readonly />
				<input id="txtQcBalQty_<? echo $tblRow; ?>" name="txtQcBalQty[]" class="text_boxes_numeric" type="hidden"  style="width:47px" value="<? echo $qc_bal; ?>"  /> 
			</td>
			<td><input id="txtRejectQty_<? echo $tblRow; ?>" name="txtRejectQty[]" class="text_boxes_numeric" type="text"  style="width:47px" placeholder="Write" value="<? echo $reject_qty; ?>"    readonly/></td>
			<td><input id="txtProdQty_<? echo $tblRow; ?>" name="txtProdQty[]" class="text_boxes_numeric" type="text"  style="width:47px" placeholder="Display" value="<? echo number_format($qty,4,".",""); ?>" readonly /></td>

			<td><input id="txtCompProd_<? echo $tblRow; ?>" name="txtCompProd[]" class="text_boxes_numeric" type="text"  style="width:47px" placeholder="Display" readonly value="<? echo number_format($cumelative_qty,4,".",""); ?>" />
				<input id="hidCompProd_<? echo $tblRow; ?>" name="hidCompProd[]" class="text_boxes_numeric" type="hidden"  style="width:47px" placeholder="Display" readonly value="<? echo $hid_cumelative_qty; ?>" /></td>
			<td><input id="txtBalQty_<? echo $tblRow; ?>" name="txtBalQty[]" class="text_boxes_numeric" type="text"  style="width:47px" placeholder="Display" readonly value="<? echo number_format($bal,4,".",""); ?>" />
				<input id="hidBalQty_<? echo $tblRow; ?>" name="hidBalQty[]" class="text_boxes_numeric" type="hidden"  style="width:47px" placeholder="Display" readonly value="<? echo $hideBal; ?>" /></td>
			<td><?
				$production_time=array(1=>"G.Hour",2=>"OT.Hour");
				echo create_drop_down( "cboProdTime_".$tblRow, 70, $production_time,"", 1, "-- Select --",$prod_time,1,0,'','','','','','',"cboProdTime[]"); ?>	</td>
			<td><input id="txtRemarks_<? echo $tblRow; ?>" name="txtRemarks[]" class="text_boxes" type="text" value="<? echo $remarks; ?>"   style="width:60px"/></td>
				<input type="hidden" id="hdnDtlsdata_<? echo $tblRow; ?>" name="hdnDtlsdata[]" value="<?=$data_break; ?>">
			 	<input type="hidden" id="txtDeletedId_<? echo $tblRow; ?>" name="txtDeletedId[]" value="">
				<input type="hidden" name="hdnDtlsUpdateId[]" id="hdnDtlsUpdateId_<? echo $tblRow; ?>" value="<? echo $prodDtls_id; ?>">
				<input type="hidden" name="hdnjobDtlsId[]" id="hdnjobDtlsId_<? echo $tblRow; ?>" value="<? echo $jobDtls_id; ?>">
				<input type="hidden" name="hdnRcvDtlsId[]" id="hdnRcvDtlsId_<? echo $tblRow; ?>" value="<? echo $rcvDtls_id; ?>">
				<input type="hidden" name="hdnbookingDtlsId[]" id="hdnbookingDtlsId_<? echo $tblRow; ?>" value="<? echo $bookDtls_id; ?>">
				<input type="hidden" name="hdnbookingConDtlsId[]" id="hdnbookingConDtlsId_<? echo $tblRow; ?>" value="<? echo $bookConsDtls_id; ?>">
				<input type="hidden" name="hdnBreakIDs[]" id="hdnBreakIDs_<? echo $tblRow; ?>" value="<? echo $break_id; ?>">
				<input type="hidden" name="hdnCumDelQty[]" id="hdnCumDelQty_<? echo $tblRow; ?>" value="<? echo $cum_del_qty; ?>">
			</td>
			<td style="display: none;"><? echo create_drop_down( "cbosubProcess_<? echo $tblRow; ?>", 60, $unit_of_measurement,"", 1, "-- Select --",2,1, 1,'','','','','','',"cbosubProcess[]"); ?>	</td>
			<td style="display: none;"><input id="txtMcNo_<? echo $tblRow; ?>" name="txtMcNo[]" type="text" class="text_boxes" style="width:100px;" placeholder="Browse" />
				<input id="txtMcNoID_<? echo $tblRow; ?>" name="txtMcNoID[]" type="hidden" class="text_boxes" style="width:70px" readonly />
			</td>
		</tr>                     
	<?
	}
	exit();
}

if( $action=='order_dtls_list_view' ) 
{
//echo $data; die;
$data=explode('**',$data);
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
$size_arr=return_library_array( "select id,size_name from lib_size",'id','size_name');
$tblRow=0; $buyer_po_arr=array();


$jobDtls_result =sql_select("select  id, sub_section,material_color,job_quantity from trims_job_card_dtls where status_active=1");
$jobDtls_arr=array();
foreach($jobDtls_result as $row)
{
	$jobDtls_arr[$row[csf('id')]]['sub_section']=$row[csf('sub_section')];
	$jobDtls_arr[$row[csf('id')]]['material_color']=$row[csf('material_color')];
	$jobDtls_arr[$row[csf('id')]]['job_quantity']=$row[csf('job_quantity')];
}

if($data[5]!='')
{
	$prod_sql= "select a.id as prodDtlsId, a.mst_id, a.machine_id, a.item_description, a.gmts_color_id, a.gmts_size_id, a.color_id, a.size_id, a.uom,  a.impression, a.material_color_id, a.qty_reel,  a.total_head, a.qc_qty, a.production_qty, a.comp_prod, a.reject_qty, a.prod_time, a.remarks, a.booking_dtls_id, a.receive_dtls_id, a.job_dtls_id, a.book_con_dtls_id, a.break_id from trims_production_dtls a where a.mst_id=$data[5]";
		//$sub_section=$row[csf('sub_section')];
	$productionReasult =sql_select($prod_sql);
	$prodn_arr=array(); $prodn_jobDtls_arr=array();
	foreach($productionReasult as $row)
	{
		$prodn_arr[$row[csf('job_dtls_id')]]['machine_id']=$row[csf('machine_id')];
		$prodn_arr[$row[csf('job_dtls_id')]]['prod_time']=$row[csf('prod_time')];
		$prodn_arr[$row[csf('job_dtls_id')]]['job_dtls_id']=$row[csf('job_dtls_id')];
		$prodn_arr[$row[csf('job_dtls_id')]]['id']=$row[csf('prodDtlsId')];
		$prodn_arr[$row[csf('job_dtls_id')]]['production_qty']=$row[csf('production_qty')];
		$prodn_arr[$row[csf('job_dtls_id')]]['qty_reel']=$row[csf('qty_reel')];
		$prodn_arr[$row[csf('job_dtls_id')]]['total_head']=$row[csf('total_head')];
		$prodn_arr[$row[csf('job_dtls_id')]]['reject_qty']=$row[csf('reject_qty')];
		$prodn_arr[$row[csf('job_dtls_id')]]['remarks']=$row[csf('remarks')];
		$prodn_arr[$row[csf('job_dtls_id')]]['material_color_id']=$row[csf('material_color_id')];
		$prodn_arr[$row[csf('job_dtls_id')]]['item_description']=$row[csf('item_description')];
		$prodn_arr[$row[csf('job_dtls_id')]]['sub_section']=$jobDtls_arr[$row[csf('job_dtls_id')]]['sub_section'];
		$prodn_arr[$row[csf('job_dtls_id')]]['material_color']=$jobDtls_arr[$row[csf('job_dtls_id')]]['material_color'];
		$prodn_arr[$row[csf('job_dtls_id')]]['qc_qty']=$row[csf('qc_qty')];
		$prodn_jobDtls_arr[]=$row[csf('job_dtls_id')];
		//$jobDtls_arr[$row[csf('id')]]['material_color']=$row[csf('material_color')];
	}
}
/*$sqlOrdDtls_result =sql_select("select  id, order_quantity, buyer_buyer from subcon_ord_dtls where mst_id=$data[4]");
$ordDtls_arr=array();
foreach($sqlOrdDtls_result as $row)
{
	$ordDtls_arr[$row[csf('id')]]['order_quantity']=$row[csf('order_quantity')];
}*/



$prod_result =sql_select("select job_dtls_id,sum(production_qty) as production_qty, sum(qc_qty) as qc_qty from trims_production_dtls  where status_active=1 and is_deleted=0 group by job_dtls_id");
$cumelative_arr=array();
foreach($prod_result as $row)
{
	$cumelative_arr[$row[csf('job_dtls_id')]]['cum']=$row[csf('production_qty')];
	$cumelative_arr[$row[csf('job_dtls_id')]]['qc']=$row[csf('qc_qty')];
	//$jobDtls_arr[$row[csf('id')]]['material_color']=$row[csf('material_color')];
}

$delevery_qty_trims_arr=array();
$pre_sql ="Select break_down_details_id, sum(delevery_qty) as delevery_qty  from trims_delivery_dtls where status_active=1 and is_deleted=0 group by break_down_details_id";
$pre_sql_res=sql_select($pre_sql);
foreach ($pre_sql_res as $row)
{
	$delevery_qty_trims_arr[$row[csf("break_down_details_id")]]['delevery_qty']=$row[csf("delevery_qty")];
	
}
unset($pre_sql_res);
//echo "<pre>";
//print_r

//$cumelative_arr=return_library_array( "select job_dtls_id,sum(production_qty) as production_qty from trims_production_dtls  where status_active=1 and is_deleted=0 group by job_dtls_id", "job_dtls_id", "pro
$conv_fac_arr=return_library_array( "select id,conv_factor from trims_job_card_dtls  where status_active=1 and is_deleted=0 group by id,conv_factor", "id", "conv_factor"  );
/*echo "<pre>";
print_r($cumelative_arr);*/
$sql= "select a.id, a.mst_id, a.job_no_mst, a.booking_dtls_id, a.receive_dtls_id , a.book_con_dtls_id, a.break_id, a.buyer_po_no, a.buyer_style_ref, a.item_description, a.gmts_color_id, a.gmts_size_id, a.color_id, a.size_id, a.uom, a.job_quantity,  a.impression,a.material_color,a.sub_section,a.item_group_id from trims_job_card_dtls a where a.status_active=1 and a.mst_id=$data[1]";
$data_array=sql_select($sql); $tblRow=0; $break_qty_sum_arr=array(); 
foreach($data_array as $row)
{
	$cum_del_qty=0;
	if(in_array($row[csf('id')], $prodn_jobDtls_arr))
	{
		//echo "==+"; 
		$machine_id=$prodn_arr[$row[csf('id')]]['machine_id'];
		$prod_time=$prodn_arr[$row[csf('id')]]['prod_time'];
		$jobDtls_id=$prodn_arr[$row[csf('id')]]['job_dtls_id'];
		$prodDtls_id=$prodn_arr[$row[csf('id')]]['id'];
		$production_qty=$prodn_arr[$row[csf('id')]]['production_qty'];
		$qty_reel=$prodn_arr[$row[csf('id')]]['qty_reel'];
		$total_head=$prodn_arr[$row[csf('id')]]['total_head'];
		$reject_qty=$prodn_arr[$row[csf('id')]]['reject_qty'];
		$remarks=$prodn_arr[$row[csf('id')]]['remarks'];
		$material_color_id=$prodn_arr[$row[csf('id')]]['material_color_id'];
		$item_description=$prodn_arr[$row[csf('id')]]['item_description'];
		$item=$prodn_arr[$row[csf('id')]]['item_description'];
		$sub_section=$prodn_arr[$row[csf('id')]]['sub_section'];
		$material_color=$prodn_arr[$row[csf('id')]]['material_color'];
		$qc_qty=$prodn_arr[$row[csf('id')]]['qc_qty'];
		$qty=$qc_qty+$reject_qty;

		$break_ids=explode(",",$row[csf("break_id")]);
		for($i=0; $i<=count($break_ids);$i++)
		{
			//$break_qty_sum_arr[$break_ids[$i]]['cal_qty']=($delevery_qty_trims_arr[$break_ids[$i]]['delevery_qty']);
			$cum_del_qty +=($delevery_qty_trims_arr[$break_ids[$i]]['delevery_qty']);
		}
		$cum_del_qty=$cum_del_qty*$conv_fac_arr[$jobDtls_id];
		/*$convertedBQty= $break_qty_sum_arr[$row[csf('break_id')]]['cal_qty']/$trims_groups_arr[$row[csf("id")]]['booked_conv_fac'];
		$delevery_qty_trims_arr[$row[csf("break_id")]]['delevery_qty']*/
	}
	else
	{
		$machine_id="0";
		$prod_time="0";
		$jobDtls_id=$row[csf('id')];
		$prodDtls_id='';
		$qty='';
		$qty_reel='';
		$total_head='';
		$comp_prod='';
		$reject_qty='';
		$remarks='';
		$material_color=$row[csf('material_color')];
		$material_color_id=0;
		$item=$row[csf('item_description')];
		$sub_section=$row[csf('sub_section')];
		$qc_qty='';
		$break_ids=explode(",",$row[csf("break_id")]);
		/*for($i=0; $i<=count($break_ids);$i++)
		{
			echo $break_ids[$i]."==";
			//$break_qty_sum_arr[$break_ids[$i]]['cal_qty']=($delevery_qty_trims_arr[$break_ids[$i]]['delevery_qty']);
			$cum_del_qty +=($delevery_qty_trims_arr[$break_ids[$i]]['delevery_qty']);
		}*/
	}
	
	//$cumalitive_qty=0;
	//echo $prodDtls_id."=="; 
	$rcvDtls_id=$row[csf('receive_dtls_id')];
	$bookDtls_id=$row[csf('booking_dtls_id')];
	$bookConsDtls_id=$row[csf('book_con_dtls_id')];
	$break_id=$row[csf('break_id')];
	$job_qty=$jobDtls_arr[$jobDtls_id]['job_quantity'];
	$cumelative_qty=$cumelative_arr[$jobDtls_id]['qc'];
	$total_qc_qty=$cumelative_arr[$jobDtls_id]['qc'];
	$qc_bal=$job_qty-$total_qc_qty;
	$rcvDtls_id=implode(",",array_unique(explode(",",$rcvDtls_id)));
	$bookDtls_id=implode(",",array_unique(explode(",",$bookDtls_id)));
	$bookConsDtls_id=implode(",",array_unique(explode(",",$bookConsDtls_id)));
	$break_id=implode(",",array_unique(explode(",",$break_id)));
	/*$receive_dtls_ids=array_unique(explode(",",$rcvDtls_id));
	$order_quantity='';  $receiveDtlsId_arr=array(); //$buyer_buyer=''; 
	for($j=0; $j<count($receive_dtls_ids); $j++)
	{
		if (!in_array($receive_dtls_ids[$j], $receiveDtlsId_arr))
		{
			$order_quantity+=$ordDtls_arr[$receive_dtls_ids[$j]]['order_quantity'];
			$receiveDtlsId_arr[]=$receive_dtls_ids[$j];
		}
	}*/
	if(!in_array($row[csf('id')], $prodn_jobDtls_arr))
	{
		$qc_bal=$job_qty-$total_qc_qty;
		$bal=$job_qty-($cumelative_qty);
		$hideBal=$bal;
		$hid_cumelative_qty=$cumelative_qty;
	}
	else
	{
		$qc_bal=$job_qty-$total_qc_qty+$qc_qty;
		$bal=$job_qty-($cumelative_qty);
		$hideBal=$job_qty-($cumelative_qty)+$qc_qty;
		$hid_cumelative_qty=$cumelative_qty-$qc_qty;
	}

	$tblRow++; $materialColor=array();
	$material_colors=explode('__',$material_color);
	if($material_colors[0]!='' && $material_colors[0]!=0)
	{
		for($j=0; $j<count($material_colors); $j++)
		{
			if($material_colors[$j]!='')
			{
				$materialColor[$material_colors[$j]]=$color_library[$material_colors[$j]];
			}
		}
	}
	//echo $data_break."mahbub";
	//echo "<pre>";
	//print_r($data_dreak_arr);
	?>
	<tr id="row_<? echo $tblRow; ?>">
		<td><? echo create_drop_down( "cboItemGroup_".$tblRow, 100, "select id, item_name from lib_item_group where item_category=4 and status_active=1","id,item_name", 1, "-- Select --",$row[csf('item_group_id')], "",1,'','','','','','',"cboItemGroup[]"); ?>	</td>
		<td title="<? echo  $data_break ; ?>"><input id="txtItem_<? echo $tblRow; ?>" name="txtItem[]" type="text" class="text_boxes" style="width:87px" placeholder="Display" value="<? echo $item ?>" readonly title="<? echo $item ?>" /></td>
		<td>
			<input id="txtgmtscolor_<? echo $tblRow; ?>" name="txtgmtscolor[]" type="text" value="<? echo $color_library[$row[csf('gmts_color_id')]] ?>" class="text_boxes" style="width:57px" placeholder="Display" disabled/>
			<input id="txtgmtscolorId_<? echo $tblRow; ?>" name="txtgmtscolorId[]" type="hidden" value="<? echo $row[csf('gmts_color_id')] ?>" class="text_boxes" style="width:57px" placeholder="Display" disabled/>
		</td>
		<td>
			<input id="txtgmtssize_<? echo $tblRow; ?>" name="txtgmtssize[]" type="text" value="<? echo $size_arr[$row[csf('gmts_size_id')]] ?>"  class="text_boxes" style="width:77px" placeholder="Display" disabled/>
			<input id="txtgmtssizeId_<? echo $tblRow; ?>" name="txtgmtssizeId[]" type="hidden" value="<? echo $row[csf('gmts_size_id')] ?>"  class="text_boxes" style="width:77px" placeholder="Display" disabled/>
		</td>
		<td>
			<input id="txtcolor_<? echo $tblRow; ?>" name="txtcolor[]" type="text" value="<? echo $color_library[$row[csf('color_id')]] ?>" class="text_boxes" style="width:57px" placeholder="Display" disabled/>
			<input id="txtcolorID_<? echo $tblRow; ?>" name="txtcolorID[]" type="hidden" value="<? echo $row[csf('color_id')] ?>" class="text_boxes" style="width:57px" placeholder="Display" disabled/>
		</td>
		<td>
			<input id="txtsize_<? echo $tblRow; ?>" name="txtsize[]" type="text" value="<? echo $size_arr[$row[csf('size_id')]] ?>"  class="text_boxes" style="width:57px" placeholder="Display" disabled/>
			<input id="txtsizeID_<? echo $tblRow; ?>" name="txtsizeID[]" type="hidden" value="<? echo $row[csf('size_id')] ?>"  class="text_boxes" style="width:57px" placeholder="Display" disabled/>
		</td>
		<td id="subSectionTd_<? echo $tblRow; ?>"><? echo create_drop_down( "cboSubSection_".$tblRow, 70, $trims_sub_section,"", 1, "-- Select Sub-Section --",$sub_section,'',1,'','','','','','',"cboSubSection[]"); ?></td>
		<td><? echo create_drop_down( "cboUom_<? echo $tblRow; ?>", 40, $unit_of_measurement,"", 1, "-- Select --",$row[csf('uom')],1, 1,'','','','','','',"cboUom[]"); ?>	</td>
        <td id="machinetd_<? echo $tblRow; ?>">
				<?
				echo create_drop_down("cboMachineName_".$tblRow, 80, $blank_array, "", 1, "-- Select Machine --",$machine_id,"","",'','','','','','',"cboMachineName[]"); 
				?>
			</td>
		<td><input id="txtImpression_<? echo $tblRow; ?>" name="txtImpression[]" type="text" value="<? echo $row[csf('impression')]; ?>"  class="text_boxes" style="width:100px" placeholder="Display"/></td>
		<td><? echo create_drop_down( "cborawColor_<? echo $tblRow; ?>", 60, $materialColor,"", 1, "-- Select --",$material_color_id,1, 0,'','','','','','',"cborawColor[]"); ?></td>

		<td><input id="txtQtyReel_<? echo $tblRow; ?>" name="txtQtyReel[]" class="text_boxes_numeric" type="text"  style="width:47px" placeholder="Display"  value="<? echo $qty_reel; ?>" /></td>
		<td><input id="txtTotalHead_<? echo $tblRow; ?>" name="txtTotalHead[]" class="text_boxes_numeric" type="text"  style="width:47px" placeholder="Display" value="<? echo $total_head; ?>" /></td>

		<td><input id="txtOrdQty_<? echo $tblRow; ?>" name="txtOrdQty[]" class="text_boxes_numeric" type="text"  style="width:47px" placeholder="Display" readonly value="<? echo number_format($job_qty,4,".",""); ?>" /></td>

		<td><input id="txtQcPassQty_<? echo $tblRow; ?>" name="txtQcPassQty[]" class="text_boxes_numeric" type="text"  style="width:47px" onBlur="cal_values(<? echo $tblRow; ?>);" value="<? echo number_format($qc_qty,4,".",""); ?>" placeholder="Write" />
			<input id="txtQcBalQty_<? echo $tblRow; ?>" name="txtQcBalQty[]" class="text_boxes_numeric" type="hidden"  style="width:47px" value="<? echo $qc_bal; ?>"  />
		</td>
		<td><input id="txtRejectQty_<? echo $tblRow; ?>" name="txtRejectQty[]" class="text_boxes_numeric" type="text"  style="width:47px" placeholder="Write" value="<? echo $reject_qty; ?>"  onBlur="cal_values(<? echo $tblRow; ?>);" /></td>
		<td><input id="txtProdQty_<? echo $tblRow; ?>" name="txtProdQty[]" class="text_boxes_numeric" type="text"  style="width:47px" placeholder="Display" value="<? echo number_format($qty,4,".",""); ?>" readonly /></td>

		<td><input id="txtCompProd_<? echo $tblRow; ?>" name="txtCompProd[]" class="text_boxes_numeric" type="text"  style="width:47px" placeholder="Display" readonly value="<? echo number_format($cumelative_qty,4,".",""); ?>" />
			<input id="hidCompProd_<? echo $tblRow; ?>" name="hidCompProd[]" class="text_boxes_numeric" type="hidden"  style="width:47px" placeholder="Display" readonly value="<? echo $hid_cumelative_qty; ?>" /></td>
		<td><input id="txtBalQty_<? echo $tblRow; ?>" name="txtBalQty[]" class="text_boxes_numeric" type="text"  style="width:47px" placeholder="Display" readonly value="<? echo number_format($bal,4,".",""); ?>" />
			<input id="hidBalQty_<? echo $tblRow; ?>" name="hidBalQty[]" class="text_boxes_numeric" type="hidden"  style="width:47px" placeholder="Display" readonly value="<? echo $hideBal; ?>" /></td>
		<td><?
			$production_time=array(1=>"G.Hour",2=>"OT.Hour");
			echo create_drop_down( "cboProdTime_".$tblRow, 70, $production_time,"", 1, "-- Select --",$prod_time,1,0,'','','','','','',"cboProdTime[]"); ?>	</td>
		<td><input id="txtRemarks_<? echo $tblRow; ?>" name="txtRemarks[]" class="text_boxes" type="text" value="<? echo $remarks; ?>"   style="width:60px"/></td>
			<input type="hidden" id="hdnDtlsdata_<? echo $tblRow; ?>" name="hdnDtlsdata[]" value="<?=$data_break; ?>">
		 <input type="hidden" id="txtDeletedId_<? echo $tblRow; ?>" name="txtDeletedId[]" value="">
			<input type="hidden" name="hdnDtlsUpdateId[]" id="hdnDtlsUpdateId_<? echo $tblRow; ?>" value="<? echo $prodDtls_id; ?>">
			<input type="hidden" name="hdnjobDtlsId[]" id="hdnjobDtlsId_<? echo $tblRow; ?>" value="<? echo $jobDtls_id; ?>">
			<input type="hidden" name="hdnRcvDtlsId[]" id="hdnRcvDtlsId_<? echo $tblRow; ?>" value="<? echo $rcvDtls_id; ?>">
			<input type="hidden" name="hdnbookingDtlsId[]" id="hdnbookingDtlsId_<? echo $tblRow; ?>" value="<? echo $bookDtls_id; ?>">
			<input type="hidden" name="hdnbookingConDtlsId[]" id="hdnbookingConDtlsId_<? echo $tblRow; ?>" value="<? echo $bookConsDtls_id; ?>">
			<input type="hidden" name="hdnBreakIDs[]" id="hdnBreakIDs_<? echo $tblRow; ?>" value="<? echo $break_id; ?>">
			<input type="hidden" name="hdnCumDelQty[]" id="hdnCumDelQty_<? echo $tblRow; ?>" value="<? echo $cum_del_qty; ?>">
		</td>
		<td style="display: none;"><? echo create_drop_down( "cbosubProcess_<? echo $tblRow; ?>", 60, $unit_of_measurement,"", 1, "-- Select --",2,1, 1,'','','','','','',"cbosubProcess[]"); ?>	</td>
		<td style="display: none;"><input id="txtMcNo_<? echo $tblRow; ?>" name="txtMcNo[]" type="text" class="text_boxes" style="width:100px;" placeholder="Browse" />
			<input id="txtMcNoID_<? echo $tblRow; ?>" name="txtMcNoID[]" type="hidden" class="text_boxes" style="width:70px" readonly />
		</td>
	</tr>                     
<?
}
exit();
}


if($action=="row_metarial_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	
	//echo $order_qty."nazim";
	?>
	<script>
		var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name ", "color_name" ), 0, -1); ?> ];
		var str_size = [<? echo substr(return_library_autocomplete( "select size_name from lib_size group by size_name ", "size_name" ), 0, -1); ?> ];
		
		function add_share_row( i ) 
		{
			//var row_num=$('#tbl_share_details_entry tbody tr').length-1;
			var row_num=$('#tbl_share_details_entry tbody tr').length;
			if (row_num!=i)
			{
				return false;
			}
			i++;
			$("#tbl_share_details_entry tbody tr:last").clone().find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { return name + i },
					'value': function(_, value) { return value }              
				});
			}).end().appendTo("#tbl_share_details_entry tbody");
			$('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_share_row("+i+");");
			$('#txtorderquantity_'+i).removeAttr("onKeyUp").attr("onKeyUp","sum_total_qnty("+i+");");
			$('#txtorderrate_'+i).removeAttr("onKeyUp").attr("onKeyUp","sum_total_qnty("+i+");");
			$('#txtcolor_'+i).removeAttr("disabled");
			$('#txtorderquantity_'+i).removeAttr("disabled");
			$('#decreaseset_'+i).removeAttr("disabled");			
			//$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+','+'"tbl_share_details_entry"'+");");
			$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
			$('#txtsize_'+i).val('');
			//$('#loss_'+i).val('');
			$('#hiddenid_'+i).val('');
			set_all_onclick();
			/*$("#txtcolor_"+i).autocomplete({
				source: str_color
			});
			$("#txtsize_"+i).autocomplete({
				source: str_size
			});*/
			//sum_total_qnty(i);
		}		
		
		function fn_deletebreak_down_tr(rowNo) 
		{ 
			var numRow = $('table#tbl_share_details_entry tbody tr').length; 
			if(numRow==rowNo && rowNo!=1)
			{
				var updateIdDtls=$('#hiddenid_'+rowNo).val();
				var txtDeletedId=$('#txtDeletedId').val();
				var selected_id='';
				if(updateIdDtls!='')
				{
					if(txtDeletedId=='') selected_id=updateIdDtls; else selected_id=txtDeletedId+','+updateIdDtls;
					$('#txtDeletedId').val( selected_id );
				}
				
				$('#tbl_share_details_entry tbody tr:last').remove();
			}
			else
			{
				return false;
			}
			//sum_total_qnty(rowNo);
		}

		function fnc_close()
		{
			var tot_row=$('#tbl_share_details_entry tbody tr').length;
			
			var data_break_down="";
			for(var i=1; i<=tot_row; i++)
			{
				/*if (form_validation('txtcolor_'+i+'*txtsize_'+i+'*txtorderquantity_'+i,'Color*Size*Quantity')==false)
				{
					return;
				}*/
				if($("#txtdescription_"+i).val()=="") $("#txtdescription_"+i).val(0)
				if($("#txtSpecification_"+i).val()=="") $("#txtSpecification_"+i).val(0);
				if($("#txtUnit_"+i).val()=="") $("#txtUnit_"+i).val(0);
				if($("#txtUnitPcs_"+i).val()=="") $("#txtUnitPcs_"+i).val(0);
				if($("#txtConsQty_"+i).val()=="") $("#txtConsQty_"+i).val(0);
				if($("#txtProcessLoss_"+i).val()=="") $("#txtProcessLoss_"+i).val(0);
				if($("#txtProcessLossQty_"+i).val()=="") $("#txtProcessLossQty_"+i).val(0);
				if($("#txtReqQty_"+i).val()=="") $("#txtReqQty_"+i).val(0);
				if($("#txtRemarks_"+i).val()=="") $("#txtRemarks_"+i).val(0);
				if($("#hiddenid_"+i).val()=="") $("#hiddenid_"+i).val(0);
				if($("#txtdescription_"+i).val()!='' && $("#txtdescription_"+i).val()!=0)
				{
					if(data_break_down=="")
					{
						data_break_down+=$('#txtdescription_'+i).val()+'_'+$('#txtSpecification_'+i).val()+'_'+$('#txtUnit_'+i).val()+'_'+$('#txtUnitPcs_'+i).val()+'_'+$('#txtConsQty_'+i).val()+'_'+$('#txtProcessLoss_'+i).val()+'_'+$('#txtProcessLossQty_'+i).val()+'_'+$('#txtReqQty_'+i).val()+'_'+$('#txtRemarks_'+i).val()+'_'+$('#hiddenid_'+i).val();
					}
					else
					{
						data_break_down+="__"+$('#txtdescription_'+i).val()+'_'+$('#txtSpecification_'+i).val()+'_'+$('#txtUnit_'+i).val()+'_'+$('#txtUnitPcs_'+i).val()+'_'+$('#txtConsQty_'+i).val()+'_'+$('#txtProcessLoss_'+i).val()+'_'+$('#txtProcessLossQty_'+i).val()+'_'+$('#txtReqQty_'+i).val()+'_'+$('#txtRemarks_'+i).val()+'_'+$('#hiddenid_'+i).val();
					}
				}
			}
			$('#hidden_break_tot_row').val( data_break_down );
			//alert(tot_row);//return;
			parent.emailwindow.hide();
		}

		/*function sum_total_qnty(id)
		{
			var ddd={ dec_type:5, comma:0, currency:''};
			var tot_row=$('#tbl_share_details_entry tbody tr').length;
			$("#txtorderamount_"+id).val(($("#txtorderquantity_"+id).val()*1)*($("#txtorderrate_"+id).val()*1));
			math_operation( "txt_total_order_qnty", "txtorderquantity_", "+", tot_row,ddd );
			//math_operation( "txt_average_rate", "txtorderrate_", "+", tot_row,ddd );
			math_operation( "txt_total_order_amount", "txtorderamount_", "+", tot_row,ddd );
			
			var tot_row=$('#tbl_share_details_entry tbody tr').length;
			
			var qty=0; var amt=0;
			for(var i=1; i<=tot_row; i++)
			{
				qty+=$("#txtorderquantity_"+i).val()*1;
				amt+=$("#txtorderamount_"+i).val()*1;
			}
			
			var rate=amt/qty;
			$("#txt_average_rate").val( number_format(rate,4,'.','' ) );
		}*/
		
		function create_description_row(prod_ids)
		{
			
		   // freeze_window(5);
			var row_num =  $('#tbl_share_details_entry tbody tr').length; //$('#txt_tot_row').val();
			var response_data = return_global_ajax_value(prod_ids + "**" + row_num , 'populate_prod_data', '', 'trims_production_entry_controller');
			$('#tbl_share_details_entry tbody').prepend(response_data);
			var tot_row = $('#tbl_share_details_entry tbody tr').length;
			//release_freezing();
		}

		function openmypage_material()
		{
			/*if ( form_validation('cbo_company_name*cbo_section','Company*Section')==false )
			{
				return;
			}*/
			//var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
			var data=1;
			page_link='trims_production_entry_controller.php?action=material_description_popup&data='+data;
			title='Trims Order Receive';
			

			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=650px, height=200px, center=1, resize=0, scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var theemailprod=this.contentDoc.getElementById("all_ids").value;
				if (theemailprod!="")
				{
					create_description_row(theemailprod);
				}
			}
		}

		function metarial_calculate(row)
		{
			var order_qty ='<? echo $order_qty ?>';
			//alert(order_qty);
			var unitPcs=$('#txtUnitPcs_'+row).val()*1;
			if(unitPcs==0)unitPcs=1;
			var cons_qty =order_qty/unitPcs;
			$('#txtConsQty_'+row).val(cons_qty.toFixed(2));
			process_calculate(row);
		}

		function process_calculate(row)
		{
			//var order_qty =<? //$order_qty ?>;
			var cons_qty=$('#txtConsQty_'+row).val()*1;
			var processLossQty=$('#txtProcessLossQty_'+row).val()*1;
			var processLoss=(processLossQty*100)/cons_qty;
			var reqQty=processLossQty+cons_qty;
			//alert(cons_qty+"**"+processLossQty);
			$('#txtProcessLoss_'+row).val(processLoss.toFixed(2));
			$('#txtReqQty_'+row).val(reqQty.toFixed(2));
		}
	</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<form name="qntypopup_1"  id="qntypopup_1" autocomplete="off">
				<table class="rpt_table" width="930px" cellspacing="0" cellpadding="0" rules="all" id="tbl_share_details_entry">
					<thead>
						<th width="30">Sl.</th>
						<th width="130">Description</th>
						<th width="100">Specification</th>
						<th width="80">Unit</th>
						<th width="70">Pcs/ Unit</th>
						<th width="60">Cons. Qty.</th>
						<th width="80">Process Loss %</th>
						<th width="80">Process Loss Qty.</th>
						<th width="80">Req. Qty.</th>
						<th width="80">Remarks</th>
						<th></th>
					</thead>
					<tbody id="description_list_view">
						<input type="hidden" name="txtDeletedId" id="txtDeletedId" class="text_boxes_numeric" style="width:90px" readonly />
						<input type="hidden" name="hidden_break_tot_row" id="hidden_break_tot_row" class="text_boxes" style="width:90px" />
						<? //echo $data_dreak;
						$k=0; 
						$data_array=explode("__",$data_break);
						$count_dtls_data=count($data_array);
						if($count_dtls_data>0)
						{
							foreach($data_array as $row)
							{
								$data=explode('_',$row);
								$k++;
								?>
								<tr>
									<td><input type="text" id="txtSl_<? echo $k;?>" name="txtSl_<? echo $k;?>" class="text_boxes" style="width:30px" value="<? echo $k; ?>" />
									</td>
									<td><input type="text" id="txtdescription_<? echo $k;?>" name="txtdescription_<? echo $k;?>" class="text_boxes" onClick="openmypage_material()" placeholder="Double Click" style="width:120px" value="<? echo $data[0]; ?>" />
									</td>
									<td>
										<input type="text" id="txtSpecification_<? echo $k;?>" name="txtSpecification_<? echo $k;?>" class="text_boxes" style="width:90px" value="<? echo $data[1]; ?>" <? echo $disabled; ?> /></td>
									<td><?
											echo create_drop_down( "txtUnit_".$k, 70, $unit_of_measurement,"", "1", "--- Select---",  $data[2], "","1" );
										?></td>
									<td>
										<input type="text" id="txtUnitPcs_<? echo $k;?>" name="txtUnitPcs_<? echo $k;?>"  class="text_boxes_numeric" style="width:60px" onKeyUp="metarial_calculate(<? echo $k;?>)" value="<? echo number_format($data[3],4,'.',''); ?>" <? echo $disabled; ?> />
									</td>
									<td><input type="text" id="txtConsQty_<? echo $k;?>" name="txtConsQty_<? echo $k;?>"  class="text_boxes_numeric" style="width:50px" onKeyUp="sum_total_qnty(<? echo $k;?>)" value="<? echo number_format($data[4],4,'.',''); ?>" <? echo $disabled; ?> />
									</td>
									<td>
										<input type="text" id="txtProcessLoss_<? echo $k;?>" name="txtProcessLoss_<? echo $k;?>" class="text_boxes_numeric" readonly style="width:70px" value="<? echo $data[5]; ?>"  />
									</td>
									<td><input type="text" id="txtProcessLossQty_<? echo $k;?>" name="txtProcessLossQty_<? echo $k;?>"  onKeyUp="process_calculate(<? echo $k;?>)" class="text_boxes_numeric" style="width:70px" value="<? echo number_format($data[6],4,'.',''); ?>" /></td>
									<td><input type="text" id="txtReqQty_<? echo $k;?>" name="txtReqQty_<? echo $k;?>" class="text_boxes_numeric" style="width:70px" value="<? echo number_format($data[7],4,'.',''); ?>" disabled/></td>
									<td><input type="text" id="txtRemarks_<? echo $k;?>" name="txtRemarks_<? echo $k;?>" class="text_boxes" style="width:70px" value="<? echo $data[8]; ?>" /></td>
									<td>
										<input type="hidden" id="hidbookingconsid_<? echo $k; ?>" name="hidbookingconsid_<? echo $k; ?>"  style="width:15px;" class="text_boxes" />
										<input type="hidden" id="hiddenid_<? echo $k; ?>" name="hiddenid_<? echo $k; ?>"  style="width:15px;" class="text_boxes"  />
										<?
										if($count_dtls_data==$k)
										{
											?>
												<input type="button" id="increaseset_<? echo $k;?>" style="width:30px" class="formbutton" value="+" onClick="add_share_row(<? echo $k;?>)" <? echo $disabled; ?> />
											<?
										}
										?>
										
										<input type="button" id="decreaseset_<? echo $k;?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $k;?> ,'tbl_share_details_entry' );" <? echo $disabled; ?>/>
									</td>  
								</tr>
								<?
							}
						}
						else
						{
							$k++;
							?>
							<tr>
								<td><input type="text" id="txtSl_<? echo $k;?>" name="txtSl_<? echo $k;?>" class="text_boxes" style="width:30px" value="<? echo $k; ?>" />
								</td>
								<td><input type="text" id="txtdescription_<? echo $k;?>" name="txtdescription_<? echo $k;?>" class="text_boxes" onClick="openmypage_material()" placeholder="Double Click" style="width:120px" value="<? echo $data[0]; ?>" />
								</td>
								<td>
									<input type="text" id="txtSpecification_<? echo $k;?>" name="txtSpecification_<? echo $k;?>" class="text_boxes" style="width:90px" value="<? echo $color_arr[$data[1]]; ?>" <? echo $disabled; ?> /></td>
								<td><?
										echo create_drop_down( "txtUnit_".$k, 70, $unit_of_measurement,"", "1", "--- Select---",  $data[2], "","1" );
									?>
								</td>
								<td>
									<input type="text" id="txtUnitPcs_<? echo $k;?>" name="txtUnitPcs_<? echo $k;?>" class="text_boxes_numeric" style="width:60px" onKeyUp="metarial_calculate(<? echo $k;?>)"  value="<? echo number_format($data[3],4,'.',''); ?>" <? echo $disabled; ?> />
								</td>
								<td><input type="text" id="txtConsQty_<? echo $k;?>" name="txtConsQty_<? echo $k;?>"  class="text_boxes_numeric" style="width:50px" onKeyUp="sum_total_qnty(<? echo $k;?>)" value="<? echo number_format($data[4],4,'.',''); ?>" <? echo $disabled; ?> /></td>
								<td>
									<input type="text" id="txtProcessLoss_<? echo $k;?>" name="txtProcessLoss_<? echo $k;?>" class="text_boxes_numeric" readonly style="width:70px" value="<? echo $data[3]; ?>"  />
								</td>
								<td><input type="text" id="txtProcessLossQty_<? echo $k;?>" name="txtProcessLossQty_<? echo $k;?>"  onKeyUp="process_calculate(<? echo $k;?>)"  class="text_boxes_numeric" style="width:70px" value="<? echo number_format($data[5],4,'.',''); ?>" /></td>
								<td><input type="text" id="txtReqQty_<? echo $k;?>" name="txtReqQty_<? echo $k;?>" class="text_boxes_numeric" style="width:70px" value="<? echo number_format($data[5],4,'.',''); ?>" disabled/></td>
								<td><input type="text" id="txtRemarks_<? echo $k;?>" name="txtRemarks_<? echo $k;?>" class="text_boxes" style="width:70px" value="<? echo $data[5]; ?>"/></td>
								<td>
									<input type="hidden" id="hidbookingconsid_<? echo $k; ?>" name="hidbookingconsid_<? echo $k; ?>"  style="width:15px;" class="text_boxes" value="<? echo $data[6]; ?>" />
									<input type="hidden" id="hiddenid_<? echo $k; ?>" name="hiddenid_<? echo $k; ?>"  style="width:15px;" class="text_boxes" value="<? echo $data[7]; ?>" />
									<input type="button" id="increaseset_<? echo $k;?>" style="width:30px" class="formbutton" value="+" onClick="add_share_row(<? echo $k;?>)" <? echo $disabled; ?> />
									<input type="button" id="decreaseset_<? echo $k;?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $k;?> ,'tbl_share_details_entry' );" <? echo $disabled; ?>/>
								</td>  
							</tr>
							<?
						}
						?> 
					</tbody>
				</table> 
				<table>
					<tr>
						<td align="center"><input type="button" name="main_close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" /></td>
					</tr>
				</table>
			</form>
		</div>
	</body>
	<script>//sum_total_qnty(0);</script>        
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}	
if ($action=="material_description_popup")
{
	echo load_html_head_contents("Description Popup Info","../../../", 1, 1, $unicode,'','');
	?>
	<script>
		/*function js_set_value(id)
		{ 
			$("#hidden_mst_id").val(id);
			document.getElementById('selected_job').value=id;
			parent.emailwindow.hide();
		}*/

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		} 
		var selected_id = new Array;
		function js_set_value(str) 
		{  // alert(str);
			var subcon_job = $('#txt_prod_id'+str).val();
			//alert(subcon_job);
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( subcon_job, selected_id ) == -1 ) {
				selected_id.push( subcon_job );
			}
			else 
			{
				for( var i = 0; i < selected_id.length; i++ ) 
				{
					if( selected_id[i] == subcon_job ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id ='';  var id_dtls = ''; var id_break = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id 		= id.substr( 0, id.length - 1 );
			$('#all_ids').val( id );
		}
	</script>
</head>
<body>
<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="600" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
			<thead> 
				<tr>           	 
					<th width="140" class="must_entry_caption">Company Name</th>
					<th width="100">Item Group</th>                           
					<th width="140">Description</th>
					<th width="100">Product ID</th>
					<th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
				</tr>           
			</thead>
			<tbody>
				<tr class="general">
					<td><input type="hidden" id="selected_job"><? $data=explode("_",$data); ?>  <!--  echo $data;-->
						<? 
						echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "",1); ?>
					</td>
					<td id="item_group_td">
						<?
							echo create_drop_down( "cbo_item_group", 130, "select id,item_name from lib_item_group where item_category in (101) and status_active=1 and is_deleted=0 order by item_name", "id,item_name", 1, "-- Select --", 0, "", $disabled,"" );
						 ?>
					</td>
					<td align="center">
						<input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
					</td>
					<td align="center">
						<input type="text" name="txt_product_id" id="txt_product_id" class="text_boxes_numeric" style="width:100px" placeholder="" />
					</td>
					<td align="center">
						<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_item_group').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('txt_product_id').value, 'create_description_search_list_view', 'search_div', 'trims_production_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" />
						<input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
					</td>
					</tr>
					<tr>
						<td colspan="5" align="center" valign="top" id=""><div id="search_div"></div></td>
					</tr>
				</tbody>
			</table>    
			</form>
		</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_description_search_list_view")
{	
	$data=explode('_',$data);
	$group_id=str_replace("'","",$data[1]);
	$description_str=str_replace("'","",$data[2]);
	$product_id=trim(str_replace("'","",$data[3]));
	if($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if($group_id!=0) $group=" and item_group_id=$group_id"; else { $group=''; }	
	if($description_str!='') $description=" and item_description='$description_str'"; else { $description=''; }	
	if($product_id!='') $product=" and id='$product_id'"; else { $product=''; }	

	
	$sql="select id,company_id, item_code,item_description,item_group_id,item_size,current_stock,brand_name,origin,model,sub_group_name,unit_of_measure from product_details_master where status_active=1 and is_deleted=0 and item_category_id=101 $company $group $description $product and status_active=1 and is_deleted=0";
	// echo $sql;
	$data_array=sql_select($sql);
	?>
	 <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="585" >
		<thead>
			<th width="30">SL</th>
			<th width="110">Item Group</th>
			<th width="80">UOM</th>
			<th width="200">Description</th>
			<th>Product ID</th>
		</thead>
	</table>
		<div style="width:585px; max-height:270px;overflow-y:scroll;" >	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="585" class="rpt_table" id="tbl_po_list">
		<tbody>
			<? 
			$i=1;
			
			$item_group_arr=return_library_array( "select id,item_name from lib_item_group where item_category in (101) and status_active=1 and is_deleted=0 order by item_name", "id,item_name");
			foreach($data_array as $row)
			{  
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if ($i%2==0)  
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";	
				?>	
				<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $i;?>)" > 
					<td width="30"><? echo $i; ?></td>
					<td width="110"><? echo $item_group_arr[$row[csf('item_group_id')]]; ?></td>
					<td width="80"><? echo $unit_of_measurement[$row[csf('unit_of_measure')]]; ?></td>
					<td width="200" style="word-break:break-all" ><? echo $row[csf('item_description')]; ?></td>
					<td><? echo $row[csf('id')]; ?>
						<input name="txt_prod_id<? echo $i; ?>" id="txt_prod_id<? echo $i; ?>" type="hidden" value="<? echo $row[csf('id')]; ?>" />
					</td>
				</tr>
				<? 
				$i++; 
			} 
			?>
		</tbody>
	</table><div style="width:40%; float:left" align="left">
		<input type="button" class="formbutton" id="close" style="width:80px" onClick="parent.emailwindow.hide();" value="Close" />
		<input type="hidden"  id="all_ids" />
	</div>
	` 
	<?    
	exit();
}

if ($action == "populate_prod_data") 
{
	$ex_data = explode("**", $data);
	$prod_id =   $ex_data[0] ; 
	if(!$prod_id)$prod_id=0;
	 
		
	 $sql="select id,company_id,item_code,item_description,item_group_id,item_size,current_stock,brand_name,origin,model,sub_group_name,unit_of_measure from product_details_master where status_active=1 and is_deleted=0 and item_category_id=101 and id in($prod_id) and status_active=1 and is_deleted=0";
  
	$result = sql_select($sql);
	$count=count($result);
	$i=$ex_data[1]+$count;
	foreach ($result as $row)
	{ 
		?>
		<tr>
			<td><input type="text" id="txtSl_<? echo $i;?>" name="txtSl_<? echo $i;?>" class="text_boxes" style="width:30px" value="<? echo $i; ?>" />
			</td>
			<td><input type="text" id="txtdescription_<? echo $i;?>" name="txtdescription_<? echo $i;?>" class="text_boxes" onClick="openmypage_material()" placeholder="Double Click" style="width:120px" value="<? echo $row[csf('item_description')]; ?>" />
			</td>
			<td>
				<input type="text" id="txtSpecification_<? echo $i;?>" name="txtSpecification_<? echo $i;?>" class="text_boxes" style="width:90px" value="<? echo $row[csf('item_description')]; ?>" <? echo $disabled; ?> /></td>
			<td><?
				echo create_drop_down( "txtUnit_".$i, 70, $unit_of_measurement,"", "1", "--- Select---",  $row[csf('unit_of_measure')], "","1" );
			?></td>
			<td>
				<input type="text" id="txtUnitPcs_<? echo $i;?>" name="txtUnitPcs_<? echo $i;?>" onKeyUp="metarial_calculate(<? echo $i;?>)"  class="text_boxes_numeric" style="width:60px" />
			</td>
			<td><input type="text" id="txtConsQty_<? echo $i;?>" name="txtConsQty_<? echo $i;?>"  class="text_boxes_numeric" style="width:50px" onKeyUp="sum_total_qnty(<? echo $i;?>)"  />
			</td>
			<td>
				<input type="text" id="txtProcessLoss_<? echo $i;?>" name="txtProcessLoss_<? echo $i;?>" class="text_boxes_numeric" style="width:70px" value="<? echo $data[3]; ?>"  />
			</td>
			<td><input type="text" id="txtProcessLossQty_<? echo $i;?>" name="txtProcessLossQty_<? echo $i;?>"  onKeyUp="process_calculate(<? echo $i;?>)"  class="text_boxes_numeric" style="width:70px" disabled/></td>
			<td><input type="text" id="txtReqQty_<? echo $i;?>" name="txtReqQty_<? echo $i;?>" class="text_boxes_numeric" style="width:70px"  disabled/></td>
			<td><input type="text" id="txtRemarks_<? echo $i;?>" name="txtRemarks_<? echo $i;?>" class="text_boxes" style="width:70px" /></td>
			<td>
				<input type="hidden" id="hidbookingconsid_<? echo $i; ?>" name="hidbookingconsid_<? echo $i; ?>"  style="width:15px;" class="text_boxes"  />
				<input type="hidden" id="hiddenid_<? echo $i; ?>" name="hiddenid_<? echo $i; ?>"  style="width:15px;" class="text_boxes"  />
				<input type="button" id="decreaseset_<? echo $i;?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i;?> ,'tbl_share_details_entry' );" <? echo $disabled; ?>/>
			</td>  
		</tr>
		<?
		$i--;
		 
	}
exit(); 
}


if ($action=="job_popup")
{
	echo load_html_head_contents("Job Popup Info","../../../", 1, 1, $unicode,'','');
	?>
	<script>
		function js_set_value(id)
		{ 
			$("#hidden_mst_id").val(id);
			document.getElementById('selected_job').value=id;
			parent.emailwindow.hide();
		}
		
		function fnc_load_party_popup(type,within_group)
		{
			var company = $('#cbo_company_name').val();
			var party_name = $('#cbo_party_name').val();
			var location_name = $('#cbo_location_name').val();
			var within_group = $('#cbo_within_group').val();
			load_drop_down( 'trims_production_entry_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0)
			{
				$('#search_by_td').html('Job ID');
			}
			else if(val==2)
			{
				$('#search_by_td').html('W/O No');
			}
			else if(val==4)
			{
				$('#search_by_td').html('Buyer Po');
			}
			else if(val==5)
			{
				$('#search_by_td').html('Buyer Style');
			}
		}
	</script>
</head>
<body>
<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="940" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
			<thead> 
				<tr>
					<th colspan="8"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
				</tr>
				<tr>               	 
					<th width="140" class="must_entry_caption">Company Name</th>
					<th width="60">Within Group</th>                           
					<th width="140">Party Name</th>
					<th width="80">Search By</th>
					<th width="100" id="search_by_td">Job ID</th>
					<th width="60">Year</th>
					<th width="170">Date Range</th>                            
					<th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
				</tr>           
			</thead>
			<tbody>
				<tr class="general">
					<td><input type="hidden" id="selected_job"><? $data=explode("_",$data); ?>  <!--  echo $data;-->
						<? 
						echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "fnc_load_party_popup(1,document.getElementById('cbo_within_group').value);",1); ?>
					</td>
					<td>
						<?php echo create_drop_down( "cbo_within_group", 60, $yes_no,"", 0, "--  --", $data[3], "fnc_load_party_popup(1,this.value);",1 ); ?>
					</td>
					<td id="buyer_td">
						<? echo create_drop_down( "cbo_party_name", 150, "","", 1, "-- Select Party --", $data[2], "fnc_load_party_popup(1,this.value);" );   	 
						?>
					</td>
					<td>
												<?
							$search_by_arr=array(1=>"Job ID",2=>"W/O No",4=>"Buyer Po",5=>"Buyer Style");
							echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
						?>
					</td>
					<td align="center">
						<input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
					</td>
					<td align="center"><? echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
					<td align="center">
						<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
						<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
					</td>
					<td align="center">
						<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value, 'create_job_search_list_view', 'search_div', 'trims_production_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
					</tr>
					<tr>
						<td colspan="9" align="center" valign="middle">
							<? echo load_month_buttons();  ?>
							<input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
						</td>
					</tr>
					<tr>
						<td colspan="9" align="center" valign="top" id=""><div id="search_div"></div></td>
					</tr>
				</tbody>
			</table>    
			</form>
		</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_job_search_list_view")
{	
	$data=explode('_',$data);
	$party_id=str_replace("'","",$data[1]);
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	$within_group =$data[7];
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[8]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";}

	if($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	//echo $search_type; die;
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no = '$search_str' ";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref = '$search_str' ";
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '$search_str%'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '$search_str%'";  
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str'";  
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str%'"; 
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str%'";   
		}
	}	

	if($party_id!=0) $party_id_cond=" and a.party_id='$party_id'"; else $party_id_cond="";

	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $delivery_date = "and a.delivery_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $delivery_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $delivery_date = "and a.delivery_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $delivery_date ="";
	}
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	if($within_group==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}

	if($db_type==0) 
	{
		$ins_year_cond="year(a.insert_date)";
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
	}
	$sql= "select a.id, a.trims_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id,  a.order_no, a.received_id,a.received_no ,a.section_id
	 from trims_job_card_mst a, trims_job_card_dtls b
	 where a.entry_form=257 and a.trims_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $delivery_date $company $party_id_cond $withinGroup $search_com_cond $withinGroup $year_cond 
	 group by a.id, a.trims_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.order_no, a.received_id, a.received_no ,a.section_id
	 order by a.id DESC";
	//echo $sql;
	$data_array=sql_select($sql);
	?>
	 <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="685" >
		<thead>
			<th width="30">SL</th>
			<th width="120">Job No</th>
			<th width="60">Year</th>
			<th width="120">W/O No</th>
			<th width="120">Section</th>
		</thead>
		</table>
		<div style="width:685px; max-height:270px;overflow-y:scroll;" >	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="665" class="rpt_table" id="tbl_po_list">
		<tbody>
			<? 
			$i=1;
			foreach($data_array as $row)
			{  
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('trims_job')].'_'.$row[csf('received_id')]; ?>")' style="cursor:pointer" >
					<td width="30"><? echo $i; ?></td>
					<td width="120"><? echo $row[csf('trims_job')]; ?></td>
					<td width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
					<td width="120"><? echo $row[csf('order_no')]; ?></td>
					<td width="110"><? echo $trims_section[$row[csf('section_id')]]; ?></td>
				   
				</tr>
				<? 
				$i++; 
			} 
			?>
		</tbody>
	</table>
	<?
	exit();
}

if ($action=="load_mst_php_data_to_form")
{
	$data=explode('_',$data);
	$id=$data[0];
	$company=$data[1];
	//echo "select a.id, a.trims_job, a.company_id, a.location_id, a.party_id, a.currency_id, a.within_group,a.party_location, a.order_id, a.order_no, a.order_qty, a.received_no, a.received_id, a.section_id ,b.item_description, sum(job_quantity) as job_quantity from trims_job_card_mst a, trims_job_card_dtls b  where a.id=b.mst_id and a.entry_form=257 and a.id=$data and a.status_active=1 group by a.id, a.trims_job, a.company_id, a.location_id, a.party_id, a.currency_id, a.within_group,a.party_location, a.order_id, a.order_no, a.order_qty, a.received_no, a.received_id, a.section_id ,b.item_description"  ;  die; 
	
	$variable_set_invent = sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$company and variable_list=23 and category = 4 and status_active =1 and is_deleted=0 order by id");
	$over_receive_limit = !empty($variable_set_invent) ? $variable_set_invent[0][csf('over_rcv_percent')] : 0;
	//echo "select a.id, a.trims_job, a.company_id, a.location_id, a.party_id, a.currency_id, a.within_group,a.party_location, a.order_id, a.order_no, a.order_qty, a.received_no, a.received_id, a.section_id ,b.item_description, sum(job_quantity) as job_quantity from trims_job_card_mst a, trims_job_card_dtls b  where a.id=b.mst_id and a.entry_form=257 and a.company_id=$company and a.id=$id and a.status_active=1 group by a.id, a.trims_job, a.company_id, a.location_id, a.party_id, a.currency_id, a.within_group,a.party_location, a.order_id, a.order_no, a.order_qty, a.received_no, a.received_id, a.section_id ,b.item_description";
	$nameArray=sql_select( "select a.id, a.trims_job, a.company_id, a.location_id, a.party_id, a.currency_id, a.within_group,a.party_location, a.order_id, a.order_no, a.order_qty, a.received_no, a.received_id, a.section_id ,b.item_description, sum(job_quantity) as job_quantity from trims_job_card_mst a, trims_job_card_dtls b  where a.id=b.mst_id and a.entry_form=257 and a.company_id=$company and a.id=$id and a.status_active=1 group by a.id, a.trims_job, a.company_id, a.location_id, a.party_id, a.currency_id, a.within_group,a.party_location, a.order_id, a.order_no, a.order_qty, a.received_no, a.received_id, a.section_id ,b.item_description" );
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('hid_recv_id').value 				= '".$row[csf("received_id")]."';\n";
		echo "document.getElementById('cbo_company_name').value 		= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_within_group').value 		= '".$row[csf("within_group")]."';\n";  
		echo "document.getElementById('txt_job_no').value 				= '".$row[csf("trims_job")]."';\n";  
		echo "document.getElementById('hid_job_id').value 				= '".$row[csf("id")]."';\n";  
		echo "document.getElementById('txt_order_no').value 			= '".$row[csf("order_no")]."';\n";  
		echo "document.getElementById('hid_order_id').value 			= '".$row[csf("order_id")]."';\n";  
		echo "document.getElementById('txt_item').value 				= '".$row[csf("item_description")]."';\n";  
		echo "document.getElementById('txt_order_qty').value 			= '".$row[csf("job_quantity")]."';\n";  
		echo "document.getElementById('txt_over_ctrl_var_sts').value 	= '".$over_receive_limit."';\n";  
		
		echo "fnc_load_party(1,'".$row[csf("within_group")]."');\n";
		//echo "load_drop_down( 'requires/trims_production_entry_controller', '".$row[csf("company_id")]."', 'load_drop_down_location', 'location_td' );\n";	
		echo "document.getElementById('cbo_location_name').value 		= ".$row[csf("location_id")].";\n";
		//echo "load_drop_down( 'requires/trims_production_entry_controller',  '".$row[csf("company_id")]."', 'load_drop_down_buyer', 'buyer_td' );\n";
		echo "document.getElementById('cbo_party_name').value			= ".$row[csf("party_id")].";\n";
		echo "fnc_load_party(2,'".$row[csf("within_group")]."');\n";
		echo "document.getElementById('cbo_party_location').value		= '".$row[csf("party_location")]."';\n";	
		
		echo "document.getElementById('hid_order_id').value          	= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('cbo_section').value 				= ".$row[csf("section_id")].";\n";
		//echo "document.getElementById('cbo_section').value 				= ".$over_receive_limit.";\n";
		echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		echo "$('#cbo_within_group').attr('disabled','true')".";\n";
		echo "$('#cbo_party_name').attr('disabled','true')".";\n";
	}
	exit();	
}



if ($action=="production_popup")
{
	echo load_html_head_contents("Production Popup Info","../../../", 1, 1, $unicode,'','');
	?>
	<script>
		function js_set_value(id)
		{ 
			$("#hidden_mst_id").val(id);
			document.getElementById('selected_job').value=id;
			parent.emailwindow.hide();
		}
		
		function fnc_load_party_popup(type,within_group)
		{
			var company = $('#cbo_company_name').val();
			var party_name = $('#cbo_party_name').val();
			var location_name = $('#cbo_location_name').val();
			var within_group = $('#cbo_within_group').val();
			load_drop_down( 'trims_production_entry_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0)
			{
				$('#search_by_td').html('System ID');
			}
			else if(val==2)
			{
				$('#search_by_td').html('W/O No');
			}
			else if(val==4)
			{
				$('#search_by_td').html('Buyer Po');
			}
			else if(val==5)
			{
				$('#search_by_td').html('Buyer Style');
			}
		}
	</script>
</head>
<body>
<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="940" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
			<thead> 
				<tr>
					<th colspan="9"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
				</tr>
				<tr>               	 
					<th width="140" class="must_entry_caption">Company Name</th>
					<th width="60">Within Group</th>                           
					<th width="140">Party Name</th>
					<th width="80">Search By</th>
					<th width="100" id="search_by_td">System ID</th>
					<th width="60">Year</th>
					<th width="170">Date Range</th>                            
					<th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
				</tr>           
			</thead>
			<tbody>
				<tr class="general">
					<td><input type="hidden" id="selected_job"><? $data=explode("_",$data); ?>  <!--  echo $data;-->
						<? 
						echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "fnc_load_party_popup(1,document.getElementById('cbo_within_group').value);",1); ?>
					</td>
					<td>
						<?php echo create_drop_down( "cbo_within_group", 60, $yes_no,"", 0, "--  --", $data[3], "fnc_load_party_popup(1,this.value);",1 ); ?>
					</td>
					<td id="buyer_td">
						<? echo create_drop_down( "cbo_party_name", 150, "","", 1, "-- Select Party --", $data[2], "fnc_load_party_popup(1,this.value);" );   	 
						?>
					</td>
					<td>
						<?
							$search_by_arr=array(1=>"System ID",2=>"W/O No",4=>"Buyer Po",5=>"Buyer Style");
							echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
						?>
					</td>
					<td align="center">
						<input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
					</td>
					<td align="center"><? echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
					<td align="center">
						<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
						<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
					</td>
					<td align="center">
						<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value, 'create_prod_search_list_view', 'search_div', 'trims_production_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
					</tr>
					<tr>
						<td colspan="9" align="center" valign="middle">
							<? echo load_month_buttons();  ?>
							<input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
						</td>
					</tr>
					<tr>
						<td colspan="9" align="center" valign="top" id=""><div id="search_div"></div></td>
					</tr>
				</tbody>
			</table>    
			</form>
		</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_prod_search_list_view")
{	
	$data=explode('_',$data);
	$party_id=str_replace("'","",$data[1]);
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	$within_group =$data[7];
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[8]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";}
	if($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	//echo $search_type; die;
	
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com="and a.prod_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no = '$search_str' ";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref = '$search_str' ";
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com="and a.prod_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '$search_str%'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '$search_str%'";  
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com="and a.prod_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str'";  
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com="and a.prod_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str%'"; 
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str%'";   
		}
	}

	if($search_str!="" && ($search_by==2 || $search_by==4 || $search_by==5))
	{
		if($db_type==0) $id_cond="group_concat(b.id) as id";
		else if($db_type==2) $id_cond="rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as id";

		$job_dtls_ids = return_field_value("$id_cond", "trims_job_card_mst a, trims_job_card_dtls b", "a.entry_form=257 and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $search_com_cond", "id");// and a.trims_job=b.job_no_mst
	}

	if($db_type==2 && $job_dtls_ids!="") $job_dtls_ids = $job_dtls_ids->load();
	if ($job_dtls_ids!="")
	{
		$job_dtls_ids=explode(",",$job_dtls_ids);
		$job_dtls_idsCond=""; $jobDtlsCond="";
		//echo count($job_dtls_ids); die;
		if($db_type==2 && count($job_dtls_ids)>=999)
		{
			$chunk_arr=array_chunk($job_dtls_ids,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",",$val);
				if($job_dtls_idsCond=="")
				{
					$job_dtls_idsCond.=" and ( b.job_dtls_id in ( $ids) ";
				}
				else
				{
					$job_dtls_idsCond.=" or  b.job_dtls_id in ( $ids) ";
				}
			}
			$job_dtls_idsCond.=")";
		}
		else
		{
			$ids=implode(",",$job_dtls_ids);
			$job_dtls_idsCond.=" and b.job_dtls_id in ($ids) ";
		}
	}
	else if($job_dtls_ids=='' && ($search_str!="" && ($search_by==2 || $search_by==4 || $search_by==5)))
	{
		echo "Not Found"; die;
	}

	if($party_id!=0) $party_id_cond=" and a.party_id='$party_id'"; else $party_id_cond="";

	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $production_date = "and a.production_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $production_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $production_date = "and a.production_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $production_date ="";
	}
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	if($within_group==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}

	if($db_type==0) 
	{
		$ins_year_cond="year(a.insert_date)";
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
	}
	
	$sql= "select a.id,a.trims_production, a.prod_no_prefix, a.prod_no_prefix_num, a.company_id,  a.location_id , a.within_group,  a.party_id, a.party_location ,a.production_date,  a.order_id, a.received_id, a.job_id, a.item, a.quantity, a.section_id, a.machine_group, a.floor,$ins_year_cond as year
	from trims_production_mst a, trims_production_dtls b
	where a.entry_form=269 and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $production_date $company $buyer $withinGroup $search_com $job_dtls_idsCond 
	group by  a.id,a.trims_production, a.prod_no_prefix, a.prod_no_prefix_num, a.company_id,  a.location_id , a.within_group,  a.party_id, a.party_location ,a.production_date,  a.order_id, a.received_id, a.job_id, a.item, a.quantity, a.section_id, a.machine_group, a.floor,a.insert_date
	order by a.id DESC";
	// echo $sql;
	$data_array=sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="685" >
		<thead>
			<th width="30">SL</th>
			<th width="120">Production No</th>
			<th width="60">Year</th>
			<th width="120">Section</th>
			<th>Item</th>
		</thead>
		</table>
		<div style="width:685px; max-height:270px;overflow-y:scroll;" >	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="665" class="rpt_table" id="tbl_po_list">
		<tbody>
			<? 
			$i=1;
			foreach($data_array as $row)
			{  
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('trims_production')].'_'.$row[csf('received_id')].'_'.$row[csf('job_id')]; ?>")' style="cursor:pointer" >
					<td width="30"><? echo $i; ?></td>
					<td width="120"><? echo $row[csf('trims_production')]; ?></td>
					<td width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
					<td width="120"><? echo $trims_section[$row[csf('section_id')]]; ?></td>
					<td style="text-align:center;"><? echo $row[csf('item')]; ?></td>
				</tr>
				<? 
				$i++; 
			} 
			?>
		</tbody>
	</table>
	<?
	exit();
}

if ($action=="load_production_data_to_form")
{
	$data=explode('_',$data);
	$id=$data[0];
	$company=$data[1];

	$variable_set_invent = sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$company and variable_list=23 and category = 4 and status_active =1 and is_deleted=0 order by id");
	$over_receive_limit = !empty($variable_set_invent) ? $variable_set_invent[0][csf('over_rcv_percent')] : 0;
	$sql="select a.id,a.trims_production, a.prod_no_prefix, a.prod_no_prefix_num, a.company_id,  a.location_id , a.within_group,  a.party_id, a.party_location ,a.production_date,  a.order_id, a.received_id, a.job_id, a.item, a.quantity, a.section_id, a.machine_group,floor from trims_production_mst a where a.entry_form=269 and a.id=$id and a.status_active=1 ";

	$nameArray=sql_select( $sql );
	foreach ($nameArray as $row)
	{	
		$rc=$row[csf("received_id")];
		//echo "10**select order_no from subcon_ord_mst where id=$rc"; die;

		$trims_job = return_field_value("trims_job", "trims_job_card_mst", "id=".$row[csf("job_id")]."", "trims_job");
		$order_no = return_field_value("order_no", "subcon_ord_mst", "id=".$rc."", "order_no");
		//echo $order_no; die;
		echo "document.getElementById('txt_production_no').value 		= '".$row[csf("trims_production")]."';\n";
		echo "document.getElementById('update_id').value 				= ".$row[csf("id")].";\n";
		echo "document.getElementById('hid_recv_id').value 				= ".$row[csf("received_id")].";\n";
		echo "document.getElementById('hid_job_id').value 				= ".$row[csf("job_id")].";\n";  
		echo "document.getElementById('txt_job_no').value 				= '".$trims_job."';\n";  
		echo "document.getElementById('txt_order_no').value 			= '".$order_no."';\n";
		echo "document.getElementById('hid_order_id').value 			= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('txt_over_ctrl_var_sts').value 	= '".$over_receive_limit."';\n";  
		

		echo "document.getElementById('cbo_company_name').value 		= ".$row[csf("company_id")].";\n";
		echo "document.getElementById('cbo_within_group').value 		= ".$row[csf("within_group")].";\n"; 
		echo "fnc_load_party(1,'".$row[csf("within_group")]."');\n";
		echo "document.getElementById('cbo_party_name').value			= ".$row[csf("party_id")].";\n";
		echo "fnc_load_party(2,'".$row[csf("within_group")]."');\n";
		echo "document.getElementById('cbo_party_location').value		= ".$row[csf("party_location")].";\n";

		echo "document.getElementById('cbo_location_name').value 		= ".$row[csf("location_id")].";\n"; 
		//echo "document.getElementById('txt_item').value 				= '".$row[csf("item")]."';\n";  
		//echo "document.getElementById('txt_order_qty').value 			= '".$row[csf("quantity")]."';\n";  

		echo "load_drop_down( 'requires/trims_production_entry_controller', '".$row[csf("company_id")].'_'.$row[csf("location_id")]."', 'load_drop_down_machine_group', 'group_td' );\n";
		echo "document.getElementById('cbo_mc_group').value 			= '".$row[csf("machine_group")]."';\n"; 

		echo "load_drop_down( 'requires/trims_production_entry_controller', '".$row[csf("company_id")].'_'.$row[csf("location_id")].'_'.$row[csf("machine_group")]."', 'load_drop_down_floor', 'floor_td' );\n";
		echo "document.getElementById('cbo_floor_id').value 			= ".$row[csf("floor")].";\n";

		//echo "fnc_load_drop( $row[csf("company_id")],$row[csf("location_id")],$row[csf("machine_group")],$row[csf("floor")]);\n";
		
		echo "document.getElementById('txt_prod_date').value			= '".change_date_format($row[csf("production_date")])."';\n"; 
		echo "document.getElementById('cbo_section').value 				= ".$row[csf("section_id")].";\n";
		echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		echo "$('#cbo_within_group').attr('disabled','true')".";\n";
		echo "$('#cbo_party_name').attr('disabled','true')".";\n";
		echo "$('#txt_job_no').attr('disabled','true')".";\n";
		echo "$('#txt_order_no').attr('disabled','true')".";\n";
	}
	exit();	
}
?>