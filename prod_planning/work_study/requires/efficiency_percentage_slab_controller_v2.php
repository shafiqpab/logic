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
Updated by 		         : 	 
Update date		         : 	 
QC Performed BY	         :		
QC Date			         :	
Comments		         : 	 
*/
header('Content-type:text/html; charset=utf-8');
session_start();
if($_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];


if ($action=="save_update_delete_efficiency_percentage")
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
			
			$id_dtls=return_next_id( "id", "efficiency_percentage_slab", 1 ) ;
 			$field_array= "id,smv_lower_limit,smv_upper_limit,order_qty_lower_limit,order_qty_upper_limit,efficiency_new_order,efficiency_repeat_order,learning_cub_percentage,inserted_by,insert_date,status_active,is_deleted,company_id";

 			$data_array="";
			for ($i=1;$i<=$total_row;$i++)
		    {  
				$txtSmvLowerLimit="txtSmvLowerLimit_".$i;
				$txtSmvUpperLimit="txtSmvUpperLimit_".$i;
				$txtOrderQtyLowerLimit="txtOrderQtyLowerLimit_".$i;
				$txtOrderQtyUpperLimit="txtOrderQtyUpperLimit_".$i;
				$txtNewOrder="txtNewOrder_".$i;
				$txtLearningCubPercentage="txtLearningCubPercentage_".$i;
				$txtRepeatOrder="txtRepeatOrder_".$i;
				//echo $$txtOrderQtyLowerLimit ;die;
				if(str_replace("'", '', $$txtSmvLowerLimit)=='' and str_replace("'", '', $$txtSmvUpperLimit)=='' and  str_replace("'", '', $$txtOrderQtyLowerLimit)=='' and  str_replace("'", '', $$txtOrderQtyUpperLimit)==''  and  str_replace("'", '', $$txtNewOrder)==''  and  str_replace("'", '', $$txtRepeatOrder)=='')
				{
					//echo "If every value is null ";
				}
					else
					{
				if ($data_array!='') $data_array .=",";

				$data_array .="(".$id_dtls.",".$$txtSmvLowerLimit.",".$$txtSmvUpperLimit.",".$$txtOrderQtyLowerLimit.",".$$txtOrderQtyUpperLimit.",".$$txtNewOrder.",".$$txtRepeatOrder.",".$$txtLearningCubPercentage.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0, ".$company_name.")";
				
				
				$id_dtls=$id_dtls+1;
			}
				
		    } 

			//echo "5000".$data_array; die();
 			//echo "5**"."INSERT INTO efficiency_percentage_slab(".$field_array."VALUES ".$data_array; die;

			$rID_1=sql_insert("efficiency_percentage_slab",$field_array,$data_array,1);

			if($db_type==0)
			{
				if($rID_1){
					mysql_query("COMMIT");  
					echo "0**".$company_name;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**";
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($rID_1)
				{
					oci_commit($con);  
					echo "0**".$company_name;

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
  			$id_dtls=return_next_id( "id", "efficiency_percentage_slab", 1);
			
			$field_array_up="smv_lower_limit*smv_upper_limit*order_qty_lower_limit*order_qty_upper_limit*efficiency_new_order*efficiency_repeat_order*learning_cub_percentage*updated_by*update_date*status_active*is_deleted";
			
			$field_array= "id,smv_lower_limit,smv_upper_limit,order_qty_lower_limit,order_qty_upper_limit,efficiency_new_order,efficiency_repeat_order,learning_cub_percentage,inserted_by,insert_date,status_active,is_deleted,company_id";



			$add_comma=0; $data_array=""; //echo "10**";
			for ($i=1;$i<=$total_row;$i++)
		    {
				$txtSmvLowerLimit="txtSmvLowerLimit_".$i;
				$txtSmvUpperLimit="txtSmvUpperLimit_".$i;
				$txtOrderQtyLowerLimit="txtOrderQtyLowerLimit_".$i;
				$txtOrderQtyUpperLimit="txtOrderQtyUpperLimit_".$i;
				$txtNewOrder="txtNewOrder_".$i;
				$txtRepeatOrder="txtRepeatOrder_".$i;
				$txtLearningCubPercentage="txtLearningCubPercentage_".$i;
				$updateIdDtls="updateDtls_".$i;
				
				$update_all_status=execute_query("update efficiency_percentage_slab set status_active=0,is_deleted=1 where company_id='$company_name'");

			if(str_replace("'", '', $$txtSmvLowerLimit)=='' and str_replace("'", '', $$txtSmvUpperLimit)=='' and  str_replace("'", '', $$txtOrderQtyLowerLimit)=='' and  str_replace("'", '', $$txtOrderQtyUpperLimit)==''  and  str_replace("'", '', $$txtNewOrder)==''  and  str_replace("'", '', $$txtRepeatOrder)=='')
				{
					//echo "If every value is null ";
				}
			 	else
			  	{

	 				if (str_replace("'",'',$$updateIdDtls)!="")
					{
						$id_arr[]=str_replace("'",'',$$updateIdDtls);
						
						$data_array_up[str_replace("'",'',$$updateIdDtls)] =explode("*",("".$$txtSmvLowerLimit."*".$$txtSmvUpperLimit."*".$$txtOrderQtyLowerLimit."*".$$txtOrderQtyUpperLimit."*".$$txtNewOrder."*".$$txtRepeatOrder."*".$$txtLearningCubPercentage."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*1*0"));
						
					}
				 	else 
					{
						if ($add_comma!=0) $data_array .=",";
						$data_array .="(".$id_dtls.",".$$txtSmvLowerLimit.",".$$txtSmvUpperLimit.",".$$txtOrderQtyLowerLimit.",".$$txtOrderQtyUpperLimit.",".$$txtNewOrder.",".$$txtRepeatOrder.",".$$txtLearningCubPercentage.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0, ".$company_name.")";
	 				
					    $id_dtls=$id_dtls+1;
	 					$add_comma++;	
					}
			  	}	
		    }

		     // $id_ups=implode(',',$id_arr); 
		     // $update_specific_status=execute_query("update efficiency_percentage_slab set status_active=1,is_deleted=0 where id in($id_ups)");
			//echo $data_array.'=='; die;
			//$rID_1=sql_insert("sample_development_dtls",$field_array2,$data_array2,1);
			 //echo "10**"; print_r($data_array_up);die;
			
			
			$flag=1;
			if($data_array!="")
			{
				//echo "insert into sample_development_dtls (".$field_array.") values ".$data_array; 
				$rID=sql_insert("efficiency_percentage_slab",$field_array,$data_array,0);
				if($rID) $flag=1; else $flag=0;
			}
			/*echo '=='.$data_array.'==';
			die;*/
			if($data_array_up!="")
			{
				$rID1=execute_query(bulk_update_sql_statement("efficiency_percentage_slab", "id",$field_array_up,$data_array_up,$id_arr ));
				if($rID1) $flag=1; else $flag=0;
			}
			
			
			if($db_type==0)
			{
				if($rID_1){
					mysql_query("COMMIT");  
					echo "1**".str_replace("'",'',$update_id)."**4";
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
					echo "1**".str_replace("'",'',$update_id)."**4";
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
