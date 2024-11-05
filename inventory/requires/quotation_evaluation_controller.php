<?php

header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 ","id,location_name", 1, "-- Select Location --", $selected, "" );
	die;
}

if ($action=="load_drop_down_supplier")
{ 
	echo create_drop_down( "cbo_supplier_id", 150, "select distinct(a.id), a.supplier_name from lib_supplier a,lib_supplier_party_type b,lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id  and c.tag_company=$data and a.status_active=1 and a.is_deleted=0 order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
	//echo create_drop_down( "cbo_supplier_id", 150, "select id,supplier_name from lib_supplier where FIND_IN_SET($data,tag_company) and status_active=1 order by supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
	die;
}

/* Supplier Dublicate Check here */
if ($action=="validate_supplier_load_php_dtls_form")
{
	$data=explode('_',$data);
$nameArray=sql_select( "select a.id,a.company_id,a.requ_no_id,a.item_category_id,b.supplier_id,b.requ_item_id from inv_quot_evalu_mst a,inv_quot_evalu_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.requ_no_id='$data[2]' and a.item_category_id='$data[1]' and b.supplier_id='$data[3]' and b.requ_item_id='$data[4]'" );
		  if($nameArray)
		  {
			  echo "1";
		  }
		  else{ echo "2";}
	exit;
}
/* Requisition */
if ($action=="quotation_evaluation_popup")
{
 	echo load_html_head_contents("Popup Info","../../",1, 1,'',1,'');
	$data=explode('_',$data);
?>
<script>
	  function js_set_value(id)
	  {
		  document.getElementById('selected_job').value=id;
		  parent.emailwindow.hide();
	  
	  }
</script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="quotationevaluation_2"  id="quotationevaluation_2" autocomplete="off">
	<table width="800" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                    <thead>                	 
                      <th width="150">Item Category</th>
                      <th width="200">Date Range</th>
                      <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>            
                    </thead>
        			<tr>
                   		<td>
                        <? 
						echo create_drop_down( "cbo_item_category_id", 150,$item_category,"", 1, "-- Select --", $data[1], "","","","","","1,2,3,4,12,13,14");   	 
						?>
                        </td>
                    	<td>
                        <input type="hidden" id="hidden_company" value="<? echo $data[0];?>">
                        <input type="hidden" id="hidden_location" value="<? echo $data[1];?>">
                        <input type="hidden" id="selected_job">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					  	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 	</td> 
                        <td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('hidden_company').value+'_'+document.getElementById('cbo_item_category_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('hidden_location').value, 'quotation_evaluation_list_view', 'search_div', 'quotation_evaluation_controller', 'setFilterGrid(\'list_view\',-1)')"  style="width:100px;" />
                       
                       </td>
        			</tr>
             	</table>
          	</td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle">
				<? echo load_month_buttons(1);  ?>
           
            </td>
        </tr>
        <tr>
            <td align="center" valign="top" id="search_div"> 
	
            </td>
        </tr>
    </table>    
    </form>
   </div>
    
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

/* Requisition No List view*/
if($action=="quotation_evaluation_list_view")
{	
		$data=explode('_',$data);
		
		if ($data[1]!=0) $item_category_val=" and item_category_id='$data[1]'"; else { echo "Please Select item category First."; die; }
		if($db_type==0)
		{	
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = " and requisition_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $order_rcv_date ="";
		}
		if($db_type==2)
		{	
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = " and requisition_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $order_rcv_date ="";
		}
		$sql= "select id, requ_prefix_num, requ_no,requisition_date,company_id,item_category_id,location_id,department_id,section_id,manual_req from inv_purchase_requisition_mst where status_active=1 and is_deleted=0  and company_id=$data[0] $item_category_val $order_rcv_date order by id asc ";
		$company=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$location=return_library_array("select id,location_name from lib_location",'id','location_name');	
		$department=return_library_array("select id,department_name from  lib_department",'id','department_name');
		$section=return_library_array("select id,section_name from lib_section",'id','section_name');	
		$arr=array (2=>$company,3=>$item_category,4=>$location,5=>$department,6=>$section);
		
		  echo  create_list_view("list_view", "Requisition No,Requisition Date,Company,Item Category,Location,Department,Section,status","100,120,100,100,90,80,100,100","900","320",0,$sql, "js_set_value","id,requ_no","",1,"0,0,company_id,item_category_id,location_id,department_id,section_id,0",$arr,"requ_prefix_num,requisition_date,company_id,item_category_id,location_id,department_id,section_id,manual_req", "",'','0,3,0,0,0,0,0,0') ;
		 
} 

//right side product list create here--------------------//
if($action=="requisition_container_dtls")
{
	$item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
	$arr=array(0=>$item_name_arr);
	if($db_type==0) $gro_field=" group by a.id ";
	if($db_type==2) $gro_field=" group by a.id,a.item_group_id,a.sub_group_name,a.item_description,a.item_size,c.item_name ";
	else $gro_field="";
		
	$sql="select a.id,a.item_group_id,a.sub_group_name,a.item_description,a.item_size,c.item_name from  product_details_master a,inv_purchase_requisition_dtls b,lib_item_group c where a.item_group_id=c.id and a.id=b.product_id and b.mst_id='$data' $gro_field " ;
	
	echo  create_list_view("list_view", "Item Group,Item Sub Group,Item Description,Item Size","100,90,90,60","420","320",0,$sql, "js_set_value_des","id,item_name,sub_group_name,item_description,item_size","",1,"item_group_id",$arr,"item_group_id,sub_group_name,item_description,item_size", "",'','') ;
	die;
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here=======================================================
	{
		  $con = connect();
		  if($db_type==0)
		  {
			  mysql_query("BEGIN");
		  }
		 $supplier_id=str_replace("'","",$cbo_supplier_id);
		 $cbo_company=str_replace("'","",$cbo_company_id);
		 $cbo_item_category=str_replace("'","",$cbo_item_category_id);
		 $requ_no=str_replace("'","",$req_id);
		  $requ_item=str_replace("'","",$hidden_requsition);
		  $duplicate=is_duplicate_field("b.id, b.supplier_id","inv_quot_evalu_dtls b,inv_quot_evalu_mst a","a.id=b.mst_id and a.company_id='$cbo_company' and a.requ_no_id='$requ_no' and a.item_category_id='$cbo_item_category' and b.requ_item_id='$requ_item' and b.supplier_id='$supplier_id'");
		  if($duplicate==1)
		  {
			  echo "11**This supplier is exist for same item of this requisition.";
			  disconnect($con);exit;
		  }
		  
		  if(str_replace("'","",$hidden_quotation)=="")
			{
				 if($db_type==2)
				{
				$new_requ_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'REQ', date("Y",time()), 5, "select requ_no_prefix,requ_prefix_num from  inv_quot_evalu_mst where company_id=$cbo_company_id and TO_CHAR(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "requ_no_prefix", "requ_prefix_num" ));
				}
				 if($db_type==0)
				{
				$new_requ_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'REQ', date("Y",time()), 5, "select requ_no_prefix,requ_prefix_num from  inv_quot_evalu_mst where company_id=$cbo_company_id and YEAR(insert_date)=".date('Y',time())." order by id desc ", "requ_no_prefix", "requ_prefix_num" ));
				}
				$id=return_next_id("id","inv_quot_evalu_mst",1);
				$field_array="id,requ_no_prefix,requ_prefix_num,system_id,company_id,location_name,dates,comments,requ_no_id,item_category_id,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id.",'".$new_requ_no[1]."',".$new_requ_no[2].",'".$new_requ_no[0]."',".$cbo_company_id.",".$cbo_location_name.",".$txt_requisition_date.",".$txt_comment.",".$req_id.",".$cbo_item_category_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				//$rID=sql_insert("inv_quot_evalu_mst",$field_array,$data_array,0);
				$txt_job_no=$new_requ_no[0];
				
			}		
			else
			{
				$id=str_replace("'",'',$hidden_quotation);
				$field_array="system_id*company_id*location_name*dates*comments*requ_no_id*item_category_id*updated_by*update_date";
				$data_array="".$txt_system_id."*".$cbo_company_id."*".$cbo_location_name."*".$txt_requisition_date."*".$txt_comment."*".$req_id."*".$cbo_item_category_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				//$rID=sql_update("inv_quot_evalu_mst",$field_array,$data_array,"id",$id,1);  
				 $txt_job_no=$txt_system_id;
				
			}
		  
		  $dtls_id=return_next_id("id","inv_quot_evalu_dtls",0);
		  $field_array1="id,mst_id,supplier_id,quotation_ref,requ_item_id,inserted_by,insert_date,status_active,is_deleted";
		  $data_array1="(".$dtls_id.",".$id.",".str_replace("'","",$cbo_supplier_id).",".$txt_quotaion_ref.",".$hidden_requsition.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		 
		  //$rID1=sql_insert("inv_quot_evalu_dtls",$field_array1,$data_array1,0);
		  
		  $evalu_factor_id=return_next_id("id","inv_quot_evalu_factor",0);
		  $field_array2="id,dtls_mst_id,evalu_factor_id,value,inserted_by,insert_date,status_active,is_deleted";

		  $add_comma=0;
			for($i=1; $i<=$tot_row; $i++)
			{
				$cbo="cbo_".$i;
				$txtevaluationfactor="txtevaluationfactor_".$i;
				$updateid_dtls="updateiddtls_".$i;
				
				  
				if(str_replace("'",'',$$updateid_dtls)=="")  
				{
					if ($add_comma!=0) $data_array2 .=",";
					$data_array2 .="(".$evalu_factor_id.",".$dtls_id.",".$$cbo.",".$$txtevaluationfactor.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$evalu_factor_id=$evalu_factor_id+1;
					$add_comma++;
				}
			}  
		//$rID2=sql_insert("inv_quot_evalu_factor",$field_array2,$data_array2,1);
		
		if(str_replace("'","",$hidden_quotation)=="")
		{
			$rID=sql_insert("inv_quot_evalu_mst",$field_array,$data_array,0);
		}
		else
		{
			$rID=sql_update("inv_quot_evalu_mst",$field_array,$data_array,"id",$id,1); 	
		}
		$rID1=sql_insert("inv_quot_evalu_dtls",$field_array1,$data_array1,0);
		$rID2=sql_insert("inv_quot_evalu_factor",$field_array2,$data_array2,1);
		//echo "5**".$rID ."**".$rID1 ."**". $rID2;die;
		  if($db_type==0)
		  {
			  if($rID && $rID1 && $rID2)
			  {
				  mysql_query("COMMIT"); 
				  echo "0**".str_replace("'", '', $id)."**".str_replace("'", '', $txt_job_no)."**".str_replace("'", '', $dtls_id); 
			  }
			  else
			  {
				  mysql_query("ROLLBACK"); 
				  echo "10**".str_replace("'", '', $id)."**".str_replace("'", '', $txt_job_no); 
			  }
		  }
		   if($db_type==2)
		  {
			  if($rID && $rID1 && $rID2)
			  {
				 oci_commit($con);
				 echo "0**".str_replace("'", '', $id)."**".str_replace("'", '', $txt_job_no)."**".str_replace("'", '', $dtls_id); 
			  }
			  else
			  {
				 oci_rollback($con);
				 echo "10**".str_replace("'", '', $id)."**".str_replace("'", '', $txt_job_no); 
			  }
		  }
		  disconnect($con);
		  die;
	}
	// Update Here==================================================================
	else if ($operation==1)  
	{
		  $con = connect();
		  if($db_type==0)
		  {
			  mysql_query("BEGIN");
		  } 
		  
			$id=str_replace("'",'',$hidden_quotation);
			$field_array="system_id*company_id*location_name*dates*comments*requ_no_id*item_category_id*updated_by*update_date";
			$data_array="".$txt_system_id."*".$cbo_company_id."*".$cbo_location_name."*".$txt_requisition_date."*".$txt_comment."*".$req_id."*".$cbo_item_category_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			//$rID=sql_update("inv_quot_evalu_mst",$field_array,$data_array,"id",$id,0);  

		  $field_array_update1="mst_id*supplier_id*quotation_ref*requ_item_id*updated_by*update_date";
		  $data_array_update1="".$id."*".$cbo_supplier_id."*".$txt_quotaion_ref."*".$hidden_requsition."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		  //$rID2=sql_update("inv_quot_evalu_dtls",$field_array_update1,$data_array_update1,"id",$update_id_dtls_id,1);
			$evalu_factor_id=return_next_id("id","inv_quot_evalu_factor",0);
			$field_array2="id,dtls_mst_id,evalu_factor_id,value,inserted_by,insert_date";

		   $field_array_up ="dtls_mst_id*evalu_factor_id*value*updated_by*update_date";
		   $add_comma=0;
		   for($i=1; $i<=$tot_row; $i++)
			{
				$cbo="cbo_".$i;
				$txtevaluationfactor="txtevaluationfactor_".$i;
				$updateid_dtls="updateiddtls_".$i;
				
				if(str_replace("'",'',$$updateid_dtls)=="")  
					{ 
						if ($add_comma!=0) $data_array2 .=",";
						$data_array2 .="(".$evalu_factor_id.",".$update_id_dtls_id.",".$$cbo.",".$$txtevaluationfactor.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
						$evalu_factor_id=$evalu_factor_id+1;
						$add_comma++;
					}
					
					else
					{
						$id_arr[]=str_replace("'",'',$$updateid_dtls);
						$data_array_up[str_replace("'",'',$$updateid_dtls)] =explode("*",("".$update_id_dtls_id."*".$$cbo."*".$$txtevaluationfactor."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
						
					}
			}
			
		$rID=1;$rID2=1;$rID3=1;$rID4=1;
		$rID=sql_update("inv_quot_evalu_mst",$field_array,$data_array,"id",$id,0);  
		$rID2=sql_update("inv_quot_evalu_dtls",$field_array_update1,$data_array_update1,"id",$update_id_dtls_id,1);
		$rID3=execute_query(bulk_update_sql_statement("inv_quot_evalu_factor", "id",$field_array_up,$data_array_up,$id_arr ),1);
		if($data_array2!="")
		{
			$rID4=sql_insert("inv_quot_evalu_factor",$field_array2,$data_array2,1);
		}
				
		if($db_type==0)
		  {
			  if($rID && $rID2 && $rID3 && $rID4)
			  {
				  mysql_query("COMMIT");  
				  echo "1**".str_replace("'", '', $id)."**".str_replace("'", '', $txt_system_id)."**".str_replace("'", '', $update_id_dtls_id);
			  }
			  else
			  {
				  mysql_query("ROLLBACK"); 
				  echo "10**".str_replace("'",'')."**".str_replace("'",'',$update_id);
			  }
		  }
		  if($db_type==2)
		  {
			  if($rID && $rID2 && $rID3 && $rID4)
			  {
				  oci_commit($con);
				  echo "1**".str_replace("'", '', $id)."**".str_replace("'", '', $txt_system_id)."**".str_replace("'", '', $update_id_dtls_id);
			  }
			  else
			  {
				  oci_rollback($con);
				  echo "10**".str_replace("'",'')."**".str_replace("'",'',$update_id);
			  }
		  }
		  disconnect($con);
		  die;	
	} 
	else if ($operation==2)  
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$mst_id=str_replace("'",'',$hidden_quotation);
		$update_id_dtls_id=str_replace("'","",$update_id_dtls_id);
		if($mst_id!="" && $update_id_dtls_id!="")
		{ 
			$field_array="updated_by*update_date*status_active*is_deleted";
			$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
			$rID=sql_update("inv_quot_evalu_dtls",$field_array,$data_array,"id",$update_id_dtls_id,0);
			$rID2=sql_update("inv_quot_evalu_factor",$field_array,$data_array,"dtls_mst_id",$update_id_dtls_id,1);
			if($db_type==0)
			{
				if($rID && $rID2)
				{
					mysql_query("COMMIT");  
					echo "2**".$mst_id."**".str_replace("'", '', $txt_system_id)."**".$update_id_dtls_id;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$mst_id."**".$update_id_dtls_id;
				}
			}
			if($db_type==2)
			{
				if($rID && $rID2)
				{
					oci_commit($con);
					echo "2**".$mst_id."**".str_replace("'", '', $txt_system_id)."**".$update_id_dtls_id;
				}
				else
				{
					oci_rollback($con);
					echo "10**".$mst_id."**".$update_id_dtls_id;
				}
			}
			disconnect($con);
			die;
		}
		else
		{
			echo "10";disconnect($con);die;
		}
	}
	
	
}

if($action=="primary_eval_fac_body")
{
	?>
    <tr id="evaluationfactor_1">
        <td width="220px">
            <? 
                
                echo create_drop_down( "cbo_1", 220, $quot_evaluation_factor, 1, 1, "-- Select Factor --", $selected, "", "", "", "", "", "" );
            ?>	 
        </td>
        <td width="220">
            <input style="width:210px;" type="text" name="txtevaluationfactor_1" id="txtevaluationfactor_1"  class="text_boxes" />
            
            <input type="hidden" id="updateiddtls_1" />
        </td>
        <td width="70" align="left">
          &nbsp;
            <input style="width:25px;" type="button" id="incrementfactor_1"  class="formbutton" value="+" onClick="add_factor_row(1)"/>
            <input style="width:25px;" type="button" id="decrementfactor_1"  class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1,'evaluation_tbl')"/>
            
        </td>
    </tr>
    <?
}

/* After save -- *List view*/

if($action=="show_dtls_list_view") // 
{ 

	if($db_type==0) $conct_gr=" concat(b.item_name,',',a.sub_group_name,',',a.item_description,',',a.item_size) as aname ";
	 if($db_type==2) $conct_gr=" b.item_name || ','|| a.sub_group_name || ',' || a.item_description || ',' || a.item_size as aname ";
	 
	  
	 $sql="select a.id,$conct_gr from product_details_master a, lib_item_group b where a.item_group_id=b.id";
	
	 $item_name_library=return_library_array($sql, "id","aname"); 
	 $supplier_id=return_library_array( "select id,supplier_name from  lib_supplier", "id", "supplier_name");
	 $arr=array(0=>$supplier_id,1=>$item_name_library);	
	 $sql_1 = "select a.id,a.supplier_id,a.quotation_ref,a.requ_item_id, b.id as uids from inv_quot_evalu_dtls a,inv_quot_evalu_mst b where b.id=$data and b.id=a.mst_id and a.status_active=1 and a.is_deleted=0 order by a.id";
	
	echo  create_list_view("list_view", "Supplier,Requisition Item,Quotation Ref","250,220,220","700","320",1,$sql_1, "get_php_form_data","id,supplier_id","'populate_data_from_data'",1,"supplier_id,requ_item_id,0",$arr,"supplier_id,requ_item_id,quotation_ref","requires/quotation_evaluation_controller") ;
	die;
}
if($action=="populate_data_from_data")
{
	$data=explode('_',$data);
	 if($db_type==0) $conct_gr=" concat(b.item_name,',',a.sub_group_name,',',a.item_description,',',a.item_size) as aname ";
	 if($db_type==2) $conct_gr=" b.item_name || ','|| a.sub_group_name || ',' || a.item_description || ',' || a.item_size as aname ";
	 $sql1="select a.id,$conct_gr from product_details_master a, lib_item_group b where a.item_group_id=b.id";
	 $item_name_library=return_library_array($sql1, "id","aname");
	
	$supplier_id=return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$sql =sql_select("select id,mst_id,supplier_id,quotation_ref,requ_item_id from  inv_quot_evalu_dtls where id=$data[0] and supplier_id='$data[1]'");
	
	foreach($sql as $row)
	{
		 echo "document.getElementById('update_id_dtls_id').value 			= '".$row[csf("id")]."';\n";
		 echo "document.getElementById('cbo_supplier_id').value 			= '".$row[csf("supplier_id")]."';\n";
		 echo "document.getElementById('txt_quotaion_ref').value 			= '".$row[csf("quotation_ref")]."';\n";
		 echo "document.getElementById('txt_requisition_item').value 		= '".$item_name_library[$row[csf("requ_item_id")]]."';\n";
		 echo "document.getElementById('hidden_requsition').value 			= '".$row[csf("requ_item_id")]."';\n";
		 echo "update_factor_data(  ".$row[csf("id")].");";
		 echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_quotation_evaluation',1);\n";


  	}
	exit();

}

if($action=="show_factor_list")
{
	$i=0;
	//echo "select id,dtls_mst_id,evalu_factor_id,value from inv_quot_evalu_factor where dtls_mst_id=$data";
	$sql=sql_select("select id,dtls_mst_id,evalu_factor_id,value from inv_quot_evalu_factor where dtls_mst_id=$data order by id asc");
	$count=count($sql);
	foreach( $sql as $row)
	{
		$i++;
		if($i!=$count) $display="none"; else $display="";
		?>
        <tr id="evaluationfactor_<? echo $i; ?>">
            <td width="220px">
                <? 
                    
                    echo create_drop_down( "cbo_".$i, 220, $quot_evaluation_factor, 1, 1, "-- Select Factor --", $row[csf("evalu_factor_id")], "", "", "", "", "", "" );
                ?>	 
            </td>
            <td width="220">
                <input style="width:210px;" type="text" name="txtevaluationfactor_<? echo $i; ?>" id="txtevaluationfactor_<? echo $i; ?>" value="<? echo $row[csf("value")]; ?>"  class="text_boxes" />
                
                <input type="hidden" id="updateiddtls_<? echo $i; ?>" value="<? echo $row[csf("id")]; ?>" />
            </td>
            <td width="70" align="left" >
              &nbsp;
                <input style="width:25px;display:<? echo $display; ?>" type="button" id="incrementfactor_<? echo $i; ?>"  class="formbutton" value="+" onClick="add_factor_row(<? echo $i; ?>)"/>
                <input style="width:25px;display:<? echo $display; ?>" type="button" id="decrementfactor_<? echo $i; ?>"  class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>,'evaluation_tbl')"/>
            </td>
        </tr>
        <?
	}

}
?>


<?
if($action=="quot_popup")//System ID  Popup
{
  	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);  
?>
     
<script>
	function js_set_value(id)
	{
 		$("#hidden_sys_number").val(id); // qu number

		parent.emailwindow.hide();
	}
</script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="780" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>                	 
                    <th width="250">Item Category</th>
                    <th width="250">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="center">
                        <? 
						
 							echo create_drop_down("cbo_item_category_id", 200,$item_category,"", 1, "-- Select --", $selected, "","","","","","1,2,3,4,12,13,14"); 
                        ?>
                    </td>
           
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                        
                    </td> 
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_item_category_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_qe_search_list_view', 'search_div', 'quotation_evaluation_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
            </tr>
        	<tr>                  
            	<td align="center" height="40" valign="middle" colspan="5">
					<? echo load_month_buttons(1);  ?>
                    <!-- Hidden field here-------->
                     <input type="hidden" id="hidden_sys_number"/>
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
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}
?>

<?
/* System List view */
if($action=="create_qe_search_list_view")
{
	$data = explode("_",$data);
	$company_id=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$requ_no=return_library_array( "select id,requ_prefix_num from inv_purchase_requisition_mst",'id','requ_prefix_num');
	if ($data[0]!=0) $item_category_val=" and item_category_id='$data[0]'"; else { echo "Please Select item category."; die; }
	 if($db_type==2)
	 {
	if ($data[1]!="" &&  $data[2]!="") $order_rcv_date = "and dates between '".change_date_format($data[1], 'mm-dd-yyyy','/',1)."' and '".change_date_format($data[2],'mm-dd-yyyy','/',1)."'"; else $order_rcv_date="";
	 }
	  if($db_type==0)
	 {
	if ($data[1]!="" &&  $data[2]!="") $order_rcv_date = "and dates between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; else $order_rcv_date="";
	 }
		
		$sql = "select id,requ_prefix_num,system_id,company_id,dates,comments,requ_no_id,item_category_id from inv_quot_evalu_mst where status_active=1 and is_deleted=0 and company_id=$data[3] and item_category_id=$data[0] $item_category_val $order_rcv_date ";
		$arr=array (1=>$company_id,4=>$requ_no,5=>$item_category);

	echo  create_list_view("list_view", "System No,Company,Date,Comments,Requisition No,Item Category", "120,100,100,100,100,100","800","250",0, $sql , "js_set_value", "id,requ_no_id", "", 1, "0,company_id,0,0,requ_no_id,item_category_id", $arr , "requ_prefix_num,company_id,dates,comments,requ_no_id,item_category_id", "quotation_evaluation_controller","",'0,0,3,0,0,0') ; 
exit();

}

if($action=="load_php_mst_form")
{
	$requ_no=return_library_array( "select id,requ_no from inv_purchase_requisition_mst",'id','requ_no');
	// "select id,system_id,company_id,location_name,dates,comments,requ_no_id,item_category_id from inv_quot_evalu_mst where id=$data";
	 $sql = sql_select("select id,system_id,company_id,location_name,dates,comments,requ_no_id,item_category_id from inv_quot_evalu_mst where id=$data");
	
	foreach ($sql as $row)
	{	
		echo "document.getElementById('hidden_quotation').value 					= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_system_id').value 						= '".$row[csf("system_id")]."';\n";
		echo "document.getElementById('cbo_company_id').value 						= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_location_name').value 					= '".$row[csf("location_name")]."';\n";
		echo "document.getElementById('txt_requisition_date').value 				= '".change_date_format($row[csf("dates")])."';\n";
		echo "document.getElementById('txt_comment').value							= '".$row[csf("comments")]."';\n";
		echo "document.getElementById('req_id').value								= '".$row[csf("requ_no_id")]."';\n";
		echo "document.getElementById('txt_requisition_no').value					= '".$requ_no[$row[csf("requ_no_id")]]."';\n";    
		echo "document.getElementById('cbo_item_category_id').value					= '".$row[csf("item_category_id")]."';\n";
		//echo "document.getElementById('cbo_supplier_id').value					= '".$row[csf("item_category_id")]."';\n";
		echo "load_drop_down( 'requires/quotation_evaluation_controller', document.getElementById('cbo_company_id').value, 'load_drop_down_supplier','supplier_td');\n"; 
		
	}	
exit();
}
/*Report Print*/
if($action=="quotation_evaluation_print"){
	 extract($_REQUEST);
	$data=explode('*',$data);
?>
	<div id="table_row" style="width:930px;">

<?  $company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location=return_field_value("location_name","lib_location","id=$data[3]" );
	$address=return_field_value("address","lib_location","id=$data[3]");
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	 if($db_type==0) $conct=" concat(b.item_name,',',a.sub_group_name,',',a.item_description,',',a.item_size) as aname ";
	 if($db_type==2) $conct=" b.item_name || ','|| a.sub_group_name || ',' || a.item_description || ',' || a.item_size as aname ";
	  
	  
	 $sql="select a.id,$conct from product_details_master a, lib_item_group b where a.item_group_id=b.id";
	 $item_name_library=return_library_array($sql, "id","aname");
?>
 <table width="900" align="right">
            <tr class="form_caption">
                <td colspan="6" align="center"><h1><? echo $company_library[$data[0]]; ?></h1></td>
            </tr>
            <tr class="form_caption">
                <td colspan="6" align="center"><? echo $location.",".$address;  ?></td>
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:18px"><center><strong><? echo $data[11]; ?></strong></center></td>
            </tr>
               
            <tr>
                <td width="130"><strong>Item Category:</strong></td> <td width="175px"><? echo $item_category[$data[2]]; ?></td>
                <td width="120"><strong>Evaluation ID:</strong></td><td width="175px"><? echo $data[1]; ?></td>
            </tr>
            <tr>
				<td><strong>Location:</strong></td> <td width="175px"><? echo $location; ?></td>
                <td><strong>Evaluation Date:</strong></td><td width="175px"><? echo $data[4]; ?></td>
               
            </tr>
            <tr>
                <td><strong>Requisition No.</strong></td> <td width="175px"><? echo $data[5]; ?></td>

            </tr>
 </table>
        <br>
        	<div style="width:100%;">
 <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="50">SL</th>
                <th width="80" align="center">Requisition Item</th>
                <th width="200" align="center">Evaluation Factor</th>
                <?
				 if($db_type==0) $suplier_gr="group by supplier_id";
				 if($db_type==2) $suplier_gr="group by supplier_id";
				
				 if($db_type==0)
					{
					$supplier_id_lib=return_library_array( "select id,supplier_id from inv_quot_evalu_dtls where mst_id='$data[6]' $suplier_gr", "id","supplier_id"  );
					 }
					  if($db_type==2)
					{
					$supplier_id_lib=return_library_array( "select supplier_id from inv_quot_evalu_dtls where mst_id='$data[6]' $suplier_gr", "supplier_id","supplier_id"  );
					 }
				foreach($supplier_id_lib as $key=>$value)
				{
				?>
					<th width="70" align="center"><? echo $supplier_library[$value]; ?></th> 
				<? 
				}
				?>
            </thead>
            <?
            $cond="";
	if($data[1]!="") $cond .= " and a.system_id='$data[1]'";
	if($data[2]!="") $cond .= " and a.item_category_id='$data[2]'";
	
	 if($db_type==0) $conct_f=" concat(supplier_id,'',evalu_factor_id) ";
	 if($db_type==2) $conct_f="supplier_id || '' || evalu_factor_id ";
	
	 if($db_type==0) $groupby=" group by a.requ_item_id";
	 if($db_type==2) $groupby="group by a.requ_item_id,a.mst_id";
	
	 if($db_type==0) $groupby_factor=" group by evalu_factor_id";
	 if($db_type==2) $groupby_factor="group by a.id,a.evalu_factor_id ";
	 
	 if($db_type==0) $conct_gr="group";
	 if($db_type==2) $conct_gr="wm";

	 if($db_type==0)
					{
	$requisition_factors=return_library_array( "select a.id,a.evalu_factor_id from inv_quot_evalu_factor a,inv_quot_evalu_dtls b where b.id=a.dtls_mst_id and b.mst_id='$data[6]' $groupby_factor", "id", "evalu_factor_id"  );
					}
	 if($db_type==2)
					{
	$requisition_factors=return_library_array( "select a.id,a.evalu_factor_id from inv_quot_evalu_factor a,inv_quot_evalu_dtls b where b.id=a.dtls_mst_id and b.mst_id='$data[6]' $groupby_factor", "evalu_factor_id", "evalu_factor_id"  );
					}
	$sql="select  a.mst_id,a.requ_item_id, ";
	$i=0;
	foreach($supplier_id_lib as $key=>$value)
	{
		foreach($requisition_factors as $req=>$req_val)
		{
			if($i!=0)$sql.=",";
			if($db_type==0)
			{
			$sql.="group_concat(CASE WHEN  $conct_f='".$value.$req_val."' THEN  value  END )as fval$value$req_val ";
			}
			else
			{
			$sql.="listagg(CASE WHEN ($conct_f='".$value.$req_val."') THEN  value  END ) within group  (order by value) as fval$value$req_val ";
	
			}
			$i++;
		}
		//echo $sql;
	}
	$sql.=" from inv_quot_evalu_factor b,inv_quot_evalu_dtls a where a.id=b.dtls_mst_id and a.mst_id='$data[6]' $groupby ";
	//echo ($sql);
	$i=0;
			$sql_result=sql_select($sql);
			$k=0;
			$row_arr=array();
			foreach($sql_result as $row)
			{
				$k++;
				
				foreach($requisition_factors as $req=>$req_val)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					$i++;	
		?>
			<tr> 
               
                <?
				if(!in_array($k,$row_arr))
				{
					$row_arr[]=$k;
				?>
                 	<td rowspan="<? echo count($requisition_factors); ?>"><? echo $k; ?></td>
                	<td rowspan="<? echo count($requisition_factors); ?>"><? echo $item_name_library[$row[csf('requ_item_id')]];?></td>
                <? } ?>
                <td  bgcolor="<? echo $bgcolor; ?>"><? echo $quot_evaluation_factor[$req_val]; ?></td>
                <?
					foreach($supplier_id_lib as $key=>$value)
					{
						
						?> 
                        <td  bgcolor="<? echo $bgcolor; ?>"><? echo $row[csf('fval').$value.$req_val]; ?></td>
                        <?
					}
				?>
                
			</tr>
          
			<?php
				if(($item_name_library[$row['requ_item_id']])!="")  $item_name_library[$row['requ_item_id']]=$item_name_library[""]; 
				
			}
			}	
		?>
      <br><br>  
 </table>
<table  width="900"  border="1" style="position: relative; left:30px;" rules="all" class="rpt_table">
 		<tr>
            <td colspan="7" height="40">Comments: &nbsp; <? echo $data[10];?></td>
         </tr>
        
 		
 </table> <br>
	 <?
		echo signature_table(69, $data[0], "900px");
	 ?>
</div>
</div>
	<?
	exit();
}

?>