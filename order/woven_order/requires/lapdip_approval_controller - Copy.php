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

if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');
	if($operation==0)  // Insert Here
	{
		$con = connect();
		
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$id=return_next_id( "id", "wo_po_lapdip_approval_info", 1 ) ;
		
		$field_array="id, job_no_mst, po_break_down_id, color_name_id, lapdip_target_approval_date, send_to_factory_date, recv_from_factory_date, submitted_to_buyer, approval_status, approval_status_date, lapdip_no, lapdip_comments, is_master_color, is_deleted, status_active, inserted_by, insert_date, garments_nature"; 
		
		if($color_id=='ec')
		{
			// Issue Id 24209 for Charka
			
			//$color_new_id=return_id( $color_id_ec_1, $color_arr, "lib_color", "id,color_name");// color_id_ec_1=Always will be checked with the first color name for Extra color
			/*if(str_replace("'","",$color_id_ec_1)!="")
			{ 
				if (!in_array(str_replace("'","",$color_id_ec_1),$new_array_color))
				{
					$color_new_id = return_id( str_replace("'","",$color_id_ec_1), $color_arr, "lib_color", "id,color_name","79");  
					//echo $$txtColorName.'='.$color_id.'<br>';
					$new_array_color[$color_new_id]=str_replace("'","",$color_id_ec_1);
				}
				else $color_new_id =  array_search(str_replace("'","",$color_id_ec_1), $new_array_color); 
			}
			else $color_new_id=0;*/
		}
		
		for($i=1; $i<=$tot_row; $i++)
		{
			$po_id="po_id_".$color_id."_".$i;
			$color_name_id="color_id_".$color_id."_".$i;
			$target_app_date="target_app_date_".$color_id."_".$i;
			$send_to_factory_date="send_to_factory_date_".$color_id."_".$i;
			$recv_from_factory_date="recv_from_factory_date_".$color_id."_".$i;
			$sent_to_buyer="submitted_to_buyer_".$color_id."_".$i;
			$action="action_".$color_id."_".$i;
			$action_date="action_date_".$color_id."_".$i;
			$txt_lapdip_no="txt_lapdip_no_".$color_id."_".$i;
			$txt_comments="txt_comments_".$color_id."_".$i;
			$cbo_status="cbo_status_".$color_id."_".$i;
			
			//$color_id_ec_chk="color_id_ec_".$color_id."_".$i;
			$color_id_ec_chk="color_id_ec_".$i;
			
			if($color_id=='ec') 
			{
				
				 	// Issue Id 24209 for Charka
					if(str_replace("'","",$$color_id_ec_chk)!="")
					{
						if (!in_array(str_replace("'","",$$color_id_ec_chk),$new_array_color))
						{
							$color_new_id = return_id( str_replace("'","",$$color_id_ec_chk), $color_arr, "lib_color", "id,color_name","79");
							$new_array_color[$color_new_id]=str_replace("'","",$$color_id_ec_chk);
						}
						else $color_new_id =  array_search(str_replace("'","",$$color_id_ec_chk), $new_array_color);
					}
					else $color_new_id =0;
				
				$color_lib_id=$color_new_id;
				$is_master_color=0;
			}
			else
			{
				$color_lib_id=$$color_name_id;
				$is_master_color=1;
			}

			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",'".$txt_job_no."',".$$po_id.",".$color_lib_id.",".$$target_app_date.",".$$send_to_factory_date.",".$$recv_from_factory_date.",".$$sent_to_buyer.",".$$action.",".$$action_date.",".$$txt_lapdip_no.",".$$txt_comments.",".$is_master_color.",0,".$$cbo_status.",".$user_id.",'".$pc_date_time."',$garments_nature)";
			
			$id=$id+1;
		}
		//echo "10**insert into wo_po_lapdip_approval_info (".$field_array.") values ".$data_array;die;
		$rID=sql_insert("wo_po_lapdip_approval_info",$field_array,$data_array,1);
		 
		if($db_type==0)
		{
			if($rID)
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
				else{
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
		
		$id=return_next_id( "id", "wo_po_lapdip_approval_info", 1 ) ;
		$data_array="";
		
		$field_array="id, job_no_mst, po_break_down_id, color_name_id, lapdip_target_approval_date, send_to_factory_date, recv_from_factory_date, submitted_to_buyer, approval_status, approval_status_date, lapdip_no, lapdip_comments, is_master_color, is_deleted, status_active, inserted_by, insert_date, garments_nature";
		$field_array_update="job_no_mst*po_break_down_id* 	color_name_id*lapdip_target_approval_date*send_to_factory_date*recv_from_factory_date*submitted_to_buyer*approval_status*approval_status_date*lapdip_no*lapdip_comments*is_deleted*status_active*updated_by*update_date*garments_nature";
		
		for($i=1; $i<=$tot_row; $i++)
		{
			$po_id="po_id_".$color_id."_".$i;
			$color_name_id="color_id_".$color_id."_".$i;
			$target_app_date="target_app_date_".$color_id."_".$i;
			$send_to_factory_date="send_to_factory_date_".$color_id."_".$i;
			$recv_from_factory_date="recv_from_factory_date_".$color_id."_".$i;
			$sent_to_buyer="submitted_to_buyer_".$color_id."_".$i;
			$action="action_".$color_id."_".$i;
			$action_date="action_date_".$color_id."_".$i;
			$txt_lapdip_no="txt_lapdip_no_".$color_id."_".$i;
			$txt_comments="txt_comments_".$color_id."_".$i;
			$cbo_status="cbo_status_".$color_id."_".$i;
			$updateid="updateid_".$color_id."_".$i;

			 if(str_replace("'",'',$$updateid)!="")
			 {
				$id_arr[]=str_replace("'",'',$$updateid);
				$data_array_update[str_replace("'",'',$$updateid)] = explode(",",("'".$txt_job_no."',".$$po_id.",".$$color_name_id.",".$$target_app_date.",".$$send_to_factory_date.",".$$recv_from_factory_date.",".$$sent_to_buyer.",".$$action.",".$$action_date.",".$$txt_lapdip_no.",".$$txt_comments.",0,".$$cbo_status.",".$user_id.",'".$pc_date_time."',$garments_nature"));
			 }
			 else
			 {
				if($data_array!="") $data_array.=",";
			$data_array .="(".$id.",'".$txt_job_no."',".$$po_id.",".$$color_name_id.",".$$target_app_date.",".$$send_to_factory_date.",".$$recv_from_factory_date.",".$$sent_to_buyer.",".$$action.",".$$action_date.",".$$txt_lapdip_no.",".$$txt_comments.",1,0,".$$cbo_status.",".$user_id.",'".$pc_date_time."',$garments_nature)";
			
				$id=$id+1;
			 }
		}
		
		$flag=1;
		if($data_array_update!="")
		{
			//echo "10**".bulk_update_sql_statement( "wo_po_lapdip_approval_info", "id", $field_array_update, $data_array_update, $id_arr ); die;
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
          
		if($current_status!="")
		{
			$field_array_status="updated_by*update_date*current_status";
			$data_array_status=$user_id."*'".$pc_date_time."'*0";
	
			$rID3=sql_multirow_update("wo_po_lapdip_approval_info",$field_array_status,$data_array_status,"id",$current_status,1);
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
 
		$rID=sql_delete("wo_po_lapdip_approval_info",$field_array,$data_array,"job_no_mst*color_name_id","'".$txt_job_no."'"."*".$color_id,1);
		
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
 
if($action=="order_popup")
{
  	echo load_html_head_contents("Lapdip Approval Info","../../../", 1, 1, '','','');
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
        <table width="960" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <tr align="center">
                    <th colspan="9"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr> 
                <tr>                     	 
                    <th width="160" class="must_entry_caption">Company Name</th>
                    <th width="172" class="must_entry_caption">Buyer Name</th>
                    <th width="80">Job No</th>
                    <th width="100">Style Ref </th>
                    <th width="100">Order No</th>
                    <th width="80">Internal Ref.</th>
                    <th width="130" colspan="2">Date Range</th>
                    <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">Job Without PO</th>  
                </tr>         
            </thead>
            <tbody>
				<tr class="general">
					<td><input type="hidden" id="selected_job">
                        <? echo create_drop_down( "cbo_company_name", 160, "select comp.id,comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'lapdip_approval_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?>
                    </td>
                    <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 172, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --",$selected ); ?></td>
                    <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes_numeric" style="width:70px"></td>
                    <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:90px"></td>
                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:90px"></td>
                    <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"></td>
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date"></td> 
                    <td align="center">
                         <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $garments_nature; ?>+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_internal_ref').value, 'create_po_search_list_view', 'search_div', 'lapdip_approval_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
                </tr>
                <tr align="center">
                    <td colspan="9"><? echo load_month_buttons(1); ?></td>
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
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else $company="";
	
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $$buyer="";//{ echo "Please Select Buyer First."; die; }
	if($db_type==0) { $year_cond=" and SUBSTRING_INDEX(a.`insert_date`, '-', 1)=$data[7]";  $insert_year="SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year";  }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[7]";   $insert_year="to_char(a.insert_date,'YYYY') as year";}
	//if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num='$data[6]'  $year_cond "; else  $job_cond=""; 
	//if (str_replace("'","",$data[8])!="") $order_cond=" and b.po_number like '%$data[8]%' $year_cond "; else  $order_cond=""; 
	$job_cond=""; $order_cond=""; $style_cond=""; $internalRefCond="";
	if($data[10]==1)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num='$data[6]' $year_cond"; //else  $job_cond=""; 
		if (str_replace("'","",$data[8])!="") $order_cond=" and b.po_number = '$data[8]'  "; //else  $order_cond=""; 
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no ='$data[9]'"; //else  $style_cond=""; 
		if (trim($data[11])!="") $internalRefCond=" and b.grouping ='$data[11]'"; //else  $style_cond=""; 
	}
	else if($data[10]==2)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '$data[6]%' $year_cond"; //else  $job_cond=""; 
		if (str_replace("'","",$data[8])!="") $order_cond=" and b.po_number like '$data[8]%'  "; //else  $order_cond=""; 
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '$data[9]%'  "; //else  $style_cond=""; 
		if (trim($data[11])!="") $internalRefCond=" and b.grouping like '$data[11]%'  "; //else  $style_cond=""; 
	}
	else if($data[10]==3)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]' $year_cond"; //else  $job_cond=""; 
		if (str_replace("'","",$data[8])!="") $order_cond=" and b.po_number like '%$data[8]'  "; //else  $order_cond=""; 
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '%$data[9]'"; //else  $style_cond="";
		if (trim($data[11])!="") $internalRefCond=" and b.grouping like '%$data[11]'"; //else  $style_cond=""; 
	}
	else if($data[10]==4 || $data[10]==0)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]%' $year_cond"; //else  $job_cond=""; 
		if (str_replace("'","",$data[8])!="") $order_cond=" and b.po_number like '%$data[8]%'  "; //else  $order_cond=""; 
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '%$data[9]%'"; //else  $style_cond="";
		if (trim($data[11])!="") $internalRefCond=" and b.grouping like '%$data[11]%'"; //else  $style_cond=""; 
	}
	if($db_type==0)
	{
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	else if($db_type==2 || $db_type==1)
	{
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".date("j-M-Y",strtotime($data[3]))."' and '".date("j-M-Y",strtotime($data[4]))."'"; else $shipment_date ="";
	}
	 
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$arr=array (2=>$comp,3=>$buyer_arr);
	
	if($data[2]==0)
	{
		 $sql= "select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.po_number, b.grouping, b.po_quantity, b.pub_shipment_date as shipment_date, $insert_year from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and a.garments_nature=$data[5] $shipment_date $company $buyer $job_cond $style_cond $order_cond $internalRefCond order by a.id DESC"; 
	//echo $sql;
		 
		echo create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,Internal Ref,PO Quantity,Shipment Date", "60,60,120,110,100,80,80,100,80","950","240",0, $sql , "js_set_value", "job_no", "", 1, "0,0,company_name,buyer_name,0,0,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,po_number,grouping,po_quantity,shipment_date", "",'','0,0,0,0,0,1,0,0,1,3');
	}
	else
	{
		$sql= "select a.job_no_prefix_num, $insert_year, a.job_no, a.company_name, a.buyer_name, a.style_ref_no from wo_po_details_master a where a.status_active=1 and a.garments_nature=$data[5] and a.is_deleted=0 $company $buyer $job_cond order by a.id DESC";
		
		echo create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No,", "90,90,150,150,100","880","240",0, $sql , "js_set_value", "job_no", "", 1, "0,0,company_name,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no", "",'','0,0,0,0,0,1,0,2,3') ;
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
		echo "load_drop_down('requires/lapdip_approval_controller','".$row[csf("job_no")]."', 'load_drop_down_color_name', 'load_color');\n";
	}
}
if($action=="load_drop_down_color_name")
{
	echo create_drop_down( "cbo_color_name", 172, "select a.id, a.color_name from lib_color a, wo_po_color_size_breakdown b where a.id=b.color_number_id and b.job_no_mst='$data' and  b.is_deleted=0 and b.status_active=1 and a.id not in(select color_name_id from wo_po_lapdip_approval_info where job_no_mst='$data' and is_deleted=0 and status_active=1) group by a.id,a.color_name order by a.color_name","id,color_name", 1, "-- Select Color --", '', "show_list_view(document.getElementById('txt_job_no').value+'**'+1+'**'+this.value, 'lapdip_approval_list_view_edit','lapdip_approval_list_view','requires/lapdip_approval_controller','$(\'#hide_color_id\').val(\'\')');",0,'',"Extra Color","ec");  
	exit();
}

if($action=="lapdip_approval_list_view_edit")
{
	$data=explode("**",$data);
	$job_no=$data[0]; $type=$data[1]; $color_array=array(); $po_id=''; $color_arr=array();
	$po_number_arr=return_library_array( "select id, po_number from wo_po_break_down where job_no_mst='$job_no'",'id','po_number');

	$poIdsArr=array(); $allPoIdArr=array();
	$colorData=sql_select("select a.po_break_down_id, b.id, b.color_name from wo_po_color_size_breakdown a, lib_color b where a.color_number_id=b.id and a.job_no_mst='$job_no'");
	foreach($colorData as $row)
	{
		$color_arr[$row[csf('id')]]=$row[csf('color_name')];
		$poIdsArr[$row[csf('id')]].=$row[csf('po_break_down_id')].",";
		$allPoIdArr[$row[csf('po_break_down_id')]]=$row[csf('po_break_down_id')];
	}
	
	$colorDataEc=sql_select("select b.id, b.color_name from wo_po_lapdip_approval_info a, lib_color b where a.color_name_id=b.id and a.job_no_mst='$job_no' and a.color_name_id not in(".implode(",",array_keys($color_arr)).") group by b.id, b.color_name");
	foreach($colorDataEc as $row)
	{
		$color_arr[$row[csf('id')]]=$row[csf('color_name')];
	}
	
	$sql="SELECT id, po_break_down_id, color_name_id, lapdip_target_approval_date, send_to_factory_date, recv_from_factory_date, submitted_to_buyer, approval_status, approval_status_date,lapdip_no,lapdip_comments,status_active from wo_po_lapdip_approval_info where job_no_mst='$job_no' and is_deleted=0 and status_active=1 order by color_name_id,po_break_down_id,id";
	$dataArray=sql_select($sql);
	$partial_approved=0; $full_approved=0; $unapproved=0;
	foreach ($dataArray as $row) {
		$color_approval_status_arr[$row[csf('color_name_id')]][$row[csf('po_break_down_id')]]=$row[csf('approval_status')];
	}
	$partial_approved = 0;
	$color_approved_status=array();
	foreach ($color_approval_status_arr as $color_id => $color_app_arr) {
	    $status = 'Fully Approved';
	    $partial_approved = 0;
	    if(count($color_app_arr)>1)
	    {
			foreach ($color_app_arr as $key1 => $value1) {
				$unapproved = 0;
				foreach ($color_app_arr as $key2 => $value2) {
					if($key1 != $key2 && $value1 == 3 && $value2 != 3){
						$partial_approved = 1 ; 
						break;			
					}
					if($key1 != $key2 && $value1 != 3 && $value2 != 3){
						$unapproved++;				
					}
				};
				if($partial_approved > 0){
					$status = 'Partial Approved';
					break;
				}
				elseif($unapproved == count($color_app_arr) - 1){
					$status = 'Full Pending';
					break;
				}
			}
	    }
	    else{
	    	foreach ($color_app_arr as $key => $value) {
	    		if($value == 3){
	    			$status = 'Fully Approved';
	    		}
	    		else{
	    			$status = 'Full Pending';
	    		}
	    	}
	    }
		$color_approved_status[$color_id] = $status;
	}
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
		
		$color_id=$row[csf("color_name_id")];
		if(in_array($color_id,$color_array))
		{
			$print_cond_header=0;
			$print_cond_footer=0;
        }
		else
		{
			$print_cond_header=1;
			if($z==1) 
			{
				$print_cond_footer=0;
			}
			else 
			{
				$print_cond_footer=1;
			}
			$color_array[]=$color_id;
		}
		
		if($print_cond_footer==1)
		{
					$po_id_arr=array_unique(explode(",",substr($po_id,0,-1)));
					$colorPoIds=array_unique(explode(",",substr($poIdsArr[$prev_color_id],0,-1)));
					$result=implode(",",array_diff($colorPoIds,$po_id_arr));
					//print_r($result);
					foreach($result as $poId)
					{
					?>
						<tr align="center">
							<td>
								<?
									echo create_drop_down("po_no_".$prev_color_id."_".$i, 100, $po_number_arr,"", 1,'', $poId,"",1);
								?>
								<input type="hidden" name="po_id_<? echo $prev_color_id.'_'.$i; ?>" id="po_id_<? echo $prev_color_id.'_'.$i; ?>" value="<? echo $poId; ?>" style="width:100px;" class="text_boxes" disabled="disabled">
							</td>
							<td>
								<?
									echo create_drop_down("color_".$prev_color_id."_".$i, 90, $color_arr,"", 1,'', $prev_color_id,"",1);
								?>
								<input type="hidden" name="color_id_<? echo $prev_color_id.'_'.$i; ?>" id="color_id_<? echo $prev_color_id.'_'.$i; ?>" value="<? echo $prev_color_id; ?>" style="width:90px;" class="text_boxes" disabled="disabled">
							</td>
							<td>
								<input type="text" name="target_app_date_<? echo $prev_color_id.'_'.$i; ?>" id="target_app_date_<? echo $prev_color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'target_app_date_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="send_to_factory_date_<? echo $prev_color_id.'_'.$i; ?>" id="send_to_factory_date_<? echo $prev_color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'send_to_factory_date_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="recv_from_factory_date_<? echo $prev_color_id.'_'.$i; ?>" id="recv_from_factory_date_<? echo $prev_color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'recv_from_factory_date_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="submitted_to_buyer_<? echo $prev_color_id.'_'.$i; ?>" id="submitted_to_buyer_<? echo $prev_color_id.'_'.$i; ?>" style="width:80px;" class="datepicker" onChange="copy_value(this.value,'submitted_to_buyer_',<? echo $i; ?>)">
							</td>
							<td>
								<?
									echo create_drop_down("action_".$prev_color_id."_".$i, 90, $approval_status,"", 1, "--   --","","copy_value(this.value,'action_',".$i.")");
								?>
							</td>
							<td>
								<input type="text" name="action_date_<? echo $prev_color_id.'_'.$i; ?>" id="action_date_<? echo $prev_color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'action_date_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="txt_lapdip_no_<? echo $prev_color_id.'_'.$i; ?>" id="txt_lapdip_no_<? echo $prev_color_id.'_'.$i; ?>" style="width:100px;" class="text_boxes" onChange="copy_value(this.value,'txt_lapdip_no_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="txt_comments_<? echo $prev_color_id.'_'.$i; ?>" id="txt_comments_<? echo $prev_color_id.'_'.$i; ?>" style="width:100px;" class="text_boxes" onChange="copy_value(this.value,'txt_comments_',<? echo $i; ?>)" placeholder="Single Click" onClick="fnc_comments(this.id,this.value)" readonly>
							</td>
							<td>
								<?
									echo create_drop_down("cbo_status_".$prev_color_id."_".$i, 80, $row_status,"", 0,"","","copy_value(this.value,'cbo_status_',".$i.")",0);
								?>
								<input type="hidden" name="updateid_<? echo $prev_color_id.'_'.$i; ?>" id="updateid_<? echo $prev_color_id.'_'.$i; ?>" value="">
							</td>
							<td></td>
						</tr>
					<?	
					$i++;
					}
					$po_id='';
					$i=1;
					?>
        			</tbody>
                </table>
            </div>
		<?
		}
		
		if($print_cond_header==1)
		{
		?>
            <h3 align="left" id="accordion_h<? echo $color_id; ?>" style="width:1075px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel_<? echo $color_id; ?>', 'fnc_color_id(<? echo $color_id; ?>,1,0)',1)"> +<? echo $color_arr[$color_id]; ?> <span style="color: red">[<? echo $color_approved_status[$color_id] ?>]</span></h3>
            <div id="content_search_panel_<? echo $color_id; ?>" style="display:none" class="accord_close">
                <table class="rpt_table" border="1" width="1075" cellpadding="0" cellspacing="0" rules="all" id="table_<? echo $color_id; ?>">
                    <thead>
                        <th>Po Number</th>
                        <th class="must_entry_caption">Color Name</th>
                        <th>Target Approval Date</th>
                        <th>Sent To Lab Section</th>
                        <th>Recv. From Lab Section</th>
                        <th>Submitted To Buyer</th>
                        <th>Action</th>
                        <th class="must_entry_caption">Action Date</th>
                        <th>Labdip No</th>
                        <th>Comments</th>
                        <th>Status</th>
                        <th><input type="hidden" name="current_status_<? echo $color_id; ?>" id="current_status_<? echo $color_id; ?>" value="" style="width:75px;" class="text_boxes" readonly></th>
                    </thead>
                    <tbody>
		<?
		}
		
		$po_id.=$row[csf("po_break_down_id")].",";
        ?>
                <tr align="center" title="<?=$po_number_arr[$row[csf("po_break_down_id")]];?>">
                    <td>
                        <?
                            echo create_drop_down("po_no_".$color_id."_".$i, 100, $po_number_arr,"", 1,'', $row[csf("po_break_down_id")],"",1);
                        ?>
                        <input type="hidden" name="po_id_<? echo $color_id.'_'.$i; ?>" id="po_id_<? echo $color_id.'_'.$i; ?>" value="<? echo $row[csf("po_break_down_id")]; ?>" style="width:100px;" class="text_boxes" disabled="disabled">
                    </td>
                    <td>
                        <?
                            echo create_drop_down("color_".$color_id."_".$i, 90, $color_arr,"", 1,'', $color_id,"",1);
                        ?>
                        <input type="hidden" name="color_id_<? echo $color_id.'_'.$i; ?>" id="color_id_<? echo $color_id.'_'.$i; ?>" value="<? echo $color_id; ?>" style="width:90px;" class="text_boxes" disabled="disabled">
                    </td>
                    <td>
                        <input type="text" name="target_app_date_<? echo $color_id.'_'.$i; ?>" id="target_app_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'target_app_date_',<? echo $i;?>)" value="<? if($row[csf("lapdip_target_approval_date")]!="0000-00-00") echo change_date_format($row[csf("lapdip_target_approval_date")]);?>" <? echo $disable; ?>>
                    </td>
                    <td>
                        <input type="text" name="send_to_factory_date_<? echo $color_id.'_'.$i; ?>" id="send_to_factory_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'send_to_factory_date_',<? echo $i; ?>)" value="<? if($row[csf("send_to_factory_date")]!="0000-00-00") echo change_date_format($row[csf("send_to_factory_date")]); ?>" <? echo $disable; ?> >
                    </td>
                    <td>
                        <input type="text" name="recv_from_factory_date_<? echo $color_id.'_'.$i; ?>" id="recv_from_factory_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'recv_from_factory_date_',<? echo $i; ?>)" value="<? if($row[csf("recv_from_factory_date")]!="0000-00-00") echo change_date_format($row[csf("recv_from_factory_date")]); ?>" <? echo $disable; ?> >
                    </td>                        
                    <td>
                        <input type="text" name="submitted_to_buyer_<? echo $color_id.'_'.$i; ?>" id="submitted_to_buyer_<? echo $color_id.'_'.$i; ?>" style="width:80px;" class="datepicker" onChange="copy_value(this.value,'submitted_to_buyer_',<? echo $i; ?>)" value="<? if($row[csf("submitted_to_buyer")]!="0000-00-00") echo change_date_format($row[csf("submitted_to_buyer")]); ?>" <? echo $disable; ?>>
                    </td>
                    <td>
                        <?
                            echo create_drop_down("action_".$color_id."_".$i, 90, $approval_status,"", 1, "--   --",$row[csf("approval_status")],"copy_value(this.value,'action_',".$i.")",$disable_status);
                        ?>
                    </td>
                    <td>
                        <input type="text" name="action_date_<? echo $color_id.'_'.$i; ?>" id="action_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'action_date_',<? echo $i; ?>)" value="<? if($row[csf("approval_status_date")]!="" && $row[csf("approval_status_date")]!="0000-00-00") echo change_date_format($row[csf("approval_status_date")]); ?>"  <? echo $disable; ?>>
                    </td>
                     <td>
                        <input type="text" name="txt_lapdip_no_<? echo $color_id.'_'.$i; ?>" id="txt_lapdip_no_<? echo $color_id.'_'.$i; ?>" style="width:100px;" class="text_boxes" onChange="copy_value(this.value,'txt_lapdip_no_',<? echo $i; ?>)" value="<? echo $row[csf("lapdip_no")]; ?>"  <? echo $disable; ?>>
                    </td>
                    <td>
                        <input type="text" name="txt_comments_<? echo $color_id.'_'.$i; ?>" id="txt_comments_<? echo $color_id.'_'.$i; ?>" style="width:100px;" class="text_boxes" onChange="copy_value(this.value,'txt_comments_',<? echo $i; ?>)" value="<? echo $row[csf("lapdip_comments")]; ?>" <? echo $disable; ?> placeholder="Single Click" onClick="fnc_comments(this.id, this.value)" readonly >
                    </td>
                    <td>
                        <?
                            echo create_drop_down("cbo_status_".$color_id."_".$i, 80, $row_status,"", 0,"",$row[csf("status_active")],"copy_value(this.value,'cbo_status_',".$i.")",$disable_status);
                        ?>
                        <input type="hidden" name="updateid_<? echo $color_id.'_'.$i; ?>" id="updateid_<? echo $color_id.'_'.$i; ?>" value="<? echo $row[csf("id")]; ?>">
                    </td>
                    <td>
                        <?
                        if($row[csf("approval_status")]==2)
                        {
                        ?>
                            <input type="button" id="addrow_<? echo $i; ?>"  name="addrow_<? echo $i; ?>" style="width:75px" class="formbutton" value="Re-Submit" onClick="resubmit(<? echo $color_id; ?>,<? echo $i; ?>)" />
                        <?
                        }
                        ?>
                    </td>
                </tr>
                
		<?
		$i++;
		$z++;
		$prev_color_id=$color_id;
	}
	if($z>1)
	{
					$po_id_arr=array_unique(explode(",",substr($po_id,0,-1)));
					$colorPoIds=array_unique(explode(",",substr($poIdsArr[$color_id],0,-1)));
					$result=implode(",",array_diff($colorPoIds,$po_id_arr));
					//print_r($result);
					foreach($result as $poId)
					{
					?>
						<tr align="center">
							<td>
								<?
									echo create_drop_down("po_no_".$color_id."_".$i, 100, $po_number_arr,"", 1,'', $poId,"",1);
								?>
								<input type="hidden" name="po_id_<? echo $color_id.'_'.$i; ?>" id="po_id_<? echo $color_id.'_'.$i; ?>" value="<? echo $poId; ?>" style="width:100px;" class="text_boxes" disabled="disabled">
							</td>
							<td>
								<?
									echo create_drop_down("color_".$color_id."_".$i, 90, $color_arr,"", 1,'', $color_id,"",1);
								?>
								<input type="hidden" name="color_id_<? echo $color_id.'_'.$i; ?>" id="color_id_<? echo $color_id.'_'.$i; ?>" value="<? echo $color_id; ?>" style="width:90px;" class="text_boxes" disabled="disabled">
							</td>
							<td>
								<input type="text" name="target_app_date_<? echo $color_id.'_'.$i; ?>" id="target_app_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'target_app_date_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="send_to_factory_date_<? echo $color_id.'_'.$i; ?>" id="send_to_factory_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'send_to_factory_date_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="recv_from_factory_date_<? echo $color_id.'_'.$i; ?>" id="recv_from_factory_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'recv_from_factory_date_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="submitted_to_buyer_<? echo $color_id.'_'.$i; ?>" id="submitted_to_buyer_<? echo $color_id.'_'.$i; ?>" style="width:80px;" class="datepicker" onChange="copy_value(this.value,'submitted_to_buyer_',<? echo $i; ?>)">
							</td>
							<td>
								<?
									echo create_drop_down("action_".$color_id."_".$i, 90, $approval_status,"", 1, "--   --","","copy_value(this.value,'action_',".$i.")");
								?>
							</td>
							<td>
								<input type="text" name="action_date_<? echo $color_id.'_'.$i; ?>" id="action_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'action_date_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="txt_lapdip_no_<? echo $color_id.'_'.$i; ?>" id="txt_lapdip_no_<? echo $color_id.'_'.$i; ?>" style="width:100px;" class="text_boxes" onChange="copy_value(this.value,'txt_lapdip_no_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="txt_comments_<? echo $color_id.'_'.$i; ?>" id="txt_comments_<? echo $color_id.'_'.$i; ?>" style="width:100px;" class="text_boxes" onChange="copy_value(this.value,'txt_comments_',<? echo $i; ?>)" placeholder="Single Click" onClick="fnc_comments(this.id,this.value)" readonly>
							</td>
							<td>
								<?
									echo create_drop_down("cbo_status_".$color_id."_".$i, 80, $row_status,"", 0,"","","copy_value(this.value,'cbo_status_',".$i.")",0);
								?>
								<input type="hidden" name="updateid_<? echo $color_id.'_'.$i; ?>" id="updateid_<? echo $color_id.'_'.$i; ?>" value="">
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

	if($type==1)
	{
		$color_id=$data[2];
		if($color_id=="ec")//ec=Extra Color
		{
		?>
			<h3 align="left" id="accordion_h<? echo $color_id; ?>" style="width:1075px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel_<? echo $color_id; ?>', 'fnc_color_id(\'<? echo $color_id; ?>\',0,1)',1)"> +<? echo "Extra Color"; ?> <span style="color: red">[<? echo $color_approved_status[$color_id] ?>]</span></h3>
			<div id="content_search_panel_<? echo $color_id; ?>" style="display:none" class="accord_close">
				<table class="rpt_table" border="1" width="1075" cellpadding="0" cellspacing="0" rules="all" id="table_<? echo $color_id; ?>">
					<thead>
						<th>Po Number</th>
						<th>Color Name</th>
						<th>Target Approval Date</th>
						<th>Sent To Lab Section</th>
                        <th>Recv. From Lab Section</th>
						<th>Submitted To Buyer</th>
						<th>Action</th>
						<th>Action Date</th>
						<th>Labdip No</th>
						<th>Comments</th>
						<th>Status</th>
						<th><input type="hidden" name="current_status_<? echo $color_id; ?>" id="current_status_<? echo $color_id; ?>" value="" style="width:75px;" class="text_boxes" readonly></th>
					</thead>
					<tbody>
					<?
                    $i=1;
					foreach($allPoIdArr as $poId)
					{
						if($i==1)
						{
							$disable="";
							$disable_status=0;
						}
						else 
						{
							$disable="disabled='disabled'";
							$disable_status=1;
						}
					?>
						<tr align="center" title="<?=$po_number_arr[$poId];?>">
							<td>
								<?
									echo create_drop_down("po_no_".$color_id."_".$i, 100, $po_number_arr,"", 1,'', $poId,"",1);
								?>
								<input type="hidden" name="po_id_<? echo $color_id.'_'.$i; ?>" id="po_id_<? echo $color_id.'_'.$i; ?>" value="<? echo $poId; ?>" style="width:100px;" class="text_boxes" disabled="disabled">
							</td>
							<td>
								<input type="text" name="color_id_<? echo $color_id.'_'.$i; ?>" id="color_id_<? echo $color_id.'_'.$i; ?>" value="" style="width:90px;" class="text_boxes" onBlur="check_color_name(this.value,'color_id_',<? echo $i; ?>)" <? //echo $disable; ?>>
                                <input type="hidden" name="color_<? echo $color_id.'_'.$i; ?>" id="color_id_<? echo $color_id.'_'.$i; ?>" value="" style="width:90px;" class="text_boxes" disabled="disabled">
							</td>
							<td>
								<input type="text" name="target_app_date_<? echo $color_id.'_'.$i; ?>" id="target_app_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'target_app_date_',<? echo $i; ?>)" <? echo $disable; ?>>
							</td>
							<td>
								<input type="text" name="send_to_factory_date_<? echo $color_id.'_'.$i; ?>" id="send_to_factory_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'send_to_factory_date_',<? echo $i; ?>)" <? echo $disable; ?>>
							</td>
							<td>
								<input type="text" name="recv_from_factory_date_<? echo $color_id.'_'.$i; ?>" id="recv_from_factory_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'recv_from_factory_date_',<? echo $i; ?>)" <? echo $disable; ?>>
							</td>
							<td>
								<input type="text" name="submitted_to_buyer_<? echo $color_id.'_'.$i; ?>" id="submitted_to_buyer_<? echo $color_id.'_'.$i; ?>" style="width:80px;" class="datepicker" onChange="copy_value(this.value,'submitted_to_buyer_',<? echo $i; ?>)" <? echo $disable; ?>>
							</td>
							<td>
								<?
									echo create_drop_down("action_".$color_id."_".$i, 90, $approval_status,"", 1, "--   --","","copy_value(this.value,'action_',".$i.")",$disable_status);
								?>
							</td>
							<td>
								<input type="text" name="action_date_<? echo $color_id.'_'.$i; ?>" id="action_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'action_date_',<? echo $i; ?>)" <? echo $disable; ?>>
							</td>
							<td>
								<input type="text" name="txt_lapdip_no_<? echo $color_id.'_'.$i; ?>" id="txt_lapdip_no_<? echo $color_id.'_'.$i; ?>" style="width:100px;" class="text_boxes" onChange="copy_value(this.value,'txt_lapdip_no_',<? echo $i; ?>)" <? echo $disable; ?>>
							</td>
							<td>
								<input type="text" name="txt_comments_<? echo $color_id.'_'.$i; ?>" id="txt_comments_<? echo $color_id.'_'.$i; ?>" style="width:100px;" class="text_boxes" onChange="copy_value(this.value,'txt_comments_',<? echo $i; ?>)" <? echo $disable; ?> placeholder="Single Click" onClick="fnc_comments(this.id,this.value)" readonly>
							</td>
							<td>
								<?
									echo create_drop_down("cbo_status_".$color_id."_".$i, 80, $row_status,"", 0,"","","copy_value(this.value,'cbo_status_',".$i.")",$disable_status);
								?>
								<input type="hidden" name="updateid_<? echo $color_id.'_'.$i; ?>" id="updateid_<? echo $color_id.'_'.$i; ?>" value="">
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
		else if($color_id!=0)
		{
		?>
			<h3 align="left" id="accordion_h<? echo $color_id; ?>" style="width:1075px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel_<? echo $color_id; ?>', 'fnc_color_id(<? echo $color_id; ?>,0,0)',1)"> +<? echo $color_arr[$color_id]; ?> <span style="color: red">[<? echo $color_approved_status[$color_id] ?>]</span></h3>
			<div id="content_search_panel_<? echo $color_id; ?>" style="display:none" class="accord_close">
				<table class="rpt_table" border="1" width="1075" cellpadding="0" cellspacing="0" rules="all" id="table_<? echo $color_id; ?>">
					<thead>
						<th>Po Number</th>
						<th>Color Name</th>
						<th>Target Approval Date</th>
						<th>Sent To Lab Section</th>
                        <th>Recv. From Lab Section</th>
						<th>Submitted To Buyer</th>
						<th>Action</th>
						<th>Action Date</th>
						<th>Labdip No</th>
						<th>Comments</th>
						<th>Status</th>
						<th><input type="hidden" name="current_status_<? echo $color_id; ?>" id="current_status_<? echo $color_id; ?>" value="" style="width:75px;" class="text_boxes" readonly></th>
					</thead>
					<tbody>
					<?
					$i=1;
					/*$sql="select po_break_down_id from wo_po_color_size_breakdown where job_no_mst='$job_no' and color_number_id=$color_id and status_active=1 and is_deleted=0 group by po_break_down_id";
					$dataArray=sql_select($sql);
					foreach($dataArray as $row)*/
					$colorPoIds=array_unique(explode(",",substr($poIdsArr[$color_id],0,-1)));
					foreach($colorPoIds as $poId)
					{
					?>
						<tr align="center" title="<?=$po_number_arr[$poId];?>">
							<td>
								<?
									echo create_drop_down("po_no_".$color_id."_".$i, 100, $po_number_arr,"", 1,'', $poId,"",1);
								?>
								<input type="hidden" name="po_id_<? echo $color_id.'_'.$i; ?>" id="po_id_<? echo $color_id.'_'.$i; ?>" value="<? echo $poId; ?>" style="width:100px;" class="text_boxes" disabled="disabled">
							</td>
							<td>
								<?
									echo create_drop_down("color_".$color_id."_".$i, 90, $color_arr,"", 1,'', $color_id,"",1);
								?>
								<input type="hidden" name="color_id_<? echo $color_id.'_'.$i; ?>" id="color_id_<? echo $color_id.'_'.$i; ?>" value="<? echo $color_id; ?>" style="width:90px;" class="text_boxes" disabled="disabled">
							</td>
							<td>
								<input type="text" name="target_app_date_<? echo $color_id.'_'.$i; ?>" id="target_app_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'target_app_date_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="send_to_factory_date_<? echo $color_id.'_'.$i; ?>" id="send_to_factory_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'send_to_factory_date_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="recv_from_factory_date_<? echo $color_id.'_'.$i; ?>" id="recv_from_factory_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'recv_from_factory_date_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="submitted_to_buyer_<? echo $color_id.'_'.$i; ?>" id="submitted_to_buyer_<? echo $color_id.'_'.$i; ?>" style="width:80px;" class="datepicker" onChange="copy_value(this.value,'submitted_to_buyer_',<? echo $i; ?>)">
							</td>
							<td>
								<?
									echo create_drop_down("action_".$color_id."_".$i, 90, $approval_status,"", 1, "--   --","","copy_value(this.value,'action_',".$i.")");
								?>
							</td>
							<td>
								<input type="text" name="action_date_<? echo $color_id.'_'.$i; ?>" id="action_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'action_date_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="txt_lapdip_no_<? echo $color_id.'_'.$i; ?>" id="txt_lapdip_no_<? echo $color_id.'_'.$i; ?>" style="width:100px;" class="text_boxes" onChange="copy_value(this.value,'txt_lapdip_no_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="txt_comments_<? echo $color_id.'_'.$i; ?>" id="txt_comments_<? echo $color_id.'_'.$i; ?>" style="width:100px;" class="text_boxes" onChange="copy_value(this.value,'txt_comments_',<? echo $i; ?>)" placeholder="Single Click" onClick="fnc_comments(this.id,this.value)" readonly>
							</td>
							<td>
								<?
									echo create_drop_down("cbo_status_".$color_id."_".$i, 80, $row_status,"", 0,"","","copy_value(this.value,'cbo_status_',".$i.")",0);
								?>
								<input type="hidden" name="updateid_<? echo $color_id.'_'.$i; ?>" id="updateid_<? echo $color_id.'_'.$i; ?>" value="">
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

if($action=="check_color_name")
{
	$data=explode("**",$data);
	$response=is_duplicate_field( "b.color_number_id", "lib_color a, wo_po_color_size_breakdown b", "a.id=b.color_number_id and b.job_no_mst='".trim($data[1])."' and a.color_name='".trim($data[0])."' and b.is_deleted=0 and b.status_active=1");
	echo $response;
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