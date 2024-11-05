<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
if ($action=="search_list_view")
{
	$lib_sup=return_library_array("select supplier_name,id from lib_supplier", "id", "supplier_name");
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$sql="select id,supplier_id,yarn_count,composition,percent,yarn_type,rate,effective_date from lib_yarn_rate where status_active=1 and is_deleted=0 order by id";
	$arr=array (0=>$lib_sup,1=>$lib_yarn_count,2=>$composition,4=>$yarn_type);
	echo  create_list_view ( "list_view", "Supplier Name,Yarn Count,Composition,Percent,Type,Rate/KG,Effective Date", "250,100,300,40,110,50,70","1080","350",0, $sql, "get_php_form_data", "id", "'load_php_data_to_form'",1, "supplier_id,yarn_count,composition,0,yarn_type,0,0", $arr , "supplier_id,yarn_count,composition,percent,yarn_type,rate,effective_date", "../merchandising_details/requires/yarn_rate_controller",'setFilterGrid("list_view",-1);','0,0,0,1,0,2,3') ;
}
    
if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id,supplier_id,yarn_count,composition,percent,yarn_type,rate,effective_date from lib_yarn_rate where id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('cbo_supplier').value  = '".$inf[csf("supplier_id")]."';\n";
		echo "document.getElementById('cbocountcotton').value = '".$inf[csf("yarn_count")]."';\n";    
		echo "document.getElementById('cbocompone').value = '".$inf[csf("composition")]."';\n";

		echo "document.getElementById('txtcompone').value  = '".$composition[$inf[csf("composition")]]."';\n";
		echo "document.getElementById('percentone').value = '".$inf[csf("percent")]."';\n";   
		echo "document.getElementById('cbotypecotton').value  = '".$inf[csf("yarn_type")]."';\n";
		echo "document.getElementById('txt_rate').value  = '".$inf[csf("rate")]."';\n";
		echo "document.getElementById('txt_date').value  = '".change_date_format($inf[csf("effective_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('update_id').value  = '".$inf[csf("id")]."';\n";
	    echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_yarn_rate',1);\n";  
	}
}
if ($action=="save_update_delete")
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
			$id=return_next_id( "id", "lib_yarn_rate", 1 ) ;
			$field_array1= "id,supplier_id,yarn_count,composition,percent,yarn_type,rate,effective_date,inserted_by,insert_date,status_active,is_deleted";
			$data_array1="(".$id.",".$cbo_supplier.",".$cbocountcotton.",".$cbocompone.",".$percentone.",".$cbotypecotton.",".$txt_rate.",".$txt_date.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
			$rID=sql_insert("lib_yarn_rate",$field_array1,$data_array1,0);
			if($db_type==0)
			{
				if($rID){
					mysql_query("COMMIT");  
					echo "0**".$rID;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			
			if($db_type==2 || $db_type==1 )
			{
				if($rID)
				{
					oci_commit($con);  
					echo "0**".$rID;
				}
			else{
					oci_rollback($con); 
					echo "10**".$rID;
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
			$field_array1= "supplier_id*yarn_count*composition*percent*yarn_type*rate*effective_date*updated_by*update_date*status_active*is_deleted";
			$data_array1="".$cbo_supplier."*".$cbocountcotton."*".$cbocompone."*".$percentone."*".$cbotypecotton."*".$txt_rate."*".$txt_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'1'*'0'";
			 $rID=sql_update("lib_yarn_rate",$field_array1,$data_array1,"id","".$update_id."",0);
			
			if($db_type==0)
			{
				if($rID){
					mysql_query("COMMIT");  
					echo "1**".$rID;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			if($db_type==2 || $db_type==1 )
			{
				
				if($rID)
				{
					oci_commit($con);  
					echo "1**".$rID;
				}
				else{
					oci_rollback($con); 
					echo "10**".$rID;
				}
			}
			disconnect($con);
			die;
	}
	
	else if ($operation==2) // Delete Here
	{
		
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$field_array1="updated_by*update_date*status_active*is_deleted";
			$data_array1="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
			$rID=sql_delete("lib_yarn_rate",$field_array1,$data_array1,"id","".$update_id."",1);
			
			if($db_type==0)
			{
				if($rID){
					mysql_query("COMMIT");  
					echo "2**".$rID;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			if($db_type==2 || $db_type==1 )
			{
			 if($rID)
				{
					oci_commit($con);   
					echo "2**".$rID;
				}
				else{
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
	                    <th>Composition
                        	<input type="hidden" name="hidcompid" id="hidcompid" value="" style="width:50px">
                            <input type="hidden" name="hidcompname" id="hidcompname" value="" style="width:50px">
                        </th>
	                </thead>
                    </table>
                    <table cellpadding="0" cellspacing="0" border="1" rules="all" width="430" class="rpt_table" id="comp_tbl">
                    <tbody>

                    <? 
                    $i=1; foreach($composition as $id=>$comp_name) { if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; ?>
                    	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $id; ?>,'<? echo $comp_name; ?>')">
                            <td width="30"><? echo $i; ?></td>
                            <td><? echo $comp_name; ?> </td> 						
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