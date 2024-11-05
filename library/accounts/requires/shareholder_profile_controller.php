<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="shareholder_profile")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
?>
<script>

function change_caption( value, td_id )
{
	if(value==2)
	{
		document.getElementById(td_id).innerHTML="Enter Name ";
	}
	else if(value==3)
	{
		document.getElementById(td_id).innerHTML="Enter TIN";
	}
	else 
	{
		document.getElementById(td_id).innerHTML="Enter Id No";
	}
}

function js_set_value(id)
	{
		$("#hidden_mst_id").val(id);
   		parent.emailwindow.hide();
 	}

</script>
</head>
<body>	
<div align="center" style="width:100%;" >

<form name="searchorder_1"  id="searchorder_1"  autocomplete="off">
<fieldset style="width:750px;">
            <legend>Enter search words </legend>
            	<table cellpadding="0" cellspacing="0" width="550" class="rpt_table" align="center" id="">
                	<thead>
                    	<th width="200">Search By</th>
                        <th width="190" id="search_by_th_up">Search Text</th>
                        <th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" /></th>
                     </thead>
                     <tr class="general">
                        <td>
                        <?
						$share_source =array(1=>"Id No",2=>"Name",3=>"TIN");
						echo create_drop_down( "cbo_search_by", 100, $share_source,"", 1, "-- Select Source --", $selected, "change_caption( this.value, 'search_by_th_up' );" );
						?>
                        </td>
                        <td id="search_by_td">
						<input type="text" style="width:90%" class="text_boxes"  name="txt_search_text" id="txt_search_text" />
            			</td>
                        <td>
                        <input type="hidden" id="hidden_mst_id">
                        <input type="hidden" name="id_field" id="id_field" value="" />
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('txt_search_text').value+'_'+document.getElementById('cbo_search_by').value,'shareholder_list','shareholdersearch_div','shareholder_profile_controller','setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
                 		</td>
					</tr>
                </table>
                <table id="" width="100%">
					<tr>
                    	<td colspan="9">
                        	<div style="width:750px; margin-top:10px" id="shareholdersearch_div" align="left"></div>
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
}



if($action=="shareholder_list")
{
	
							 
$sql_data =sql_select("select id, id_number, name, bo_account_id, national_id, tin  from ac_lib_shareholder_profile where status_active=1 and is_deleted=0 $sql_cond");

?>
<div>
	<div style="width:750px;" align="left">
        <table cellspacing="0" cellpadding="0" width="100%" class="rpt_table" >
            <thead>
                  <th width="40">SL No</th> 	 	 	 	
                  <th width="120">Id No</th> 	
                  <th width="170">Name</th>
                  <th width="120">BO A/C Id</th>
                  <th width="100">TIN</th>
                  <th>National Id</th>
            </thead>
        </table>
	</div>	
    <div style="width:750px; overflow-y:scroll; min-height:50px; max-height:250px;" id="sales_contact" align="left">
	  <table id="tbl_po_list" cellspacing="0" cellpadding="0" width="100%" class="rpt_table" >
<?
		$i=0;
		foreach($sql_data	as $row)
		    {
				 $i++;	$i++;
			if ($i%2==0)  
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
					
	 	
?>
            <tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')];?>);" >
		       <td width="40"><? echo $i; ?></td>
               <td width="120"><? echo  $row[csf('id_number')];  ?></td> 	 	 	 	 		
               <td width="170"><? echo $row[csf('name')]; ?></td>
               <td width="120"><? echo $row[csf('bo_account_id')]; ?></td>
               <td width="100"><? echo $row[csf('tin')];?></td>
               <td width=""><? echo $row[csf('national_id')]; ?></td>
			</tr>	
		<?
       }  
       ?>
		</table>
    </div>		
<?	

}


if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select  id, id_number, name, bo_account_id, father_name, mother_name, profession, organization, designation, national_id, tin,vat, email,phone,present_plot_no,present_level_no,present_road_no,present_block,present_country,present_province,present_city,present_zip_code,permanent_plot_no,permanent_level_no,permanent_road_no,permanent_block,permanent_country,permanent_province,permanent_city,permanent_zip_code from ac_lib_shareholder_profile where status_active=1 and is_deleted=0 and id='$data'" );
	
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('txt_id_no').value = '".($inf[csf("id_number")])."';\n";    
		//echo "document.getElementById('txt_id').value  = '".($inf[csf("id")])."';\n"; 
		echo "document.getElementById('txt_name').value = '".($inf[csf("name")])."';\n";    
		echo "document.getElementById('txt_bo_ac_id').value  = '".($inf[csf("bo_account_id")])."';\n"; 
		echo "document.getElementById('txt_father_name').value = '".($inf[csf("father_name")])."';\n";    
		echo "document.getElementById('txt_mother_name').value  = '".($inf[csf("mother_name")])."';\n"; 
		echo "document.getElementById('txt_profession').value = '".($inf[csf("profession")])."';\n"; 
		echo "document.getElementById('txt_organization').value  = '".($inf[csf("organization")])."';\n";
		echo "document.getElementById('txt_designation').value  = '".($inf[csf("designation")])."';\n";
		echo "document.getElementById('txt_national_id').value  = '".($inf[csf("national_id")])."';\n";
		echo "document.getElementById('txt_tin').value  = '".($inf[csf("tin")])."';\n";
		echo "document.getElementById('txt_vat').value  = '".($inf[csf("vat")])."';\n";
		echo "document.getElementById('txt_email').value  = '".($inf[csf("email")])."';\n";
		echo "document.getElementById('txt_phone').value  = '".($inf[csf("phone")])."';\n";
		
		echo "document.getElementById('txt_present_plot_no').value = '".($inf[csf("present_plot_no")])."';\n";    
		echo "document.getElementById('txt_present_level_no').value  = '".($inf[csf("present_level_no")])."';\n"; 
		echo "document.getElementById('txt_present_road_no').value = '".($inf[csf("present_road_no")])."';\n";    
		echo "document.getElementById('txt_present_block_no').value  = '".($inf[csf("present_block")])."';\n"; 
		echo "document.getElementById('cbo_present_country').value = '".($inf[csf("present_country")])."';\n";    
		echo "document.getElementById('txt_present_province').value  = '".($inf[csf("present_province")])."';\n"; 
		echo "document.getElementById('cbo_present_state').value = '".($inf[csf("present_city")])."';\n"; 
		echo "document.getElementById('txt_present_zip_code').value  = '".($inf[csf("present_zip_code")])."';\n";
		
		echo "document.getElementById('txt_permanent_plot_no').value  = '".($inf[csf("permanent_plot_no")])."';\n";
		echo "document.getElementById('txt_permanent_level_no').value  = '".($inf[csf("permanent_level_no")])."';\n";
		echo "document.getElementById('txt_permanent_road_no').value  = '".($inf[csf("permanent_road_no")])."';\n";
		echo "document.getElementById('txt_permanent_block_no').value  = '".($inf[csf("permanent_block")])."';\n";
		echo "document.getElementById('cbo_permanent_country').value  = '".($inf[csf("permanent_country")])."';\n";
		echo "document.getElementById('txt_permanent_province').value  = '".($inf[csf("permanent_province")])."';\n";
		echo "document.getElementById('cbo_permanent_state').value  = '".($inf[csf("permanent_city")])."';\n";
		echo "document.getElementById('txt_permanent_zip_code').value  = '".($inf[csf("permanent_zip_code")])."';\n";
		
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_shareholder_profile',1);\n";  

	}
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // ===========================Insert Here==================================
	{
		if (is_duplicate_field( "id_number", "ac_lib_shareholder_profile", "id_number=$txt_id_no and bo_account_id = $txt_bo_ac_id " ) == 1)
		{
			echo "11**0"; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			 
			$id=return_next_id( "id", "ac_lib_shareholder_profile", 1 ) ; 
			 
			$field_array="id,id_number,name,bo_account_id,father_name,mother_name,profession,organization,designation,national_id,tin,vat,email,phone,present_plot_no,present_level_no,present_road_no,present_block,present_country,present_province,present_city,present_zip_code,permanent_plot_no,permanent_level_no,permanent_road_no,permanent_block,permanent_country,permanent_province,permanent_city,permanent_zip_code,inserted_by,insert_date,status_active,is_deleted"; 			 
			
			$data_array="(".$id.",".$txt_id_no.",".$txt_name.",".$txt_bo_ac_id.",".$txt_father_name.",".$txt_mother_name.",".$txt_profession.",".$txt_organization.",".$txt_designation.",".$txt_national_id.",".$txt_tin.",".$txt_vat.",".$txt_email.",".$txt_phone.",".$txt_present_plot_no.",".$txt_present_level_no.",".$txt_present_road_no.",".$txt_present_block_no.",".$cbo_present_country.",".$txt_present_province.",".$cbo_present_state.",".$txt_present_zip_code.",".$txt_permanent_plot_no.",".$txt_permanent_level_no.",".$txt_permanent_road_no.",".$txt_permanent_block_no.",".$cbo_permanent_country.",".$txt_permanent_province.",".$cbo_permanent_state.",".$txt_permanent_zip_code.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";
			
			$rID=sql_insert("ac_lib_shareholder_profile",$field_array,$data_array,0);
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "0**".$id;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$id;
				}
			}
			
			if($db_type==2 || $db_type==1 )
			{
		       if($rID )
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
	}
	
else if ($operation==1)// =============================Update Here=================================
	{
		/*if (is_duplicate_field( "id_number", "ac_lib_shareholder_profile", "id_number=$txt_id_no and bo_account_id = $txt_bo_ac_id " ) == 1)
		{
			echo "12**0"; die;
		}
		 
		else
		{*/
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			
			$field_array="id_number*name*bo_account_id*father_name*mother_name*profession*organization*designation*national_id*tin*vat*email*phone*present_plot_no*present_level_no*present_road_no*
present_block*present_country*present_province*present_city*present_zip_code*permanent_plot_no*permanent_level_no*permanent_road_no*permanent_block*permanent_country*
permanent_province*permanent_city*permanent_zip_code*updated_by*update_date*status_active*is_deleted";
			
			$data_array="".$txt_id_no."*".$txt_name."*".$txt_bo_ac_id."*".$txt_father_name."*".$txt_mother_name."*".$txt_profession."*".$txt_organization."*".$txt_designation."*".$txt_national_id."*".$txt_tin."*".$txt_vat."*".$txt_email."*".$txt_phone."*".$txt_present_plot_no."*".$txt_present_level_no."*".$txt_present_road_no."*".$txt_present_block_no."*".$cbo_present_country."*".$txt_present_province."*".$cbo_present_state."*".$txt_present_zip_code."*".$txt_permanent_plot_no."*".$txt_permanent_level_no."*".$txt_permanent_road_no."*".$txt_permanent_block_no."*".$cbo_permanent_country."*".$txt_permanent_province."*".$cbo_permanent_state."*".$txt_permanent_zip_code."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".'1'."*0";
			//echo $data_array; die;
			$rID=sql_update("ac_lib_shareholder_profile",$field_array,$data_array,"id","".$update_id."",1);
			
			if($db_type==0)
			{
				if($rID ){
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
			 if($rID )
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
	
else if ($operation==2)  //Delete here================================Delete here======================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*0*1";
		
		$rID=sql_delete("ac_lib_shareholder_profile",$field_array,$data_array,"id","".$update_id."",1);
		
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "2**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "7**".$rID;
			}
		}
		if($db_type==2 || $db_type==1 )
			{
			 if($rID )
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


if ($action=="save_update_delete_dtl")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here=======================================
	{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con); die;}
			$id=return_next_id( "id", "ac_lib_share_details",1); 
			$field_array="id,company_id,shareholder_profile_id,no_of_share,face_value,premium_value,inserted_by,insert_date,status_active,is_deleted"; 	
			for($i=1; $i<=$tot_row; $i++)
			{
				$cbo_company_share="cbocompanynameshare_".$i;
				$txt_share="txtnoofshare_".$i;
				$txt_face="txtfacevalue_".$i;
				$txt_premium="txtpremium_".$i;
				$txt_share_value="txtsharevalue_".$i;
				
				if ($i!=1) $data_array .=",";
				$data_array .="(".$id.",".$$cbo_company_share.",".$$txt_share_value.",".$$txt_share.",".$$txt_face.",".$$txt_premium.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				
				$id=$id+1;
			}
		
			$rID=sql_insert("ac_lib_share_details",$field_array,$data_array,0);
			
	/*================================end first insert============================================== */
				
			$id1=return_next_id( "id", "ac_lib_nominee_details", 1 ); 
			$field_array1="id,company_id,name,relation,ratio,amount,inserted_by,insert_date,status_active,is_deleted"; 
			for($i=1; $i<=$tot_row1; $i++)
			{
				$cbo_company_nominee="cbocompanynamenominee_".$i;
				$txt_name="txtnomineename_".$i;
				$txt_relation="txtnomineerelation_".$i;
				$txt_ratio="txtnomineeratio_".$i;
				$txt_amount="txtnomineeamount_".$i;
				
				if ($i!=1) $data_array1 .=",";
				$data_array1 .="(".$id1.",".$$cbo_company_nominee.",".$$txt_name.",".$$txt_relation.",".$$txt_ratio.",".$$txt_amount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$id1=$id1+1;
			}			 
			//echo "insert into ac_lib_share_details (".$field_array.") values ".$data_array;die;
			$rID1=sql_insert("ac_lib_nominee_details",$field_array1,$data_array1,1);
			//check_table_status( $_SESSION['menu_id'],0);
			
			if($db_type==0)
			{
				if($rID && $rID1 ){
					mysql_query("COMMIT");  
					echo "0**".$id;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$id;
				}
			}
			
			if($db_type==2 || $db_type==1 )
			{
				if($rID1 && $rID )
				   {
					echo $rID1."**".$id1;
				    }
			}
			disconnect($con);
			die;
	}
	
	/*else if ($operation==1)   // Update Here
	{
		if (is_duplicate_field( "id_number", "ac_lib_shareholder_profile", "id_number=$txt_id_no and bo_account_id = $txt_bo_ac_id " ) == 1)
		{
			echo "12**0"; die;
		}
		 
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			
			$field_array="id_number*name*bo_account_id*father_name*mother_name*profession*organization*designation*national_id*tin*vat*email*phone*present_plot_no*present_level_no*present_road_no*
present_block*present_country*present_province*present_city*present_zip_code*permanent_plot_no*permanent_level_no*permanent_road_no*permanent_block*permanent_country*
permanent_province*permanent_city*permanent_zip_code*inserted_by*insert_date*status_active*is_deleted";
			
			$data_array="".$txt_id_no."*".$txt_name."*".$txt_bo_ac_id."*".$txt_father_name."*".$txt_mother_name."*".$txt_profession."*".$txt_organization."*".$txt_designation."*".$txt_national_id."*".$txt_tin."*".$txt_vat."*".$txt_email."*".$txt_phone."*".$txt_present_plot_no."*".$txt_present_level_no."*".$txt_present_road_no."*".$txt_present_block_no."*".$cbo_present_country."*".$txt_present_province."*".$cbo_present_state."*".$txt_present_zip_code."*".$txt_permanent_plot_no."*".$txt_permanent_level_no."*".$txt_permanent_road_no."*".$txt_permanent_block_no."*".$cbo_permanent_country."*".$txt_permanent_province."*".$cbo_permanent_state."*".$txt_permanent_zip_code."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".'1'."*0";
			//echo $data_array; die;
			$rID=sql_update("ac_lib_shareholder_profile",$field_array,$data_array,"id","".$update_id."",1);
			
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "1**".$rID;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			disconnect($con);
			if($db_type==2 || $db_type==1 )
			{
			echo "1**".$rID;
			}
		}
		
	}
	*/
/*	else if ($operation==2)  //Delete here----------------------------------------------Delete here--------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$rID=sql_delete("lib_account_group",$field_array,$data_array,"id","".$update_id."",1);
		
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "1**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		disconnect($con);
		if($db_type==2 || $db_type==1 )
		{
	    echo "1**".$rID;
		}
		
	}*/
}



?>