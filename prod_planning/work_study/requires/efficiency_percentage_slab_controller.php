<?
/*-------------------------------------------- Comments
Purpose			         :  This form will create Efficiency Percentage Slab Entry  						
Functionality	         :	
JS Functions	         :
Created by		         :	Mirza Tahmid Tajik
Creation date 	         :  27/05/2017
Requirment Client        :  
Requirment By            : 
Requirment type          : 
Requirment               : 
Affected page            : 
Affected Code            :                
DB Script                : 
Updated by      		 :  Shafiq    
Update date     		 :  27-06-2019   
QC Performed BY	         :		
QC Date			         :	
Comments		         : 	 
*/
header('Content-type:text/html; charset=utf-8');
session_start();
if($_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
include('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];

if ($action=="efficiency_percentage_menual_insert"){
//localhost/platform-v3.1/prod_planning/work_study/requires/efficiency_percentage_slab_controller.php?action=efficiency_percentage_menual_insert
		
		
		$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
		
		$field_array_mst= "id,company_id,location_id,gmts_item_id,buyer_id,inserted_by,insert_date,status_active,is_deleted";
		$field_array_dtls= "id,mst_id,slab_no,smv_lower_limit,smv_upper_limit,learning_cub_percentage,inserted_by,insert_date,status_active,is_deleted";
		
		
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		
		$buyer_arr=[308=>'--'];
		$garments_item=[83=>'--'];
		
		foreach($buyer_arr as $buyer_name=>$buyer){
			
			foreach($garments_item as $item_name=>$item){
				
				$data_array_dtls='';
				$data_array_mst='';
				
				$company_name=1;
				$location_name=1;
				
				//Master part..............................
				$id=return_next_id( "id", "efficiency_percentage_slab_mst", 1 ) ;
				$data_array_mst="(".$id.",".$company_name.",".$location_name.",".$item_name.",".$buyer_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				
				//Dtls part....................................
			
				$saveDataArr[0]=array(SLAB_NO=>1,SMV_LOWER_LIMIT=>1,SMV_UPPER_LIMIT=>4.5,LEARNING_CUB_PERCENTAGE=>'27,45,60');
				$saveDataArr[1]=array(SLAB_NO=>1,SMV_LOWER_LIMIT=>4.51,SMV_UPPER_LIMIT=>8,LEARNING_CUB_PERCENTAGE=>'21,33,48,60');
				$saveDataArr[2]=array(SLAB_NO=>1,SMV_LOWER_LIMIT=>8.01,SMV_UPPER_LIMIT=>30,LEARNING_CUB_PERCENTAGE=>'13,20,30,40,55');
				
				$id_dtls=return_next_id( "id", "efficiency_percentage_slab_dtl", 1 ) ;
				foreach($saveDataArr as $rows){
				if ($data_array_dtls!='') $data_array_dtls .=",";

				$data_array_dtls .="(".$id_dtls.",".$id.",'".$rows[SLAB_NO]."','".$rows[SMV_LOWER_LIMIT]."','".$rows[SMV_UPPER_LIMIT]."','".$rows[LEARNING_CUB_PERCENTAGE]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";	
				$id_dtls=$id_dtls+1;
				}
				
				$rID=sql_insert("efficiency_percentage_slab_mst",$field_array_mst,$data_array_mst,1);
				$rID_dtl=sql_insert("efficiency_percentage_slab_dtl",$field_array_dtls,$data_array_dtls,1);
				
				
				 //echo $data_array_dtls;die;
				 //echo $rID.'='.$rID_dtl;
			
				if($db_type==0)
				{
					if($rID && $rID_dtl)
					{
						mysql_query("COMMIT");  
						echo "0**".$company_name."**".$id;
					}
					else
					{
						mysql_query("ROLLBACK"); 
						echo "10**";
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($rID && $rID_dtl)
					{
						oci_commit($con);  
						echo "0**".$company_name."**".$id;
		
					}
				else{
						oci_rollback($con); 
						echo "10**";
					}
				}
			
			}
		}
		disconnect($con);
		die;
		exit();
		
	}




if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 135, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id  in($data) order by location_name","id,location_name", 1, "-- Select Location--", $selected, "",0 );     	 
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 135, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  group by buy.id,buy.buyer_name  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "","" );
  exit();	 
}

if ($action=="save_update_delete_efficiency_percentage")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	//Check duplicate........................................................................
	$sql="SELECT b.ID,b.SMV_LOWER_LIMIT,b.SMV_UPPER_LIMIT,b.LEARNING_CUB_PERCENTAGE,b.LEVEL_TYPE from efficiency_percentage_slab_mst a, efficiency_percentage_slab_dtl b where a.id=b.mst_id and a.COMPANY_ID =$company_name and a.LOCATION_ID=$location_name and a.GMTS_ITEM_ID=$item_name and a.BUYER_ID=$buyer_name and   a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id not in(".($updateMstId*1).") order by a.id asc";
	  // echo $sql;die;
	$sql_result_arr = sql_select($sql);
	foreach($sql_result_arr as $row){

		for ($i=1;$i<=$total_row;$i++)
	    {  
			$txtSmvLowerLimit="txtSmvLowerLimit_".$i;
			$txtSmvUpperLimit="txtSmvUpperLimit_".$i;
			$txtLearningCubPercentage="txtLearningCubPercentage_".$i;
			$cboComplexityLevel="cboComplexityLevel_".$i;

			$txtSmvLowerLimit = str_replace("'",'',$$txtSmvLowerLimit);
			$txtSmvUpperLimit = str_replace("'",'',$$txtSmvUpperLimit);
			$txtLearningCubPercentage = str_replace("'",'',$$txtLearningCubPercentage);
			$cboComplexityLevel = str_replace("'",'',$$cboComplexityLevel);
			
			if($row['SMV_LOWER_LIMIT']==$txtSmvLowerLimit && $row['SMV_UPPER_LIMIT']==$txtSmvUpperLimit && $row['LEARNING_CUB_PERCENTAGE']==$txtLearningCubPercentage && $row['LEVEL_TYPE']==$cboComplexityLevel){
				echo "11**";
				exit();
			}
		}

	}
	//.............................................Check duplicate end;
	
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		// ================================ FOR MASTER PART ==================================
		$id=return_next_id( "id", "efficiency_percentage_slab_mst", 1 ) ;
		$field_array_mst= "id,company_id,location_id,gmts_item_id,buyer_id,inserted_by,insert_date,status_active,is_deleted";
		$data_array_mst="(".$id.",".$company_name.",".$location_name.",".$item_name.",".$buyer_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";

		// =============================== FOR DTLS PART ==================================
		$id_dtls=return_next_id( "id", "efficiency_percentage_slab_dtl", 1 ) ;
		$field_array_dtls= "id,mst_id,slab_no,smv_lower_limit,smv_upper_limit,learning_cub_percentage,level_type,inserted_by,insert_date,status_active,is_deleted";

		$data_array_dtls="";
		for ($i=1;$i<=$total_row;$i++)
	    {  
			$slabNo="slabNo_".$i;
			$txtSmvLowerLimit="txtSmvLowerLimit_".$i;
			$txtSmvUpperLimit="txtSmvUpperLimit_".$i;
			$txtLearningCubPercentage="txtLearningCubPercentage_".$i;
			$cboComplexityLevel="cboComplexityLevel_".$i;
			//echo $$txtOrderQtyLowerLimit ;die;
			if(str_replace("'", '', $$txtSmvLowerLimit)!='' and str_replace("'", '', $$txtSmvUpperLimit)!='')
			{
				if ($data_array_dtls!='') $data_array_dtls .=",";
				$data_array_dtls .="(".$id_dtls.",".$id.",".$$slabNo.",".$$txtSmvLowerLimit.",".$$txtSmvUpperLimit.",".$$txtLearningCubPercentage.",".$$cboComplexityLevel.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";	
				$id_dtls=$id_dtls+1;
			}
			
	    } 

		$rID=sql_insert("efficiency_percentage_slab_mst",$field_array_mst,$data_array_mst,1);
		$rID_dtl=sql_insert("efficiency_percentage_slab_dtl",$field_array_dtls,$data_array_dtls,1);
		
		

		if($db_type==0)
		{
			if($rID && $rID_dtl)
			{
				mysql_query("COMMIT");  
				echo "0**".$company_name."**".$id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_dtl)
			{
				oci_commit($con);  
				echo "0**".$company_name."**".$id;

			}
		else{
				oci_rollback($con); 
				echo "10**";
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
		//===================================== FOR MASTER PART =============================
		$id=return_next_id( "id", "efficiency_percentage_slab_mst", 1);
		$field_array_mst= "company_id*location_id*gmts_item_id*buyer_id*updated_by*update_date";
		$data_array_mst="".$company_name."*".$location_name."*".$item_name."*".$buyer_name."*".$user_id."*'".$pc_date_time."'";

		// =============================== FOR DTLS PART ======================================
		$id_dtls=return_next_id( "id", "efficiency_percentage_slab_dtl", 1);		
		$field_array_up="slab_no*smv_lower_limit*smv_upper_limit*learning_cub_percentage*level_type*updated_by*update_date*status_active*is_deleted";
		
		$field_array= "id,mst_id,slab_no,smv_lower_limit,smv_upper_limit,learning_cub_percentage,level_type,inserted_by,insert_date,status_active,is_deleted";

		$add_comma=0; $data_array=""; //echo "10**";
		for ($i=1;$i<=$total_row;$i++)
	    {
			$slabNo="slabNo_".$i;
			$txtSmvLowerLimit="txtSmvLowerLimit_".$i;
			$txtSmvUpperLimit="txtSmvUpperLimit_".$i;
			$txtLearningCubPercentage="txtLearningCubPercentage_".$i;
			$cboComplexityLevel="cboComplexityLevel_".$i;
			$updateIdDtls="updateDtls_".$i;

			if(str_replace("'", '', $$txtSmvLowerLimit)!='' and str_replace("'", '', $$txtSmvUpperLimit)!='')
			{
				if ($add_comma!=0) $data_array .=",";
				$data_array .="(".$id_dtls.",".$updateMstId.",".$$slabNo.",".$$txtSmvLowerLimit.",".$$txtSmvUpperLimit.",".$$txtLearningCubPercentage.",".$$cboComplexityLevel.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$id_dtls=$id_dtls+1;
				$add_comma++;	
		  	}	
	    }


		$dtlsrDelete = execute_query("delete from efficiency_percentage_slab_dtl where mst_id=$updateMstId",0);
		$rID=sql_update("efficiency_percentage_slab_mst",$field_array_mst,$data_array_mst,"id","".$updateMstId."",0);
		$flag=1;
		if($data_array!="" && $rID !=0)
		{
			$rID_dtls=sql_insert("efficiency_percentage_slab_dtl",$field_array,$data_array,0);
			if($rID_dtls) $flag=1; else $flag=0;
		}

		

		
		
		if($db_type==0)
		{
			if($rID_1){
				mysql_query("COMMIT");  
				echo "1**$company_name**".str_replace("'",'',$updateMstId)."**4";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		elseif($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "1**$company_name**".str_replace("'",'',$updateMstId)."**4";
			}
			else
			{
				oci_rollback($con); 
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}

}

		




if($action=="show_dtls_listview")
{
	$data_ex = explode("**", $data);
	$company_id = $data_ex[0];
	$item_id = $data_ex[1];
	$buyer_id = $data_ex[2];
	$sql_cond = "";
	if($company_id){$sql_cond =" company_id=$company_id";}
	if($item_id){$sql_cond .=" and gmts_item_id=$item_id";}
	if($buyer_id){$sql_cond .=" and buyer_id=$buyer_id";}

	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');

	$sql=" SELECT id,company_id,gmts_item_id,buyer_id,location_id from efficiency_percentage_slab_mst where $sql_cond and status_active=1 and is_deleted=0 order by id asc";
	$sql_result = sql_select($sql);
	if(count($sql_result)==0){echo "Data not available";}
	
    $i=1;
    foreach ($sql_result as $val) 
    { 
    	if ($i%2==0)  $bgcolor="#E9F3FF";
		else $bgcolor="#FFFFFF";    
        ?>
        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onClick="fnc_load_from_data(<? echo $val[csf('id')]; ?>);fnc_load_from_data_dtls('<? echo $val[csf('id')]; ?>');">
        	<td width="40" align="left"><? echo $i;?></td>
        	<td width="130" align="left"><? echo $company_arr[$val[csf('company_id')]];?></td>
        	<td width="130" align="left"><? echo $location_arr[$val[csf('location_id')]];?></td>
        	<td width="130" align="left"><? echo $garments_item[$val[csf('gmts_item_id')]];?></td>
        	<td width="130" align="left"><? echo $buyer_arr[$val[csf('buyer_id')]];?></td>
        </tr>
        <?
        $i++;
     }
    
	exit();
}


if($action=="show_dtls_listview_data")
{

	//$level_type_arr=return_library_array( "select ID,LEVEL_TYPE from LIB_COMPLEXITY_LEVEL where is_deleted=0 and status_active=1 order by LEVEL_TYPE",'ID','LEVEL_TYPE');
	$sql="SELECT b.id,b.slab_no,b.smv_lower_limit,b.smv_upper_limit,b.learning_cub_percentage,b.level_type from efficiency_percentage_slab_mst a, efficiency_percentage_slab_dtl b where a.id=b.mst_id and a.id = '$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 order by a.id asc";
	//echo $sql;die;
	$sql_result = sql_select($sql);
	if(count($sql_result)==0){echo "Data not available";}
	
    $i=1;
    foreach ($sql_result as $val) 
    { 
    	if ($i%2==0)  $bgcolor="#E9F3FF";
		else $bgcolor="#FFFFFF";    
        ?>
        <tr id="tr_<? echo $i;?>" class="general">
            <td align="center">
                <input type="button" id="slabNo_<? echo $i;?>" name="slabNo_<? echo $i;?>" value="<? echo $i;?>" style="background-color:#B0B0B0" disabled value="<? echo $val[csf('slab_no')];?>">
            </td>
            <td align="center">
                <input type="text" name="txtSmvLowerLimit_<? echo $i;?>" id="txtSmvLowerLimit_<? echo $i;?>" class="text_boxes_numeric" style="width:100px" value="<? echo $val[csf('smv_lower_limit')];?>">
            </td>
            <td align="center">
                <input type="text" name="txtSmvUpperLimit_<? echo $i;?>" id="txtSmvUpperLimit_<? echo $i;?>" class="text_boxes_numeric" style="width:100px" value="<? echo $val[csf('smv_upper_limit')];?>">
            </td>                    
            <td align="center">
                <input type="text" name="txtLearningCubPercentage_<? echo $i;?>" id="txtLearningCubPercentage_<? echo $i;?>" class="text_boxes" style="width:100px" placeholder="0,0,0" value="<? echo $val[csf('learning_cub_percentage')];?>">
                <input type="hidden" id="updateDtls_<? echo $i;?>" name="updateDtls_<? echo $i;?>" class="abc" value="<? echo $val[csf('id')];?>">
            </td>
			<td>
			<?

				echo create_drop_down( "cboComplexityLevel_$i", 100, $complexity_level,"", 1, "-- Select --", $val[csf('level_type')], "" );
			?>
			</td>                         
            <td>
                <input type="button" id="increaserf_<? echo $i;?>" name="increaserf_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_rf_tr(<? echo $i;?>)" />
                <input type="button" id="decreaserf_<? echo $i;?>" name="decreaserf_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_rf_deleteRow(<? echo $i;?>);" />
            </td>
        </tr>
        <?
        $i++;
     }
   
	exit();
}

if($action=="populate_input_form_data")
{
	$sql="SELECT id,company_id,gmts_item_id,buyer_id,location_id from efficiency_percentage_slab_mst where id = '$data' and status_active=1 and is_deleted=0 order by id asc";
		
	$sqlResult =sql_select($sql); 
    $i=1;
	foreach($sqlResult as $result)
	{
		echo "$('#cbo_company_name').val('".$result[csf('company_id')]."');\n";
		echo "$('#cbo_location_name').val('".$result[csf('location_id')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_id')]."');\n";
  		echo "$('#cbo_gmts_item').val('".$result[csf('gmts_item_id')]."');\n";
		echo "$('#updateMstId').val('".$result[csf('id')]."');\n";

	}
 	echo "set_button_status(1, permission, 'fnc_efficiency_percentage',2);;\n";
 	// echo "alert('ok')";
 	exit();

}

if($action=='load_php_dtls_form')
{
	$sql=" select id,smv_lower_limit,smv_upper_limit,order_qty_lower_limit,order_qty_upper_limit,efficiency_new_order,efficiency_repeat_order,company_id,learning_cub_percentage from efficiency_percentage_slab where company_id = '$data' and status_active=1 and is_deleted=0 order by id asc";
		
	$sql_result =sql_select($sql); 
    $i=1;
	if(count($sql_result)>0)
	{	
		
		foreach($sql_result as $row)
		{
			?>

			<tr id="tr_1" class="general">
            <td align="center">
                <input type="button" id="slabNo_<? echo $i;?>" name="slabNo_<? echo $i;?>" value="<?php echo $i;?>" style="background-color:#B0B0B0" disabled>
            </td>
            <td align="center">
                <input type="text" name="txtSmvLowerLimit_<? echo $i;?>" id="txtSmvLowerLimit_<? echo $i;?>" class="text_boxes_numeric " style="width:60px" value="<? echo $row[csf("smv_lower_limit")]; ?>">
            </td>
            <td align="center">
                <input type="text" name="txtSmvUpperLimit_<? echo $i;?>" id="txtSmvUpperLimit_<? echo $i;?>" class="text_boxes_numeric " style="width:60px" value="<? echo $row[csf("smv_upper_limit")]; ?>">
            </td>
            <td align="center">
                <input type="text" name="txtOrderQtyLowerLimit_<? echo $i;?>" id="txtOrderQtyLowerLimit_<? echo $i;?>" class="text_boxes_numeric " style="width:60px" value="<? echo $row[csf("order_qty_lower_limit")]; ?>">
            </td>
            <td align="center">
                <input type="text" name="txtOrderQtyUpperLimit_<? echo $i;?>" id="txtOrderQtyUpperLimit_<? echo $i;?>" class="text_boxes_numeric " style="width:60px" value="<? echo $row[csf("order_qty_upper_limit")]; ?>">
            </td>
            <td align="center">
                <input type="text" name="txtNewOrder_<? echo $i;?>" id="txtNewOrder_<? echo $i;?>" class="text_boxes_numeric " style="width:60px" value="<? echo $row[csf("efficiency_new_order")]; ?>">
            </td>
            <td align="center">
                <input type="text" name="txtRepeatOrder_<? echo $i;?>" id="txtRepeatOrder_<? echo $i;?>" class="text_boxes_numeric " style="width:60px" value="<? echo $row[csf("efficiency_repeat_order")]; ?>">
            
                <input type="hidden" class="" id="updateDtls_<? echo $i;?>" name="updateDtls_<? echo $i;?>" value="<? echo $row[csf("id")]; ?>">
            </td>
            <td align="center">
                <input type="text" name="txtLearningCubPercentage_<? echo $i;?>" id="txtLearningCubPercentage_<? echo $i;?>" value="<? echo $row[csf("learning_cub_percentage")]; ?>" class="text_boxes" style="width:60px">
            </td>
            
            <td>
                <input type="button" id="increaserf_<? echo $i;?>" name="increaserf_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_rf_tr(<? echo $i;?>)" />
                <input type="button" id="decreaserf_<? echo $i;?>" name="decreaserf_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_rf_deleteRow(<? echo $i;?>);" />
            </td>
        </tr>  

            <?
			$i++;
		}
	}
	else{
		?>

		<tr id="tr_1" class="general">
            <td align="center">
                <input type="button" id="slabNo_1" name="slabNo_1" value="1" style="background-color:#B0B0B0" disabled>
            </td>
            <td align="center">
                <input type="text" name="txtSmvLowerLimit_1" id="txtSmvLowerLimit_1" class="text_boxes_numeric " style="width:60px">
            </td>
            <td align="center">
                <input type="text" name="txtSmvUpperLimit_1" id="txtSmvUpperLimit_1" class="text_boxes_numeric " style="width:60px">
            </td>
            <td align="center">
                <input type="text" name="txtOrderQtyLowerLimit_1" id="txtOrderQtyLowerLimit_1" class="text_boxes_numeric " style="width:60px">
            </td>
            <td align="center">
                <input type="text" name="txtOrderQtyUpperLimit_1" id="txtOrderQtyUpperLimit_1" class="text_boxes_numeric " style="width:60px">
            </td>
            <td align="center">
                <input type="text" name="txtNewOrder_1" id="txtNewOrder_1" class="text_boxes_numeric " style="width:60px">
            </td>
            <td align="center">
                <input type="text" name="txtLearningCubPercentage_1" id="txtLearningCubPercentage_1" class="text_boxes" style="width:60px" placeholder="0,0,0">
            </td>
            <td align="center">
                <input type="text" name="txtRepeatOrder_1" id="txtRepeatOrder_1" class="text_boxes_numeric " style="width:60px">
            
                <input type="hidden" class="" id="updateDtls_1" name="updateDtls_1" value=''>
            </td>
            <td>
                <input type="button" id="increaserf_1" name="increaserf_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_rf_tr(1)" />
                <input type="button" id="decreaserf_1" name="decreaserf_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_rf_deleteRow(1);" />
            </td>
        </tr>


	<?
	}
	exit();
}

if($action=='check_data_is_exis')
{
	$sql="select id,smv_lower_limit,smv_upper_limit,order_qty_lower_limit,order_qty_upper_limit,efficiency_new_order,efficiency_repeat_order,company_id from efficiency_percentage_slab where company_id = '$data' and status_active=1 and is_deleted=0";
		$sql_result =sql_select($sql); 
		if(count($sql_result)>0)
		{
			echo "yes";
		}
		else
		{
			echo "no";
		}
}

?>