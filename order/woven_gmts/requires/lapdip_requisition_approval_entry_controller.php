<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];

//---------------------------------------------------- Start
if ($action=="load_drop_down_buyer")
{
	if($data != 0) $comCond="and b.tag_company=$data"; else  $comCond="";
	echo create_drop_down( "cbo_buyer_name", 172, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $comCond $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );  
	exit();
}
if ($action=="check_lapdip_data")
{ 
	$requisition_id=return_field_value("requisition_id","wo_po_lapdip_approval_info","requisition_id=$data");
	 echo $requisition_id;
	exit();
}

if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	if($operation==0)  // Insert Here
	{
		$con = connect();
		
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$id=return_next_id( "id", "wo_po_lapdip_approval_info", 1 ) ;
		
		$field_array="id, requisition_no, requisition_id, color_name_id, lapdip_target_approval_date, send_to_factory_date, recv_from_factory_date, submitted_to_buyer, approval_status, approval_status_date, lapdip_no, lapdip_comments, is_deleted, status_active, inserted_by, insert_date, garments_nature"; 
		
		$sl=1;
		for($z=1; $z<=$numberOfColor; $z++)
		{
			$color_id="colorId_".$z;
			$colorid=str_replace("'", "", $$color_id);
			$colorRow="colorRow_".$colorid;
			
			// echo "10**".$$color_id."==>".$$colorRow;die;
			for($i=1; $i<=$$colorRow-1; $i++)
			{
				 
				
				$target_app_date="targetAppDate_".$colorid."_".$i;
				$send_to_factory_date="sendToFactoryDate_".$colorid."_".$i;
				$recv_from_factory_date="recvFromFactoryDate_".$colorid."_".$i;
				$sent_to_buyer="submittedToBuyer_".$colorid."_".$i;
				$action="action_".$colorid."_".$i;
				$action_date="actionDate_".$colorid."_".$i;
				$txt_lapdip_no="txtLapdipNo_".$colorid."_".$i;
				$txt_comments="txtComments_".$colorid."_".$i;
				$cbo_status="cboStatus_".$colorid."_".$i;
				
				

				if ($sl!=1) $data_array .=",";
				$data_array .="(".$id.",'".$txt_requisition_no."',".$hiddReqId.",".$$color_id.",".$$target_app_date.",".$$send_to_factory_date.",".$$recv_from_factory_date.",".$$sent_to_buyer.",".$$action.",".$$action_date.",".$$txt_lapdip_no.",".$$txt_comments.",0,".$$cbo_status.",".$user_id.",'".$pc_date_time."',$garments_nature)";
				
				$id=$id+1;
				$sl++;
			}
		}
		//    echo "10**insert into wo_po_lapdip_approval_info ".$field_array ." values (".$data_array.")";;die;
		$rID=sql_insert("wo_po_lapdip_approval_info",$field_array,$data_array,1);
		 
		//echo "10**".$rID;die;
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "0**".$txt_requisition_no;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**".$txt_requisition_no;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			 if($rID )
			    {
					oci_commit($con);   
					echo "0**".$txt_requisition_no;
				}
				else{
					oci_rollback($con);
					echo "5**".$txt_requisition_no;
				}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$id=return_next_id( "id", "wo_po_lapdip_approval_info", 1 ) ;
		$data_array="";
		
		$field_array="id, requisition_no, requisition_id, color_name_id, lapdip_target_approval_date, send_to_factory_date, recv_from_factory_date, submitted_to_buyer, approval_status, approval_status_date, lapdip_no, lapdip_comments, is_deleted, status_active, inserted_by, insert_date, garments_nature"; 
		$field_array_update="requisition_no*requisition_id*color_name_id*lapdip_target_approval_date*send_to_factory_date*recv_from_factory_date*submitted_to_buyer*approval_status*approval_status_date*lapdip_no*lapdip_comments*is_deleted*status_active*updated_by*update_date*garments_nature";
		$sl=1;
		for($z=1; $z<=$numberOfColor; $z++)
		{
			$color_id="colorId_".$z;
			$colorid=str_replace("'", "", $$color_id);
			$colorRow="colorRow_".$colorid;
			
			 
			for($i=1; $i<=$$colorRow-1; $i++)
			{
				
				$target_app_date="targetAppDate_".$colorid."_".$i;
				$send_to_factory_date="sendToFactoryDate_".$colorid."_".$i;
				$recv_from_factory_date="recvFromFactoryDate_".$colorid."_".$i;
				$sent_to_buyer="submittedToBuyer_".$colorid."_".$i;
				$action="action_".$colorid."_".$i;
				$action_date="actionDate_".$colorid."_".$i;
				$txt_lapdip_no="txtLapdipNo_".$colorid."_".$i;
				$txt_comments="txtComments_".$colorid."_".$i;
				$cbo_status="cboStatus_".$colorid."_".$i;		 
				$updateid="updateid_".$colorid."_".$i;

			 if(str_replace("'",'',$$updateid)!="")
			 {
				
				$id_arr[]=str_replace("'",'',$$updateid);
				$data_array_update[str_replace("'",'',$$updateid)] = explode(",",("'".$txt_requisition_no."',".$hiddReqId.",".$$color_id.",".$$target_app_date.",".$$send_to_factory_date.",".$$recv_from_factory_date.",".$$sent_to_buyer.",".$$action.",".$$action_date.",".$$txt_lapdip_no.",".$$txt_comments.",0,".$$cbo_status.",".$user_id.",'".$pc_date_time."',$garments_nature"));
			 }
			 else
			 {
				if ($sl!=1) $data_array .=",";
				$data_array .="(".$id.",'".$txt_requisition_no."',".$hiddReqId.",".$$color_id.",".$$target_app_date.",".$$send_to_factory_date.",".$$recv_from_factory_date.",".$$sent_to_buyer.",".$$action.",".$$action_date.",".$$txt_lapdip_no.",".$$txt_comments.",0,".$$cbo_status.",".$user_id.",'".$pc_date_time."',$garments_nature)";
			
				$id=$id+1;
				$sl++;
			 }
		 }
	    }
	 // echo "10**".bulk_update_sql_statement( "wo_po_lapdip_approval_info", "id", $field_array_update, $data_array_update, $id_arr );die;
		$flag=1;
		if($data_array_update!="")
		{
		  	$rID=execute_query(bulk_update_sql_statement( "wo_po_lapdip_approval_info", "id", $field_array_update, $data_array_update, $id_arr ),1);
			
			if($rID) $flag=1; else $flag=0;
		}
		if($data_array!="")
		{
			$rID2=sql_insert("wo_po_lapdip_approval_info",$field_array,$data_array,1);
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			} 
		}
          
		
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".$txt_requisition_no;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**".$txt_requisition_no;
			}
		}
		
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con); 
				echo "1**".$txt_requisition_no;
			}
			else
			{
				oci_rollback($con);
				echo "6**".$txt_requisition_no;
			}
		}
		disconnect($con);
		die;
	}
	else if($operation==2)//Delete here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array=$user_id."*'".$pc_date_time."'*0*1";
 
		$rID=sql_delete("wo_po_lapdip_approval_info",$field_array,$data_array,"job_no_mst*color_name_id","'".$txt_requisition_no."'"."*".$color_id,1);
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");
				echo "2**".$txt_requisition_no;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "7**".$txt_requisition_no;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);
				echo "2**".$txt_requisition_no;
			}
			else
			{
				oci_rollback($con);
				echo "7**".$txt_requisition_no;
			}
		}
		disconnect($con);
		die;
	}
}
 
if($action=="order_popup")
{
  	echo load_html_head_contents("Lapdip Requisition  Approval Info","../../../", 1, 1, '','','');
	$garments_nature=$_REQUEST['garments_nature'];
  ?>
	<script>
	 
	
	function js_set_value( job_no )
	{
		document.getElementById('selected_job').value=job_no;
		parent.emailwindow.hide();
	}
    </script>
 </head>
 <body>
 <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="960" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <tr align="center">
                    <th colspan="7"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr> 
                <tr>                     	 
                    <th>Company Name</th>
                    <th>Buyer Name</th>
                    <th>Requisition No</th>
                    <th>Style Ref </th>                     
                    <th width="200">Date Range</th>
                    <th> </th>  
                </tr>         
            </thead>
            <tbody>
				<tr class="general">
					<td><input type="hidden" id="selected_job">
                        <? echo create_drop_down( "cbo_company_name", 160, "select comp.id,comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'lapdip_requisition_approval_entry_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                    <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 172, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --",$selected ); ?></td>
                    <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes_numeric" style="width:90px"></td>
                    <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px"></td>                     
                    <td>
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                    </td> 
                    <td align="center">
                         <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $garments_nature; ?>+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_po_search_list_view', 'search_div', 'lapdip_requisition_approval_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
                </tr>
                <tr align="center">
                    <td colspan="7"><? echo load_month_buttons(1); ?></td>
                </tr>
            </tbody>          
        </table>
        <div id="search_div" style="margin-top:5px"></div>
    </form>
 </div>
 </body>           
 <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
 </html>
 <?
 exit();
}

if($action=="create_po_search_list_view")
{
	$data=explode('_',$data);
	if($data[0]==0 && $data[1]==0)
	{
		echo "<span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Please select Company or Buyer first.";
		die;
	} 
	if ($data[0]!=0) $company=" and company_id='$data[0]'"; else $company="";
	
	if ($data[1]!=0) $buyer=" and buyer_id='$data[1]'"; else $$buyer="";//{ echo "Please Select Buyer First."; die; }
	if($db_type==0) { $year_cond=" and SUBSTRING_INDEX(`insert_date`, '-', 1)=$data[6]";  $insert_year="SUBSTRING_INDEX(`insert_date`, '-', 1) as year";  }
	if($db_type==2) {$year_cond=" and to_char(insert_date,'YYYY')=$data[6]";   $insert_year="to_char(insert_date,'YYYY') as year";}
 
	$req_cond="";
	$order_cond=""; 
	$style_cond="";
	if($data[8]==1)
	{
		if (str_replace("'","",$data[5])!="") $req_cond=" and system_number_prefix_num='$data[5]' $year_cond";   
		 
		if (trim($data[7])!="") $style_cond=" and style_refernce ='$data[7]'";  
	}
	else if($data[8]==2)
	{
		if (str_replace("'","",$data[5])!="") $req_cond=" and system_number_prefix_num like '$data[5]%' $year_cond";  
		if (trim($data[7])!="") $style_cond=" and style_refernce like '$data[7]%'  "; 
	}
	else if($data[8]==3)
	{
		if (str_replace("'","",$data[5])!="") $req_cond=" and system_number_prefix_num like '%$data[5]' $year_cond";  
		if (trim($data[7])!="") $style_cond=" and style_refernce like '%$data[7]'";  
	}
	else if($data[8]==4 || $data[9]==0)
	{
		if (str_replace("'","",$data[5])!="") $req_cond=" and system_number_prefix_num like '%$data[5]%' $year_cond"; 
		if (trim($data[7])!="") $style_cond=" and style_refernce like '%$data[7]%'";  
	}
	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and requisition_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	else if($db_type==2 || $db_type==1)
	{
		if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and requisition_date between '".date("j-M-Y",strtotime($data[2]))."' and '".date("j-M-Y",strtotime($data[3]))."'"; else $shipment_date ="";
	}
	 
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$arr=array (2=>$comp,3=>$buyer_arr);
	
	
	$sql= "select system_number_prefix_num,system_number,buyer_inquiry_no, company_id,buyer_id,season,requisition_date,style_refernce,status_active,extract(year from insert_date) as year, season_year, id, brand_id 
	from wo_hand_loom_requisition_mst where is_deleted=0 $company $buyer $shipment_date $req_cond $style_cond order by id DESC ";
	
	echo create_list_view("list_view", "Requition No,Year,Company,Buyer Name,Style Ref. No", "90,90,150,150,100","800","240",0, $sql , "js_set_value", "id", "", 1, "0,0,company_id,buyer_id,0,0,0,0", $arr , "system_number_prefix_num,year,company_id,buyer_id,style_refernce", "",'','0,0,0,0,0,1,0,2,3') ;
	 
	exit();
} 

if ($action=="populate_data_from_search_popup")
{
	$data_array=sql_select("select system_number_prefix_num,system_number,buyer_inquiry_no, company_id,buyer_id,season,requisition_date,style_refernce,status_active,extract(year from insert_date) as year, season_year, id, location_id,dealing_marchant,team_leader from wo_hand_loom_requisition_mst where id=$data and is_deleted=0   order by id DESC");
	foreach ($data_array as $row)
	{
		echo "document.getElementById('txt_requisition_no').value = '".$row[csf("system_number")]."';\n";  
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";  
		echo "document.getElementById('cbo_location_name').value = '".$row[csf("location_id")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";  
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_refernce")]."';\n";  
		echo "document.getElementById('cbo_team_leader').value = '".$row[csf("team_leader")]."';\n";  
		echo "document.getElementById('cbo_dealing_merchant').value = '".$row[csf("dealing_marchant")]."';\n"; 
	}
}
 

if($action=="lapdip_approval_list_view_edit")
{
	$data=explode("**",$data);
	$req_id=$data[0]; $type=$data[1]; $color_array=array(); $po_id=''; $color_arr=array();
	 
	$fabric_construction_name_arr=return_library_array( "select id,fabric_construction_name from lib_fabric_construction", "id", "fabric_construction_name" );
	$color_arr=return_library_array( "select id, color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");
	$poIdsArr=array(); $allPoIdArr=array();
	 
	
	// $colorDataEc=sql_select("select b.id, b.color_name from wo_po_lapdip_approval_info a, lib_color b where a.color_name_id=b.id and a.job_no_mst='$job_no' and a.color_name_id not in(".implode(",",array_keys($color_arr)).") group by b.id, b.color_name");
	// foreach($colorDataEc as $row)
	// {
 	//$color_arr[$row[csf('id')]]=$row[csf('color_name')];
	// }
	
	$sql="select id, requisition_id, color_name_id, lapdip_target_approval_date, send_to_factory_date, recv_from_factory_date, submitted_to_buyer, approval_status, approval_status_date,lapdip_no,lapdip_comments,status_active from wo_po_lapdip_approval_info where requisition_id='$req_id'   order by color_name_id,id";
	$dataArray=sql_select($sql);

	foreach($dataArray as $row)
	{
		$reqColorArr[$row[csf("color_name_id")]]=$row[csf("color_name_id")];
		
		$color_lapdip_wise_arr[$row[csf("color_name_id")]][$row[csf("lapdip_no")]]['lapdip_target_approval_date']=$row[csf("lapdip_target_approval_date")];
		$color_lapdip_wise_arr[$row[csf("color_name_id")]][$row[csf("lapdip_no")]]['send_to_factory_date']=$row[csf("send_to_factory_date")];;
		$color_lapdip_wise_arr[$row[csf("color_name_id")]][$row[csf("lapdip_no")]]['recv_from_factory_date']=$row[csf("recv_from_factory_date")];
		$color_lapdip_wise_arr[$row[csf("color_name_id")]][$row[csf("lapdip_no")]]['submitted_to_buyer']=$row[csf("submitted_to_buyer")];
		$color_lapdip_wise_arr[$row[csf("color_name_id")]][$row[csf("lapdip_no")]]['approval_status']=$row[csf("approval_status")];
		$color_lapdip_wise_arr[$row[csf("color_name_id")]][$row[csf("lapdip_no")]]['approval_status_date']=$row[csf("approval_status_date")];;
		$color_lapdip_wise_arr[$row[csf("color_name_id")]][$row[csf("lapdip_no")]]['lapdip_no']=$row[csf("lapdip_no")];;
		$color_lapdip_wise_arr[$row[csf("color_name_id")]][$row[csf("lapdip_no")]]['lapdip_comments']=$row[csf("lapdip_comments")];;
		$color_lapdip_wise_arr[$row[csf("color_name_id")]][$row[csf("lapdip_no")]]['status_active']=$row[csf("status_active")];;
		$color_lapdip_wise_arr[$row[csf("color_name_id")]][$row[csf("lapdip_no")]]['id']=$row[csf("id")];;
	}

	$sql2="SELECT id dtls_id, mst_id,inquiry_dtls_id, constuction_id, product_type, composition_id, weave_design, finish_type, color_id, fabric_weight, fabric_weight_type,finish_width, cutable_width, wash_type, offer_qnty, uom,buyer_target_price,amount,hl_no,determination_id from wo_hand_loom_requisition_dtls where mst_id=$req_id and  is_deleted=0  and status_active=1 order by id ASC";


	$dataArray2=sql_select($sql2);
	foreach($dataArray2 as $row)
	{
		$reqColorArr[$row[csf("color_id")]]=$row[csf("color_id")];
		$req_color_wise_arr[$row[csf("color_id")]]['constuction']=$fabric_construction_name_arr[$row[csf("constuction_id")]];
		$req_color_wise_arr[$row[csf("color_id")]]['composition']=$composition[$row[csf("composition_id")]];
		$req_color_wise_arr[$row[csf("color_id")]]['mst_id']=$row[csf("mst_id")];
		$req_color_wise_arr[$row[csf("color_id")]]['dtls_id']=$row[csf("dtls_id")];;
		 
	}
	//  echo "<pre>";
	//  print_r($color_lapdip_wise_arr);
	$z=1; $i=1;
	

 

	if(count($color_lapdip_wise_arr)>0){

		foreach($color_lapdip_wise_arr as $color_id=>$lapdip_data)
		{
			 
			$constuction=$req_color_wise_arr[$color_id]['constuction'];
			$composition=$req_color_wise_arr[$color_id]['composition'];
			$mst_id=$req_color_wise_arr[$color_id]['mst_id'];
			$dtls_id=$req_color_wise_arr[$color_id]['dtls_id'];
		
			
		  ?>
			<h3 align="left" id="accordion_h<? echo $color_id; ?>" style="width:1275px" class="accordion_h"> +<? echo $color_arr[$color_id]; ?></h3>
			<div id="content_search_panel_<? echo $color_id; ?>"   class="accord_close">
				<table class="rpt_table" border="1" width="1275" cellpadding="0" cellspacing="0" rules="all" id="table_<?=$color_id; ?>">
					<thead>
						<th>Constuction</th>
						<th>Composition</th>
						<th>Color Name</th>
						<th>Target Approval Date</th>
						<th>Sent To Lab Section</th>
                        <th>Recv. From Lab Section</th>
						<th>Submitted To Buyer</th>
						<th>Action</th>
						<th>Action Date</th>
						<th>Lapdip No</th>
						<th>Comments</th>
						<th>Status</th>
						<th colspan="2">
							<input type="hidden" name="current_status_<? echo $color_id; ?>" id="current_status_<? echo $color_id; ?>" value="" style="width:75px;" class="text_boxes" readonly>
							<input type="hidden" name="hiddReqDtlsId_<?=$z; ?>" id="hiddReqDtlsId_<?=$z; ?>" value="<?=$dtls_id; ?>">
							<input type="hidden" name="colorId_<?=$z; ?>" id="colorId_<?=$z; ?>" value="<? echo $color_id; ?>">
							<input type="hidden" name="numberOfColor" id="numberOfColor" value="<?=count($color_lapdip_wise_arr); ?>">
							<input type="hidden" name="hiddReqId" id="hiddReqId" value="<?=$mst_id; ?>" >
							&nbsp;
						</th>
					</thead>
					<tbody>
		 
					<?
					
					$z++;$i=1;
					 
					foreach($lapdip_data as  $val)
						{
							 
							 
					?>
						<tr align="center" id="tr">
							<td>
								<input type="text" name="constuction_id_<? echo $color_id.'_'.$i; ?>" id="constuction_id_<? echo $color_id.'_'.$i; ?>" value="<?=$constuction; ?>" style="width:90px;" class="text_boxes" disabled="disabled">
								
							</td>
							<td>
								<input type="text" name="composition_id_<? echo $color_id.'_'.$i; ?>" id="composition_id_<? echo $color_id.'_'.$i; ?>" value="<?=$composition; ?>" style="width:90px;" class="text_boxes" disabled="disabled">
							</td>
							<td>
								<?
									echo create_drop_down("color_".$color_id."_".$i, 90, $color_arr,"", 1,'', $color_id,"",1);
								?>
								
							</td>
							
							<td>
								<input type="text" name="targetAppDate_<? echo $color_id.'_'.$i; ?>" id="targetAppDate_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" value="<? if($val['lapdip_target_approval_date']!="0000-00-00") echo change_date_format($val['lapdip_target_approval_date']);?>">
							</td>
							<td>
								<input type="text" name="sendToFactoryDate_<? echo $color_id.'_'.$i; ?>" id="sendToFactoryDate_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" value="<? if($val['send_to_factory_date']!="0000-00-00") echo change_date_format($val['send_to_factory_date']);?>"  >
							</td>
							<td>
								<input type="text" name="recvFromFactoryDate_<? echo $color_id.'_'.$i; ?>" id="recvFromFactoryDate_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" value="<? if($val['recv_from_factory_date']!="0000-00-00") echo change_date_format($val['recv_from_factory_date']);?>"  >
							</td>
							<td>
								<input type="text" name="submittedToBuyer_<? echo $color_id.'_'.$i; ?>" id="submittedToBuyer_<? echo $color_id.'_'.$i; ?>" style="width:80px;" class="datepicker" value="<? if($val['submitted_to_buyer']!="0000-00-00") echo change_date_format($val['submitted_to_buyer']);?>"  >
							</td>
							<td>
								<?
									echo create_drop_down("action_".$color_id."_".$i, 90, $approval_status,"", 1, "--select  --",$val["approval_status"],"");
								?>
							</td>
							<td>
								<input type="text" name="actionDate_<? echo $color_id.'_'.$i; ?>" id="actionDate_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" value="<? if($val['approval_status_date']!="0000-00-00") echo change_date_format($val['approval_status_date']);?>"  >
							</td>
							<td>
								<input type="text" name="txtLapdipNo_<? echo $color_id.'_'.$i; ?>" id="txtLapdipNo_<? echo $color_id.'_'.$i; ?>" style="width:100px;" class="text_boxes"   value="<?=$val['lapdip_no'];?>">
							</td>
							<td>
								<input type="text" name="txtComments_<? echo $color_id.'_'.$i; ?>" id="txtComments_<? echo $color_id.'_'.$i; ?>" style="width:100px;" class="text_boxes"  placeholder="Single Click" onClick="fnc_comments(this.id,this.value)" readonly value="<?=$val['lapdip_comments'];?>">
							</td>
							<td>
								<?
									echo create_drop_down("cboStatus_".$color_id."_".$i, 80, $row_status,"", 0,"",$val['status_active'],"",0);
								?>
								<input type="hidden" name="updateid_<? echo $color_id.'_'.$i; ?>" id="updateid_<? echo $color_id.'_'.$i; ?>" value="<?=$val['id'];?>">
							</td>
							<td>
								<input type="button" id="increase_<? echo $color_id.'_'.$i; ?>" name="increase_<? echo $color_id.'_'.$i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $color_id.','.$i; ?>)" />
                                <input type="button" id="decrease_<? echo $color_id.'_'.$i; ?>" name="decrease_<? echo $color_id.'_'.$i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $color_id.','.$i; ?>);" />
								<input type="hidden" name="colorRow_<?=$color_id; ?>" id="colorRow_<?=$color_id; ?>" value="">
							</td>
						</tr>
						<?php $i++;} ?>
					
					</tbody>
				</table>
			</div>
			<?	
			
					
		}

	}else{

	
		foreach($req_color_wise_arr as $color_id=>$val)
		{
		  ?>
			<h3 align="left" id="accordion_h<? echo $color_id; ?>" style="width:1275px" class="accordion_h"> +<? echo $color_arr[$color_id]; ?></h3>
			<div id="content_search_panel_<? echo $color_id; ?>"   class="accord_close">
				<table class="rpt_table" border="1" width="1275" cellpadding="0" cellspacing="0" rules="all" id="table_<?=$color_id; ?>">
					<thead>
						<th>Constuction</th>
						<th>Composition</th>
						<th>Color Name</th>
						<th>Target Approval Date</th>
						<th>Sent To Lab Section</th>
                        <th>Recv. From Lab Section</th>
						<th>Submitted To Buyer</th>
						<th>Action</th>
						<th>Action Date</th>
						<th>Lapdip No</th>
						<th>Comments</th>
						<th>Status</th>
						<th>
							<input type="hidden" name="current_status_<? echo $color_id; ?>" id="current_status_<? echo $color_id; ?>" value="" style="width:75px;" class="text_boxes" readonly>
							<input type="hidden" name="hiddReqDtlsId_<?=$z; ?>" id="hiddReqDtlsId_<?=$z; ?>" value="<?=$val['dtls_id']; ?>">
							<input type="hidden" name="colorId_<?=$z; ?>" id="colorId_<?=$z; ?>" value="<? echo $color_id; ?>">
							<input type="hidden" name="numberOfColor" id="numberOfColor" value="<?=count($req_color_wise_arr); ?>">
							<input type="hidden" name="hiddReqId" id="hiddReqId" value="<?=$val['mst_id']; ?>" >
						</th>
					</thead>
					<tbody>
		 
					<?
					$i=1;
					$z++;
					 
					?>
						<tr align="center" id="tr">
							<td>
								<input type="text" name="constuction_id_<? echo $color_id.'_'.$i; ?>" id="constuction_id_<? echo $color_id.'_'.$i; ?>" value="<? echo $val['constuction']; ?>" style="width:90px;" class="text_boxes" disabled="disabled">
								
							</td>
							<td>
								<input type="text" name="composition_id_<? echo $color_id.'_'.$i; ?>" id="composition_id_<? echo $color_id.'_'.$i; ?>" value="<? echo $val['composition']; ?>" style="width:90px;" class="text_boxes" disabled="disabled">
							</td>
							<td>
								<?
									echo create_drop_down("color_".$color_id."_".$i, 90, $color_arr,"", 1,'', $color_id,"",1);
								?>
								
							</td>
							<td>
								<input type="text" name="targetAppDate_<? echo $color_id.'_'.$i; ?>" id="targetAppDate_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" >
							</td>
							<td>
								<input type="text" name="sendToFactoryDate_<? echo $color_id.'_'.$i; ?>" id="sendToFactoryDate_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" >
							</td>
							<td>
								<input type="text" name="recvFromFactoryDate_<? echo $color_id.'_'.$i; ?>" id="recvFromFactoryDate_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" >
							</td>
							<td>
								<input type="text" name="submittedToBuyer_<? echo $color_id.'_'.$i; ?>" id="submittedToBuyer_<? echo $color_id.'_'.$i; ?>" style="width:80px;" class="datepicker" >
							</td>
							<td>
								<?
									echo create_drop_down("action_".$color_id."_".$i, 90, $approval_status,"", 1, "--   --","","");
								?>
							</td>
							<td>
								<input type="text" name="actionDate_<? echo $color_id.'_'.$i; ?>" id="actionDate_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" >
							</td>
							<td>
								<input type="text" name="txtLapdipNo_<? echo $color_id.'_'.$i; ?>" id="txtLapdipNo_<? echo $color_id.'_'.$i; ?>" style="width:100px;" class="text_boxes" >
							</td>
							<td>
								<input type="text" name="txtComments_<? echo $color_id.'_'.$i; ?>" id="txtComments_<? echo $color_id.'_'.$i; ?>" style="width:100px;" class="text_boxes"  placeholder="Single Click" onClick="fnc_comments(this.id,this.value)" readonly>
							</td>
							<td>
								<?
									echo create_drop_down("cboStatus_".$color_id."_".$i, 80, $row_status,"", 0,"","","",0);
								?>
								<input type="hidden" name="updateid_<? echo $color_id.'_'.$i; ?>" id="updateid_<? echo $color_id.'_'.$i; ?>" value="">
							</td>
							<td>
								<input type="button" id="increase_<? echo $color_id.'_'.$i; ?>" name="increase_<? echo $color_id.'_'.$i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $color_id.','.$i; ?>)" />
                                <input type="button" id="decrease_<? echo $color_id.'_'.$i; ?>" name="decrease_<? echo $color_id.'_'.$i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $color_id.','.$i; ?>);" />
								<input type="hidden" name="colorRow_<?=$color_id; ?>" id="colorRow_<?=$color_id; ?>" value="">
							</td>
						</tr>
					
					</tbody>
				</table>
			</div>
			<?	
					$i++;
		}

	}
					
		
	
	 
					exit();
}



if($action=="comments_popup")
{ 
	echo load_html_head_contents("Comments Info", "../../../", 1, 1,'','','');
	extract($_REQUEST); 
 ?>
    
 </head>

 <body>
 <div style="width:430px;" align="center">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:400px; margin-top:10px;">
             <table cellspacing="0" cellpadding="0" border="1" rules="all" width="400" class="rpt_table" >
                <tr>
               		<td><textarea name="txt_comments" id="txt_comments" class="text_area" style="width:385px; height:120px;"><? echo $comments_data; ?></textarea></td>
                </tr>
            </table>
            <table width="400" id="tbl_close">
                 <tr>
                    <td align="center" >
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:100px" />
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
?>