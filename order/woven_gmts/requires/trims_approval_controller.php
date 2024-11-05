<? 
session_start();
include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];
//---------------------------------------------------- Start
$trims_library=return_library_array( "select id,item_name from lib_item_group", "id", "item_name"  );

if($action=="load_drop_down_buyer")
{
	if($data != 0) $comCond="and b.tag_company=$data"; else  $comCond="";
	echo create_drop_down( "cbo_buyer_name", 172, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $comCond $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );  
	exit();
}

if($action=="save_update_delete")
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
		
		$id=return_next_id( "id", "wo_po_trims_approval_info", 1 ) ;
		
		$field_array="id,job_no_mst,po_break_down_id, 	accessories_type_id,target_approval_date,sent_to_supplier,submitted_to_buyer,approval_status,approval_status_date,supplier_name,accessories_comments,is_deleted,status_active,inserted_by,insert_date,garments_nature"; 
		
		for($i=1; $i<=$tot_row; $i++)
		{
			$po_id="po_id_".$item_group_id."_".$i;
			$item_group="item_group_id_".$item_group_id."_".$i;
			$target_app_date="target_app_date_".$item_group_id."_".$i;
			$sent_to_suppl="sent_to_suppl_".$item_group_id."_".$i;
			$sent_to_buyer="sent_to_buyer_".$item_group_id."_".$i;
			$action="action_".$item_group_id."_".$i;
			$action_date="action_date_".$item_group_id."_".$i;
			$cbo_supplier="cbo_supplier_".$item_group_id."_".$i;
			$txt_comments="txt_comments_".$item_group_id."_".$i;
			$cbo_status="cbo_status_".$item_group_id."_".$i;
			
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",'".$txt_job_no."',".$$po_id.",".$$item_group.",".$$target_app_date.",".$$sent_to_suppl.",".$$sent_to_buyer.",".$$action.",".$$action_date.",".$$cbo_supplier.",".$$txt_comments.",0,".$$cbo_status.",".$user_id.",'".$pc_date_time."',$garments_nature)";
			
			$id=$id+1;
		}

		$rID=sql_insert("wo_po_trims_approval_info",$field_array,$data_array,1);
		 
		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "0**".$txt_job_no;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**".$txt_job_no;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);    
				echo "0**".$txt_job_no;
			}
			else
			{
				oci_rollback($con); 
				echo "5**".$txt_job_no;
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
		
		$id=return_next_id( "id", "wo_po_trims_approval_info", 1 ) ;
		$data_array="";
		
		$field_array="id,job_no_mst,po_break_down_id, 	accessories_type_id,target_approval_date,sent_to_supplier,submitted_to_buyer,approval_status,approval_status_date,supplier_name,accessories_comments,is_deleted,status_active,inserted_by,insert_date,garments_nature";
		$field_array_update="job_no_mst*po_break_down_id*accessories_type_id*target_approval_date*sent_to_supplier*submitted_to_buyer*approval_status*approval_status_date*supplier_name*accessories_comments*is_deleted*status_active*updated_by*update_date*garments_nature";
		
		for($i=1; $i<=$tot_row; $i++)
		{
			$po_id="po_id_".$item_group_id."_".$i;
			$item_group="item_group_id_".$item_group_id."_".$i;
			$target_app_date="target_app_date_".$item_group_id."_".$i;
			$sent_to_suppl="sent_to_suppl_".$item_group_id."_".$i;
			$sent_to_buyer="sent_to_buyer_".$item_group_id."_".$i;
			$action="action_".$item_group_id."_".$i;
			$action_date="action_date_".$item_group_id."_".$i;
			$cbo_supplier="cbo_supplier_".$item_group_id."_".$i;
			$txt_comments="txt_comments_".$item_group_id."_".$i;
			$cbo_status="cbo_status_".$item_group_id."_".$i;
			$updateid="updateid_".$item_group_id."_".$i;
			
			 if(str_replace("'",'',$$updateid)!="")
			 {
				$id_arr[]=str_replace("'",'',$$updateid);
				$data_array_update[str_replace("'",'',$$updateid)] = explode(",",("'".$txt_job_no."',".$$po_id.",".$$item_group.",".$$target_app_date.",".$$sent_to_suppl.",".$$sent_to_buyer.",".$$action.",".$$action_date.",".$$cbo_supplier.",".$$txt_comments.",0,".$$cbo_status.",".$user_id.",'".$pc_date_time."',$garments_nature"));
			 }
			 else
			 {
				if($data_array!="") $data_array.=","; 
			$data_array .="(".$id.",'".$txt_job_no."',".$$po_id.",".$$item_group.",".$$target_app_date.",".$$sent_to_suppl.",".$$sent_to_buyer.",".$$action.",".$$action_date.",".$$cbo_supplier.",".$$txt_comments.",0,".$$cbo_status.",".$user_id.",'".$pc_date_time."',$garments_nature)";
			
				$id=$id+1;
			 }
		}
		
		$flag=1;
		if($data_array_update!="")
		{
			$rID=execute_query(bulk_update_sql_statement( "wo_po_trims_approval_info", "id", $field_array_update, $data_array_update, $id_arr ),1);
			if($rID) $flag=1; else $flag=0;
		}
		
		if($data_array!="")
		{
			$rID2=sql_insert("wo_po_trims_approval_info",$field_array,$data_array,1);
			if($flag==1) {if($rID2) $flag=1; else $flag=0; } 
		}
		if($current_status!="")
		{
			$field_array_status="updated_by*update_date*current_status";
			$data_array_status=$user_id."*'".$pc_date_time."'*0";
	
			$rID3=sql_multirow_update("wo_po_trims_approval_info",$field_array_status,$data_array_status,"id",$current_status,1);
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=0; 
			}
		}
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".$txt_job_no;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**".$txt_job_no;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con); 
				echo "1**".$txt_job_no;
			}
			else
			{
				oci_rollback($con);
				echo "6**".$txt_job_no;
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

		$rID=sql_delete("wo_po_trims_approval_info",$field_array,$data_array,"job_no_mst*accessories_type_id","'".$txt_job_no."'"."*".$item_group_id,1);
		
		if($db_type==0)
		{
			
			if($rID)
			{
				mysql_query("COMMIT");
				echo "2**".$txt_job_no;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "7**".$txt_job_no;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			
	  		if($rID)
			{
				oci_commit($con); 
				echo "2**".$txt_job_no;
			}
			else
			{
				oci_rollback($con);
				echo "7**".$txt_job_no;
			}
		
		}
		disconnect($con);
		die;
	}
}
 
if ($action=="order_popup")
{
  	echo load_html_head_contents("Trims Approval Info","../../../", 1, 1, '','','');
	$garments_nature=$_REQUEST['garments_nature'];
?>
     
	<script>
	function set_checkvalue()
	{
		if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
		else document.getElementById('chk_job_wo_po').value=0;
	}
	
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
    <table width="870" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
        <thead> 
            <tr>
                <th colspan="8"><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
            </tr> 
            <tr>                   	 
                <th width="150" class="must_entry_caption">Company Name</th>
                <th width="150" class="must_entry_caption">Buyer Name</th>
                <th width="80">Job No</th>
                <th width="90">Style Ref </th>
                <th width="90">Order No</th>
                <th width="130">Date Range</th>
                <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">Job Without PO</th> 
            </tr>          
        </thead>
        <tr class="general">
            <td> <input type="hidden" id="selected_job">
                <? 
                    echo create_drop_down( "cbo_company_name", 160, "select comp.id,comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'trims_approval_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                ?>
            </td>
            <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 172, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --",$selected ); ?></td>
            <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:70px"></td>
            <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:80px"></td>
            <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:80px"></td>
            <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" readonly></td>
            <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" readonly></td> 
             <td align="center">
             <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $garments_nature; ?>+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_po_search_list_view', 'search_div', 'trims_approval_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        </tr>
        <tr>
            <td colspan="8" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
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

if($action=="create_po_search_list_view")
{
	$data=explode('_',$data);
	if($data[0]==0 && $data[1]==0)
	{
		echo "<span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Please select Company or Buyer first.";
		die;
	}
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else  $company="";
	
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($db_type==0) { $year_cond=" and SUBSTRING_INDEX(a.`insert_date`, '-', 1)=$data[7]";  $insert_year="SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year";  }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[7]";   $insert_year="to_char(a.insert_date,'YYYY') as year";}
	//if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num='$data[6]'  $year_cond "; else  $job_cond=""; 
	//if (str_replace("'","",$data[8])!="") $order_cond=" and b.po_number like '%$data[8]%'  "; else  $order_cond=""; 
	$job_cond="";
	$order_cond=""; 
	$style_cond="";
	if($data[10]==1)
	{
	if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num='$data[6]' $year_cond"; //else  $job_cond=""; 
	if (str_replace("'","",$data[8])!="") $order_cond=" and b.po_number = '$data[8]'  "; //else  $order_cond=""; 
	if (trim($data[9])!="") $style_cond=" and a.style_ref_no ='$data[9]'"; //else  $style_cond=""; 
	}
	if($data[10]==2)
	{
	if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '$data[6]%' $year_cond"; //else  $job_cond=""; 
	if (str_replace("'","",$data[8])!="") $order_cond=" and b.po_number like '$data[8]%'  "; //else  $order_cond=""; 
	if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '$data[9]%'  "; //else  $style_cond=""; 
	}
	if($data[10]==3)
	{
	if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]' $year_cond"; //else  $job_cond=""; 
	if (str_replace("'","",$data[8])!="") $order_cond=" and b.po_number like '%$data[8]'  "; //else  $order_cond=""; 
	if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '%$data[9]'"; //else  $style_cond=""; 
	}
	if($data[10]==4 || $data[10]==0)
	{
	if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]%' $year_cond"; //else  $job_cond=""; 
	if (str_replace("'","",$data[8])!="") $order_cond=" and b.po_number like '%$data[8]%'  "; //else  $order_cond=""; 
	if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '%$data[9]%'"; //else  $style_cond=""; 
	}
	if($db_type==0)
		{
	     if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
		}
	
	if($db_type==2)
		{
       	if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
		}
	 
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$arr=array (2=>$comp,3=>$buyer_arr);
	
	if($data[2]==0)
	{
		$sql= "select a.job_no_prefix_num,$insert_year, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.po_quantity,b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and a.garments_nature=$data[5] $shipment_date $company $buyer $job_cond $style_cond $order_cond order by a.job_no";  
		 
		echo create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date", "50,60,120,140,100,90,100,90,80","900","250",0, $sql , "js_set_value", "job_no", "", 1, "0,0,company_name,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date", "",'','0,0,0,0,0,1,0,1,3');
	}
	else
	{
		$sql= "select a.job_no_prefix_num,$insert_year, a.job_no,a.company_name,a.buyer_name,a.style_ref_no from wo_po_details_master a where a.status_active=1 and a.garments_nature=$data[5] and a.is_deleted=0 $company $buyer $job_cond $style_cond order by a.job_no";
		
		echo create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No", "90,120,100,100,90","600","250",0, $sql , "js_set_value", "job_no", "", 1, "0,0,company_name,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no", "",'','0,0,0,0,1,0,2,3') ;
	}
	exit();
} 

if ($action=="populate_data_from_search_popup")
{
	$data_array=sql_select("select id, job_no, company_name, buyer_name, location_name, style_ref_no, style_description, product_dept, currency_id, agent_name, region, team_leader, dealing_marchant from wo_po_details_master where job_no='$data'");
	foreach ($data_array as $row)
	{
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";  
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";  
		echo "document.getElementById('cbo_location_name').value = '".$row[csf("location_name")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";  
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref_no")]."';\n";  
		echo "document.getElementById('txt_style_description').value = '".$row[csf("style_description")]."';\n";  
		echo "document.getElementById('cbo_product_department').value = '".$row[csf("product_dept")]."';\n";  
		echo "document.getElementById('cbo_currercy').value = '".$row[csf("currency_id")]."';\n";  
		echo "document.getElementById('cbo_agent').value = '".$row[csf("agent_name")]."';\n";  
		echo "document.getElementById('cbo_region').value = '".$row[csf("region")]."';\n";  
		echo "document.getElementById('cbo_team_leader').value = '".$row[csf("team_leader")]."';\n";  
		echo "document.getElementById('cbo_dealing_merchant').value = '".$row[csf("dealing_marchant")]."';\n"; 
		echo "load_drop_down('requires/trims_approval_controller','".$row[csf("job_no")]."', 'load_drop_down_trims_name', 'load_item');\n";
	}
}

if($action=="load_drop_down_trims_name")
{  
	echo create_drop_down( "cbo_trims_name", 172, "select a.trim_group, b.item_name from wo_pre_cost_trim_cost_dtls a, lib_item_group b where a.trim_group=b.id and a.job_no='$data' and a.apvl_req=1 and a.is_deleted=0 and a.status_active=1 and a.trim_group not in(select accessories_type_id from wo_po_trims_approval_info where job_no_mst='$data' and is_deleted=0 and status_active=1) group by a.trim_group,b.item_name order by b.item_name","trim_group,item_name", 1, "-- Select Trims --", '', "show_list_view(document.getElementById('txt_job_no').value+'**'+1+'**'+this.value, 'trims_approval_list_view_edit','trims_approval_list_view','requires/trims_approval_controller','$(\'#hide_item_group_id\').val(\'\')');");  
	exit();
}

if($action=="trims_approval_list_view_edit")
{
	$data=explode("**",$data);
	$job_no=$data[0];
	$type=$data[1];
	
	$po_number_arr=return_library_array( "select id, po_number from wo_po_break_down where job_no_mst='$job_no'",'id','po_number');
	$item_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$supplier_arr=return_library_array( "select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(4,5) and status_active =1 and is_deleted=0 group by a.id, a.supplier_name order by a.supplier_name",'id','supplier_name');
	
	$item_group_array=array();
	$sql="select id, po_break_down_id, accessories_type_id, target_approval_date, sent_to_supplier, submitted_to_buyer, approval_status, approval_status_date, supplier_name, accessories_comments, status_active from wo_po_trims_approval_info where job_no_mst='$job_no' and is_deleted=0 order by accessories_type_id,po_break_down_id,id";
	
	$dataArray=sql_select($sql);
	$z=1; $i=1;
	
	foreach($dataArray as $row)
	{
		if($row[csf("approval_status")]==2 || $row[csf("approval_status")]==3)
		{
			$disable="disabled='disabled'";
			$disable_status=1;
		}
		else
		{
			$disable="";
			$disable_status=0;
		}
		
		$item_group_id=$row[csf("accessories_type_id")];
		if(in_array($item_group_id,$item_group_array))
		{
			$print_cond_header=0;
			$print_cond_footer=0;
        }
		else
		{
			$print_cond_header=1;
			$i=1;
			if($z==1) $print_cond_footer=0; else $print_cond_footer=1;
			$item_group_array[]=$item_group_id;
		}
		
		if($print_cond_footer==1)
		{
        ?>
                </table>
            </div>
		<?
		}
		if($print_cond_header==1)
		{
		?>
            <h3 align="left" id="accordion_h<? echo $item_group_id; ?>" style="width:1075px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel_<? echo $item_group_id; ?>', 'fnc_item_group_id(<? echo $item_group_id; ?>,1)',1)"> +<? echo $item_arr[$item_group_id]; ?></h3>
            <div id="content_search_panel_<? echo $item_group_id; ?>" style="display:none" class="accord_close">
                <table class="rpt_table" border="1" width="1075" cellpadding="0" cellspacing="0" rules="all" id="table_<? echo $item_group_id; ?>">
                    <thead>
                        <th>Po Number</th>
                        <th>Item Name</th>
                        <th>Target Approval Date</th>
                        <th>Sent To Supplier</th>
                        <th>Sent To Buyer</th>
                        <th>Action</th>
                        <th class="must_entry_caption">Action Date</th>
                        <th>Supplier Name</th>
                        <th>Comments</th>
                        <th>Status</th>
                        <th><input type="hidden" name="current_status_<? echo $item_group_id; ?>" id="current_status_<? echo $item_group_id; ?>" value="" style="width:75px;" class="text_boxes" readonly></th>
                    </thead>
		<?
		}
        ?>
                <tbody>
                    <tr align="center">
                        <td>
                            <?
                                echo create_drop_down("po_no_".$item_group_id."_".$i, 110, $po_number_arr,"", 1,'', $row[csf("po_break_down_id")],"",1);
                            ?>
                            <input type="hidden" name="po_id_<? echo $item_group_id.'_'.$i; ?>" id="po_id_<? echo $item_group_id.'_'.$i; ?>" value="<? echo $row[csf("po_break_down_id")]; ?>" style="width:110px;" class="text_boxes" disabled="disabled">
                        </td>
                        <td>
                            <?
                                echo create_drop_down("item_group_".$item_group_id."_".$i, 110, $item_arr,"", 1,'', $item_group_id,"",1);
                            ?>
                            <input type="hidden" name="item_group_id_<? echo $item_group_id.'_'.$i; ?>" id="item_group_id_<? echo $item_group_id.'_'.$i; ?>" value="<? echo $item_group_id; ?>" style="width:110px;" class="text_boxes" disabled="disabled">
                        </td>
                        <td>
                            <input type="text" name="target_app_date_<? echo $item_group_id.'_'.$i; ?>" id="target_app_date_<? echo $item_group_id.'_'.$i; ?>" style="width:80px;" class="datepicker" onChange="copy_value(this.value,'target_app_date_',<? echo $i; ?>)" value="<? if($row[csf("target_approval_date")]!="0000-00-00" && $row[csf("target_approval_date")]!="") echo change_date_format($row[csf("target_approval_date")]);?>" <? echo $disable;?> >
                        </td>
                        <td>
                            <input type="text" name="sent_to_suppl_<? echo $item_group_id.'_'.$i; ?>" id="sent_to_suppl_<? echo $item_group_id.'_'.$i; ?>" style="width:80px;" class="datepicker" onChange="copy_value(this.value,'sent_to_suppl_',<? echo $i; ?>)" value="<? if($row[csf("sent_to_supplier")]!="") echo change_date_format($row[csf("sent_to_supplier")]); ?>" <? echo $disable; ?> >
                        </td>
                        <td>
                            <input type="text" name="sent_to_buyer_<? echo $item_group_id.'_'.$i; ?>" id="sent_to_buyer_<? echo $item_group_id.'_'.$i; ?>" style="width:80px;" class="datepicker" onChange="copy_value(this.value,'sent_to_buyer_',<? echo $i; ?>)" value="<? if($row[csf("submitted_to_buyer")]!="") echo change_date_format($row[csf("submitted_to_buyer")]); ?>" <? echo $disable; ?> >
                        </td>
                        <td>
                            <?
                                echo create_drop_down("action_".$item_group_id."_".$i, 90, $approval_status,"", 1, "--   --",$row[csf("approval_status")],"copy_value(this.value,'action_',".$i.")",$disable_status);
                            ?>
                        </td>
                        <td>
                            <input type="text" name="action_date_<? echo $item_group_id.'_'.$i; ?>" id="action_date_<? echo $item_group_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'action_date_',<? echo $i; ?>)" value="<? if($row[csf("approval_status_date")]!="" && $row[csf("approval_status_date")]!="0000-00-00") echo change_date_format($row[csf("approval_status_date")]); ?>"  <? echo $disable; ?>>
                        </td>
                        <td>
                            <?  
                               // echo create_drop_down( "cbo_supplier_".$item_group_id."_".$i, 100, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(4,5) and status_active =1 and is_deleted=0 group by a.id, a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--   --", $row[csf("supplier_name")], "copy_value(this.value,'cbo_supplier_',".$i.")", $disable_status );
								 echo create_drop_down( "cbo_supplier_".$item_group_id."_".$i, 100, $supplier_arr,"", 1, "--   --", $row[csf("supplier_name")], "copy_value(this.value,'cbo_supplier_',".$i.")", $disable_status );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_comments_<? echo $item_group_id.'_'.$i; ?>" id="txt_comments_<? echo $item_group_id.'_'.$i; ?>" style="width:120px;" class="text_boxes" onChange="copy_value(this.value,'txt_comments_',<? echo $i; ?>)" value="<? echo $row[csf("accessories_comments")]; ?>" <? echo $disable; ?> >
                        </td>
                        <td>
                            <?
                                echo create_drop_down("cbo_status_".$item_group_id."_".$i, 80, $row_status,"", 0, "",$row[csf("status_active")],"copy_value(this.value,'cbo_status_',".$i.")",$disable_status);
                            ?>
                            <input type="hidden" name="updateid_<? echo $item_group_id.'_'.$i;?>" id="updateid_<? echo $item_group_id.'_'.$i; ?>" value="<? echo $row[csf("id")];?>">
                        </td>
                        <td>
                        	<?
							if($row[csf("approval_status")]==2)
							{
							?>
								<input type="button" id="addrow_<? echo $i; ?>"  name="addrow_<? echo $i; ?>" style="width:75px" class="formbutton" value="Re-Submit" onClick="resubmit(<? echo $item_group_id; ?>,<? echo $i; ?>)" />
							<?
                            }
                            ?>
                        </td>
                    </tr>
                </tbody>
		<?
        
		$i++;
		$z++;
	
	}
	if($z>1)
	{
	?>
			</table>
		</div>
	<?
	}

	if($type==1)
	{
		$item_group_id=$data[2];
		if($item_group_id!=0)
		{
		?>
			<h3 align="left" id="accordion_h<? echo $item_group_id; ?>" style="width:1075px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel_<? echo $item_group_id; ?>', 'fnc_item_group_id(<? echo $item_group_id; ?>,0)',1)"> +<? echo $item_arr[$item_group_id]; ?></h3>
			<div id="content_search_panel_<? echo $item_group_id; ?>" style="display:none" class="accord_close">
				<table class="rpt_table" border="1" width="1075" cellpadding="0" cellspacing="0" rules="all" id="table_<? echo $item_group_id; ?>">
					<thead>
						<th>Po Number</th>
						<th>Item Name</th>
						<th>Target Approval Date</th>
						<th>Sent To Supplier</th>
						<th>Sent To Buyer</th>
						<th>Action</th>
						<th>Action Date</th>
						<th>Supplier Name</th>
						<th>Comments</th>
						<th>Status</th>
						<th><input type="hidden" name="current_status_<? echo $item_group_id; ?>" id="current_status_<? echo $item_group_id; ?>" value="" style="width:75px;" class="text_boxes" readonly></th>
					</thead>
					<tbody>
					<?
					$sql="select b.po_break_down_id from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id and a.job_no='$job_no' and a.trim_group=$item_group_id and a.status_active=1 and a.is_deleted=0 group by b.po_break_down_id";
					
					$dataArray=sql_select($sql);
					$i=1;
		
					foreach($dataArray as $row)
					{
					?>
						<tr align="center">
							<td>
								<?
									echo create_drop_down("po_no_".$item_group_id."_".$i, 110, $po_number_arr,"", 1,'', $row[csf("po_break_down_id")],"",1);
								?>
								<input type="hidden" name="po_id_<? echo $item_group_id.'_'.$i; ?>" id="po_id_<? echo $item_group_id.'_'.$i; ?>" value="<? echo $row[csf("po_break_down_id")]; ?>" style="width:110px;" class="text_boxes" disabled="disabled">
							</td>
							<td>
								<?
									echo create_drop_down("item_group_".$item_group_id."_".$i, 110, $item_arr,"", 1,'', $item_group_id,"",1);
								?>
								<input type="hidden" name="item_group_id_<? echo $item_group_id.'_'.$i; ?>" id="item_group_id_<? echo $item_group_id.'_'.$i; ?>" value="<? echo $item_group_id; ?>" style="width:110px;" class="text_boxes" disabled="disabled">
							</td>
							<td>
								<input type="text" name="target_app_date_<? echo $item_group_id.'_'.$i; ?>" id="target_app_date_<? echo $item_group_id.'_'.$i; ?>" style="width:80px;" class="datepicker" onChange="copy_value(this.value,'target_app_date_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="sent_to_suppl_<? echo $item_group_id.'_'.$i; ?>" id="sent_to_suppl_<? echo $item_group_id.'_'.$i; ?>" style="width:80px;" class="datepicker" onChange="copy_value(this.value,'sent_to_suppl_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="sent_to_buyer_<? echo $item_group_id.'_'.$i; ?>" id="sent_to_buyer_<? echo $item_group_id.'_'.$i; ?>" style="width:80px;" class="datepicker" onChange="copy_value(this.value,'sent_to_buyer_',<? echo $i; ?>)">
							</td>
							<td>
								<?
									echo create_drop_down("action_".$item_group_id."_".$i, 90, $approval_status,"", 1, "--   --","","copy_value(this.value,'action_',".$i.")");
								?>
							</td>
							<td>
								<input type="text" name="action_date_<? echo $item_group_id.'_'.$i; ?>" id="action_date_<? echo $item_group_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'action_date_',<? echo $i; ?>)">
							</td>
							<td>
								<?  
									echo create_drop_down( "cbo_supplier_".$item_group_id."_".$i, 100, $supplier_arr,"", 1, "--   --", "", "copy_value(this.value,'cbo_supplier_',".$i.")", 0 );
									//echo create_drop_down( "cbo_supplier_".$item_group_id."_".$i, 100, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(4,5) and status_active =1 and is_deleted=0 group by a.id,a.supplier_name order by supplier_name","id,supplier_name", 1, "--   --", "", "copy_value(this.value,'cbo_supplier_',".$i.")", 0 );
								?>
							</td>
							<td>
								<input type="text" name="txt_comments_<? echo $item_group_id.'_'.$i; ?>" id="txt_comments_<? echo $item_group_id.'_'.$i; ?>" style="width:120px;" class="text_boxes" onChange="copy_value(this.value,'txt_comments_',<? echo $i; ?>)">
							</td>
							<td>
								<?
									echo create_drop_down("cbo_status_".$item_group_id."_".$i, 80, $row_status,"", 0,"","","copy_value(this.value,'cbo_status_',".$i.")",0);
								?>
								<input type="hidden" name="updateid_<? echo $item_group_id.'_'.$i; ?>" id="updateid_<? echo $item_group_id.'_'.$i; ?>" value="">
							</td>
							<td></td>
						</tr>
					<?	
					$i++;
					}
					?>
					</tbody>
				</table>
			</div>
		<?
		}
	}
	exit();
}
?>