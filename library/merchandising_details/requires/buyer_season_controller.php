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
	//echo "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active=1 and is_deleted=0 order by id";
	$nameArray= sql_select("SELECT id, season_name from lib_buyer_season where buyer_id='$data' and status_active=1 and is_deleted=0 order by id");
	// echo "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active=1 and is_deleted=0 order by id";
	?>
	<fieldset>
        <legend>New Season Entry</legend>
        <div onLoad="set_hotkey();" style="width:400px;height:auto;" align="center">
        <table width="400px" cellpadding="0" cellspacing="0" border="1" class="rpt_table" align="center" id="season_tbl" rules="all" >
            <thead>
                <th width="40">SL No</th>
                <th>Season Name</th>
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
                            <tr id="trSeason_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td>
                                	<input type="text" name="txtSeasonName_<? echo $i;?>" id="txtSeasonName_<? echo $i;?>" class="text_boxes" style="width:300px;" value=" <? echo $row[csf('season_name')];?>" onBlur="append_seasonName_row(this.value,<? echo $i;?>);" />

                                	<input type="hidden" name="updateid_<? echo $i;?>" id="updateid_<? echo $i;?>" style="width:20px;" class="text_boxes"  value="<? echo $row[csf('id')];?> " />
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
                        <tr id="trSeason_1">
                            <td width="40">1</td>
                            <td> 
                                <input type="text"name="txtSeasonName_1" id="txtSeasonName_1" style="width:300px;" class="text_boxes" onBlur="append_seasonName_row(this.value,1);" />
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
                    	echo load_submit_buttons( $permission, "fnc_buyer_season", $is_update,0 ,"reset_form('buyerseason_1','buyer_season_name','')",1);
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
	$cbo_buyer_id = $cbocompone_1;
	if ($operation==0)//Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		// if(is_duplicate_field( "country_name", " lib_country", "season_name=LOWER($txt_country_name) and is_deleted=0 " ) == 1)
		// {
		// 	echo "11**0"; die;
		// }
		
		$id = return_next_id( "id", "lib_buyer_season", 1 );
		$field_array="id, buyer_id, season_name, inserted_by, insert_date";
		$add_comma=0;
		$return_id="";
		for($i=1; $i<=$row_num; $i++)
		{
			$updateid="updateid_".$i;
			$txtSeasonName="txtSeasonName_".$i;

			$season=str_replace("'","",$$txtSeasonName);
			$check=sql_select( "select season_name from lib_buyer_season where season_name='$season' and buyer_id=$cbo_buyer_id and status_active =1 and is_deleted=0");
			if(count($check)>0)
			{
				echo "11**0"; die;
			}
			
			if(str_replace("'","",$$txtSeasonName)!='')
			{
				if ($add_comma!=0) $data_array .=",";
				$data_array.="(".$id.",".$cbo_buyer_id.",".$$txtSeasonName.",'".$user_id."','".$pc_date_time."')"; 
				$id=$id+1;
				$add_comma++;
			}
		}
		//echo $return_id;
		// echo "15**".$data_array;die;
		// echo "INSERT INTO lib_buyer_season(".$field_array.") VALUES ".$data_array;die;
		$rID=sql_insert("lib_buyer_season",$field_array,$data_array,1);
		
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
		// echo "Hello= ". $cbo_buyer_id; die;
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id = return_next_id( "id", "lib_buyer_season", 1 );
		$field_array="id, buyer_id, season_name, inserted_by, insert_date";
		$field_array_up="season_name*updated_by*update_date";
		
		$add_comma=0;
		$return_id="";
		for($i=1; $i<=$row_num; $i++)
		{
			$updateid="updateid_".$i;
			$txtSeasonName="txtSeasonName_".$i;
			
			if(str_replace("'","",$$updateid)=="")
			{
				if(str_replace("'","",$$txtSeasonName)!='')
				{
				    $season=str_replace("'","",$$txtSeasonName);
					
					$check=sql_select( "select season_name from lib_buyer_season where season_name='$season' and buyer_id=$cbo_buyer_id and status_active =1 and is_deleted=0");
					if(count($check)>0)
					{
						echo "11**0"; die;
					}

					if ($add_comma!=0) $data_array .=",";
					$data_array.="(".$id.",".$cbo_buyer_id.",".$$txtSeasonName.",'".$user_id."','".$pc_date_time."')"; 
					$id=$id+1;
					$add_comma++;
				}
			}
			else
			{
				if(str_replace("'","",$$txtSeasonName)!='')
				{
					
					$updateID_array[]=str_replace("'",'',$$updateid); 
					$data_array_up[str_replace("'",'',$$updateid)]=explode("*",("".$$txtSeasonName."*'".$user_id."'*'".$pc_date_time."'"));
				}
			}
			
		}
		
		$rID=execute_query(bulk_update_sql_statement("lib_buyer_season","id",$field_array_up,$data_array_up,$updateID_array),1);
		if($rID) $flag=1; else $flag=0;
		if($flag==1) 
		{
			if($rID) $flag=1; else $flag=0; 
		} 
		
		if($data_array!="") 
		{
			$rID=sql_insert("lib_buyer_season",$field_array,$data_array,1); 
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
		$rID=sql_delete("lib_buyer_season",$field_array,$data_array,"id","".$update_id."",1);

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

if($action=="composition_popup")
{
	echo load_html_head_contents("Order Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	// echo "<pre>"; print_r($composition); die;
	
	?>
	<script>
		function js_set_value(id,name)
		{
			document.getElementById('hidcompid').value=id;
			document.getElementById('hidcompname').value=name;
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
		<fieldset style="width:430px;margin-left:10px">
	        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="430" class="rpt_table">
	                <thead>
	                    <th width="30">SL</th>
	                    <th>Buyer Name
                        	<input type="hidden" name="hidcompid" id="hidcompid" value="" style="width:50px">
                            <input type="hidden" name="hidcompname" id="hidcompname" value="" style="width:50px">
                        </th>
	                </thead>
                    </table>
                    <table cellpadding="0" cellspacing="0" border="1" rules="all" width="430" class="rpt_table" id="comp_tbl">
                    <tbody>

                    <?
					$sql = sql_select("SELECT ID, BUYER_NAME FROM LIB_BUYER WHERE STATUS_ACTIVE =1 AND IS_DELETED=0 ORDER BY BUYER_NAME ASC");
					// echo "<pre>"; print_r($sql); die;
                    $i=1; 
					foreach($sql as $row)
					{ 
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; ?>
                    	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $row['ID']; ?>,'<? echo $row['BUYER_NAME']; ?>')">
                            <td width="30"><? echo $i; ?></td>
                            <td><? echo $row['BUYER_NAME']; ?> </td>
                        </tr>
                    <? $i++; } ?>
                    </tbody>
	            </table>
	            <div id="search_div" style="margin-top:5px"></div>   
	        </form>
	    </fieldset>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>setFilterGrid('comp_tbl',-1);</script>
	</html>
	<?
	exit();
}
?>