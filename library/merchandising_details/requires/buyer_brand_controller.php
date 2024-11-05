<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$permission=$_SESSION['page_permission'];
$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="on_change_data")
{
	extract($_REQUEST);
	//echo "select id, brand_name from lib_buyer_brand where buyer_id='$data' and status_active=1 and is_deleted=0 order by id";
	$nameArray= sql_select("select id, brand_name from lib_buyer_brand where buyer_id='$data' and status_active=1 and is_deleted=0 order by id");
	?>
	<fieldset>
        <legend>New Brand Entry</legend>
        <div onLoad="set_hotkey();" style="width:400px;height:auto;" align="center">
        <table width="400px" cellpadding="0" cellspacing="0" border="1" class="rpt_table" align="center" id="brand_tbl" rules="all" >
            <thead>
                <th width="40">SL No</th>
                <th>Brand Name</th>
            </thead>
            <tbody>
				<?
				//echo count($nameArray);
                    if(count($nameArray)>0)
                    {
						$is_update=1;
                        $i=1;
                        foreach($nameArray as $row)
                        {
                            ?>
                            <tr id="trBrand_<?=$i; ?>">
                                <td width="40"><?=$i; ?></td>
                                <td>
                                	<input type="text" name="txtBrandName_<?=$i;?>" id="txtBrandName_<?=$i;?>" class="text_boxes" style="width:300px;" value="<?=$row[csf('brand_name')];?>" onBlur="append_brandName_row(this.value,<?=$i;?>);" />

                                	<input type="hidden" name="updateid_<?=$i;?>" id="updateid_<?=$i;?>" style="width:20px;" class="text_boxes" value="<?=$row[csf('id')];?> " />
                                </td>
                            </tr>
                            <? 
                            $i++;
                        }
						
                    }
                    else
                    {
						$is_update=0;
                        ?>
                        <tr id="trBrand_1">
                            <td width="40">1</td>
                            <td> 
                                <input type="text"name="txtBrandName_1" id="txtBrandName_1" style="width:300px;" class="text_boxes" onBlur="append_brandName_row(this.value,1);" />
                                <input type="hidden" name="updateid_1" id="updateid_1" style="width:20px;" class="text_boxes" />
                            </td>
                        </tr>
                        <?	
                    }
                ?>
            </tbody>
            <tfoot>
            	<tr>
                	<td colspan="2" height="40" valign="bottom" align="center" class="button_container">
					<? 
                    	echo load_submit_buttons( $permission, "fnc_buyer_brand", $is_update,0 ,"reset_form('buyerbrand_1','buyer_brand_name','')",1);
                    ?>
                    </td>	
                </tr>
            </tfoot>
        </table>
        </div>
        <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>
    <?
	exit();
}

if ($action=="save_update_delete")
{  
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)//Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$id = return_next_id( "id", "lib_buyer_brand", 1);
		$field_array="id, buyer_id, brand_name, inserted_by, insert_date";
		$add_comma=0;
		$return_id="";
		for($i=1; $i<=$row_num; $i++)
		{
			$updateid="updateid_".$i;
			$txtBrandName="txtBrandName_".$i;
			
			if(str_replace("'","",$$txtBrandName)!='')
			{
				if ($add_comma!=0) $data_array .=",";
				$data_array.="(".$id.",".$cbo_buyer_id.",".$$txtBrandName.",'".$user_id."','".$pc_date_time."')"; 
				$id=$id+1;
				$add_comma++;
			}
		}
		//echo $return_id;
		//print_r($data_array);die;
		//echo "INSERT INTO lib_buyer_brand(".$field_array.") VALUES ".$data_array;die;
		$rID=sql_insert("lib_buyer_brand",$field_array,$data_array,1);
		
		//----------------------------------------------------------------------------------
		
		if($db_type==0)
		{
			if($rID)
			{ //$row_num
				mysql_query("COMMIT");  
				echo "0**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		elseif($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);   
				echo "0**".$rID;
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
	else if ($operation==1)//Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id = return_next_id( "id", "lib_buyer_brand", 1 );
		$field_array="id, buyer_id, brand_name, inserted_by, insert_date";
		$field_array_up="brand_name*updated_by*update_date";
		
		$add_comma=0;
		$return_id="";
		for($i=1; $i<=$row_num; $i++)
		{
			$updateid="updateid_".$i;
			$txtBrandName="txtBrandName_".$i;
			if(str_replace("'","",$$updateid)=="")
			{
				if(str_replace("'","",$$txtBrandName)!='')
				{
					if ($add_comma!=0) $data_array .=",";
					$data_array.="(".$id.",".$cbo_buyer_id.",".$$txtBrandName.",'".$user_id."','".$pc_date_time."')"; 
					$id=$id+1;
					$add_comma++;
				}
			}
			else
			{
				if(str_replace("'","",$$txtBrandName)!='')
				{
					$updateID_array[]=str_replace("'",'',$$updateid); 
					$data_array_up[str_replace("'",'',$$updateid)]=explode("*",("".$$txtBrandName."*'".$user_id."'*'".$pc_date_time."'"));
				}
			}
		}
		
		$rID=execute_query(bulk_update_sql_statement("lib_buyer_brand","id",$field_array_up,$data_array_up,$updateID_array),1);
		if($rID) $flag=1; else $flag=0;
		if($flag==1) 
		{
			if($rID) $flag=1; else $flag=0; 
		} 
		
		if($data_array!="") 
		{
			$rID=sql_insert("lib_buyer_brand",$field_array,$data_array,1); 
			if($flag==1) 
			{
				if($rID) $flag=1; else $flag=0; 
			} 
		}
		
		//----------------------------------------------------------------------------------
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);   
				echo "1**".$rID;
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
	else if ($operation==2)//Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		echo '14**Delete Not Allow';disconnect($con); die;
		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("lib_buyer_brand",$field_array,$data_array,"id","".$update_id."",1);

		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "2**".$rID;
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
				echo "2**".$rID;
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
?>