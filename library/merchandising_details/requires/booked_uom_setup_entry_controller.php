<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$permission=$_SESSION['page_permission'];

if($action=="load_drop_down_subsection")
{
 
	$data=explode('_',$data);
	if($data[0]==1) $subID='1,2,3,23,29,30,31,32,33,34,35,36,37,38,39,40';
	else if($data[0]==3) $subID='4,5,18';
	else if($data[0]==5) $subID='6,7,8,9,10,11,12,13,16,17,17,24';
	else if($data[0]==10) $subID='14,15';
	else if($data[0]==7) $subID='19,20,21,25,26,27,28,41';
	else if($data[0]==9) $subID='22';
	else $subID='0';
	//echo $data[0]."**".$data[1];
	//echo create_drop_down( "cboembtype_".$data[1], 80,$emb_type,"", 1, "-- Select --", "", "","","" ); 
	echo create_drop_down( "cboSubSection_".$data[1], 150, $trims_sub_section,"",1, "-- Select Sub-Section --","","",0,$subID,'','','','','',"cboSubSection[]");
	exit();
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
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		
		$id=return_next_id( "id", "lib_booked_uom_setup", 1 );
		$field_array= "id,company_id,section_id,sub_section_id,uom_id,inserted_by,insert_date,status_active,is_deleted";
		$item_dup_chk_arr=array();
		for($i=1; $i<=$total_row; $i++)
		{			
			$cboSection				= "cboSection_".$i;
			$cboSubSection			= "cboSubSection_".$i;
			$cboUom 				= "cboUom_".$i;
			$item=str_replace("'","",$$cboSection)."_".str_replace("'","",$$cboSubSection);

			if(!in_array($item, $item_dup_chk_arr, true))
			{
        		array_push( $item_dup_chk_arr, $item);
    		}
    		else
    		{
    			echo "26**"; disconnect($con); die;
    		}
			
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$cbo_company_name.",".$$cboSection.",".$$cboSubSection.",".$$cboUom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
			$id++;
		}
		//echo "10**INSERT INTO lib_booked_uom_setup(".$field_array.") VALUES ".$data_array;die;
		$rID=sql_insert("lib_booked_uom_setup",$field_array,$data_array,0);
		//check_table_status( $_SESSION['menu_id'],0);
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
		else if($db_type==2 || $db_type==1 )
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
		
		$id=return_next_id( "id", "lib_booked_uom_setup", 1 );
		$field_array_update="section_id*sub_section_id*uom_id*updated_by*update_date";
		$field_array= "id,company_id,section_id,sub_section_id,uom_id,inserted_by,insert_date,status_active,is_deleted";
		$item_dup_chk_arr=array();
		for($i=1; $i<=$total_row; $i++)
		{			
			$cboSection				= "cboSection_".$i;
			$cboSubSection			= "cboSubSection_".$i;
			$cboUom 				= "cboUom_".$i;
			$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;

			$item=str_replace("'","",$$cboSection)."_".str_replace("'","",$$cboSubSection);

			if(!in_array($item, $item_dup_chk_arr, true))
			{
        		array_push( $item_dup_chk_arr, $item);
    		}
    		else
    		{
    			echo "26**"; disconnect($con); die;
    		}
			
			if(str_replace("'","",$$hdnDtlsUpdateId)!="")
			{
				if(str_replace("'",'',$$hideDoChk) !=1)
				{
					$id_arr[]=str_replace("'",'',$$hdnDtlsUpdateId);
					$data_array_update[str_replace("'",'',$$hdnDtlsUpdateId)] = explode("*",("'".str_replace("'","",$$cboSection)."'*'".str_replace("'","",$$cboSubSection)."'*'".str_replace("'","",$$cboUom)."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				}
			}
			else
			{
				if ($i!=1) $data_array .=",";
				$data_array .="(".$id.",".$cbo_company_name.",".$$cboSection.",".$$cboSubSection.",".$$cboUom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
				$id=$id+1;
			}
			
		}
		$rID=true; $rID2=true; $rID3=true; unset($item_dup_chk_arr);
		if(count($data_array_update)>0)
		{
			//echo "10**".bulk_update_sql_statement( "lib_booked_uom_setup", "id", $field_array_update, $data_array_update, $id_arr ); die;
			$rID=execute_query(bulk_update_sql_statement( "lib_booked_uom_setup", "id", $field_array_update, $data_array_update, $id_arr ));
		}

		if($data_array!="")
		{
			$rID2=sql_insert("lib_booked_uom_setup",$field_array,$data_array,0);
		}

		if($txt_deleted_id!="")
		{
			$field_array_status="updated_by*update_date*status_active*is_deleted";
			$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";

			$rID3=sql_multirow_update("lib_booked_uom_setup",$field_array_status,$data_array_status,"id",$txt_deleted_id,0);
		}

		//echo "10**".$rID."**".$rID2."**".$rID3; die;
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3){
				mysql_query("COMMIT");  
				echo "1**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			
			if($rID && $rID2 && $rID3)
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
		
		//}
		
	}
	else if ($operation==2) // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$user_id=$_SESSION['logic_erp']['user_id'];
		$rID=execute_query("update lib_booked_uom_setup set status_active=0 , is_deleted=1 , updated_by=$user_id, update_date='$pc_date_time' where company_id=$cbo_company_name  ");
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
		else if($db_type==2 || $db_type==1 )
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

if( $action=='dtls_list_view' ) 
{
	extract($_REQUEST);
	//echo $data; die;2_FAL-TOR-19-00092_1_3895
	//$data=explode('_',$data);

	$sql = "SELECT id,section_id,sub_section_id,uom_id from lib_booked_uom_setup where company_id=$data and status_active=1 and is_deleted=0 order by id ASC";

	$data_array=sql_select($sql); $tblRow=0;
	
	if(count($data_array) > 0)
	{
		foreach ($data_array as $row) 
		{
			 
			$tblRow++;
			if($row[csf('section_id')]==1) $subID='1,2,3,23,29,30,31,32,33,34,35,36,37,38,39,40';
			else if($row[csf('section_id')]==3) $subID='4,5,18';
			else if($row[csf('section_id')]==5) $subID='6,7,8,9,10,11,12,13,16,17,17,24';
			else if($row[csf('section_id')]==10) $subID='14,15';
			else if($row[csf('section_id')]==7) $subID='19,20,21,25,26,27,28,41';
			else if($row[csf('section_id')]==9) $subID='22';
			else $subID='0';

			?>
			<tr id="row_<? echo $tblRow; ?>" align="center">
	            <td width="150"><? echo create_drop_down( "cboSection_".$tblRow,150, $trims_section,"", 1, "-- Select Section --",$row[csf('section_id')],"load_sub_section($tblRow)",0,'','','','','','',"cboSection[]"); ?>
	            </td>
	            <td id="subSectionTd_<? echo $tblRow; ?>"><? echo create_drop_down( "cboSubSection_".$tblRow, 150, $trims_sub_section,"", 1, "-- Select Sub-Section --",$row[csf('sub_section_id')],'',0,$subID,'','','','','',"cboSubSection[]"); ?></td>
	            <td><? echo create_drop_down( "cboUom_".$tblRow, 100, $unit_of_measurement,"", 1, "-- Select --",$row[csf('uom_id')],1, 0,'','','','','','',"cboUom[]"); ?></td>
	            <td> 
	               	<input type="button" id="increase_<? echo $tblRow; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(<? echo $tblRow; ?>,'tbl_dtls_emb','row_')" />
					<input type="button" id="decrease_<? echo $tblRow; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(<? echo $tblRow; ?>,'tbl_dtls_emb','row_');" />
					<input id="hdnDtlsUpdateId_<? echo $tblRow; ?>" name="hdnDtlsUpdateId[]" type="hidden" class="text_boxes_numeric" style="width:40px" value="<? echo $row[csf('id')]; ?> "; />
	                <!-- <input type="hidden" id="updateid_1" name="updateid_1"  class="text_boxes" style="width:20px" value=""  />  --> 
	            </td>  
	        </tr>
			<?
		}
	}
	else
	{
		?>
		<tr id="row_1" align="center">
            <td width="150"><? echo create_drop_down( "cboSection_1",150, $trims_section,"", 1, "-- Select Section --","","load_sub_section(1)",0,'','','','','','',"cboSection[]"); ?>
            </td>
            <td id="subSectionTd_1"><? echo create_drop_down( "cboSubSection_1", 150, $trims_sub_section,"", 1, "-- Select Sub-Section --","",'',0,'','','','','','',"cboSubSection[]"); ?></td>
            <td><? echo create_drop_down( "cboUom_1", 100, $unit_of_measurement,"", 1, "-- Select --",'0',1, 0,'','','','','','',"cboUom[]"); ?></td>
            <td> 
               	<input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(1,'tbl_dtls_emb','row_')" />
				<input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(1,'tbl_dtls_emb','row_');" />
				<input id="hdnDtlsUpdateId_1" name="hdnDtlsUpdateId[]" type="hidden" class="text_boxes_numeric" style="width:40px" />
                <!-- <input type="hidden" id="updateid_1" name="updateid_1"  class="text_boxes" style="width:20px" value=""  />  --> 
            </td>  
        </tr>
		<?
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
                    <? $i=1; foreach($composition as $id=>$comp_name) { if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; ?>
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
	
if($action=="check_yarn_count_determination")
{
	//$data=explode("**",$data);
	$price_sql="select lib_yarn_count_deter_id as detarmination_id from wo_pri_quo_fabric_cost_dtls a where  a.lib_yarn_count_deter_id=$data";
	$price_data_array=sql_select($price_sql);

	$pre_sql="select lib_yarn_count_deter_id as detarmination_id from wo_pre_cost_fabric_cost_dtls a where  a.lib_yarn_count_deter_id=$data";
	$pre_data_array=sql_select($pre_sql);
	 
	 $sql="select a.detarmination_id as detarmination_id from product_details_master a where   a.detarmination_id=$data and a.is_deleted=0 and a.status_active=1"; 
	
	$data_array=sql_select($sql);
	if(count($data_array)>0 || count($pre_data_array)>0 || count($price_data_array)>0)
	{
		echo "1_";
	}
	else
	{
		echo "0_";
	}
	exit();	
}
?>