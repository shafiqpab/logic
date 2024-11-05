<?
/****************************************************************
|	Purpose			:	This Form Will Create Mandatory Field
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Md. Saidul Islam REZA
|	Creation date 	:	29-05-2019
|	Updated by 		:   	
|	Update date		:    
|	QC Performed BY	:		
|	QC Date			:	
|	Comments		:
******************************************************************/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
include('../../includes/mandatory_field_list_array.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
extract($_REQUEST);

if($action=='action_user_data')
{
	$array=sql_select("select id, page_id, field_id, field_name, is_mandatory from mandatory_field where  page_id=$data and status_active=1 and is_deleted=0");
	$i=0;
	if(count($array)>0)
	{
		foreach($array as $row)
		{
			if($i==0)
				$str=$row[csf("id")]."*".$row[csf("page_id")]."*".$row[csf("field_id")]."*".$row[csf("field_name")]."*".$row[csf("is_mandatory")];
			else
				$str .="@@".$row[csf("id")]."*".$row[csf("page_id")]."*".$row[csf("field_id")]."*".$row[csf("field_name")]."*".$row[csf("is_mandatory")];
			$i++;
		}
	}	 
	echo "$('#txt_update_data_dtls').val('".$str."');\n";
	die;	
	exit();
}

if ($action=='pagename_popup')
{
	echo load_html_head_contents("Page Name popup", "../../", 1, 1,'','','');
	?>
    <script>
    function js_set_value(entry_form,entry_page)
    {
	    document.getElementById('entry_form_id').value=entry_form;
	    document.getElementById('entry_form_name').value=entry_page;
	    parent.emailwindow.hide();
    }

    </script>
    </head>
    <body>
        <table cellspacing="0" width="370"  border="1" rules="all" class="rpt_table" align="left" id="table_header">
            <thead>
                <tr>
                    <th width="50">SL</th>
                    <th width="320">Page Name</th>                 
                </tr>
            </thead>
        </table>     
        <div style="width:390px; max-height: 360px; overflow-y: scroll;" align="left" id="scroll_body">
            <table cellspacing="0" width="370"  border="1" rules="all" align="left" class="rpt_table" id="table_body">
                <?
				// var_dump($entry_form);
                $i=1;
	            foreach($entry_form as $key => $page_name)
				{
					if ($i%2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					?>
                    <tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $key; ?>','<? echo $page_name; ?>')">
                        <td width="50"><? echo $i; ?></td>
                        <td width="320"><p><? echo $page_name; ?></p></td>
					</tr>
					<?
					$i++;
				}	
				?>
            </table>
            <input type="hidden" id="entry_form_id"/>
            <input type="hidden" id="entry_form_name"/>
        </div>
    </body>
    <script>setFilterGrid('table_body',-1);</script>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}



if($action=="load_drop_down_item")
{
	// print_r($fieldlevel_arr[10000]);
	// echo $data;die;
	$field_arr=get_fieldlevel_arr($data);
	
 
    if($data == 270){ // Only for Export Invoice
        $field_arr_mod = array();
        foreach ($field_arr as $key => $val){
            if(strtolower(trim($val)) == 'mode'){
                $field_arr_mod[$key] = "Shipping Mode";
            }elseif(strtolower(trim($val)) == 'factory date'){
                $field_arr_mod[$key] = "Ex-Factory Date";
            }else{
                $field_arr_mod[$key] = $val;
            }
        }
        $field_arr = $field_arr_mod;
    }
	echo create_drop_down( "cboFieldId_1",200,$field_arr,"",1,"----Select----",0,"","","","","","","","","cbo_field_id[]" );
	exit();
}




if($action=='save_update_delete')
{
	$process=array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	//insert
	if($operation==0)
	{
		$con = connect();
		$id=return_next_id( "id", "mandatory_field", 1 ) ; 
				
        $duplicate_sql = "select id from mandatory_field where page_id=$cbo_page_id and status_active=1"; 
        $duplicate_result = sql_select($duplicate_sql); 
        if(count($duplicate_result)>0)
        {			
            $rID = execute_query("delete from mandatory_field where page_id=$cbo_page_id",1);	
        }
        
        
		
			$field_array="id,page_id,field_id,field_name,field_message,is_mandatory,inserted_by,insert_date";
			
            $data_array="";$add_comm=0;
			for($i=1;$i<=$total_row;$i++)
			{             
				$cboFieldId="cboFieldId_".$i;
				$txtFieldName=$fieldlevel_arr[str_replace("'","",$cbo_page_id)][str_replace("'","",$$cboFieldId)];
				$txtFieldMessage=ucwords(str_replace(array('cbo','txt','_'),array("",""," "),$fieldlevel_arr[str_replace("'","",$cbo_page_id)][str_replace("'","",$$cboFieldId)]));
				$cboIsMandatory="cboIsMandatory_".$i;
				
				//if ($i!=1) $data_array .=",";
					if ($add_comm!=0) $data_array .=",";
				$data_array	.="(".$id.",".$cbo_page_id.",".$$cboFieldId.",'".$txtFieldName."','".trim($txtFieldMessage)."',".$$cboIsMandatory.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";						
				$id++;$add_comm++;
			}          
		
       	
		if ($data_array	!='') {
            $rID = sql_insert("mandatory_field",$field_array,$data_array,1);
        }
		else {
             echo "20**Duplicate Field Name Not Allow in The Same Page.";
             die;
		}  
		
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'","",$cbo_page_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$cbo_page_id);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);   
				echo "0**".str_replace("'","",$cbo_page_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$cbo_page_id);
			}
		}
		disconnect($con);
		die;		
	}
		//update
	if($operation==1)
	{
		$con = connect();
		$id=return_next_id( "id", "mandatory_field", 1 ) ; 
				
        $duplicate_sql = "select id from mandatory_field where page_id=$cbo_page_id and status_active=1"; 
        $duplicate_result = sql_select($duplicate_sql); 
        if(count($duplicate_result)>0)
        {			
            $rID = execute_query("delete from mandatory_field where page_id=$cbo_page_id",1);	
        }
        
        
		
			$field_array="id,page_id,field_id,field_name,field_message,is_mandatory,updated_by,update_date";
			
            $data_array="";$add_comm=0;
			for($i=1;$i<=$total_row;$i++)
			{             
				$cboFieldId="cboFieldId_".$i;
				$txtFieldName=$fieldlevel_arr[str_replace("'","",$cbo_page_id)][str_replace("'","",$$cboFieldId)];
				$txtFieldMessage=ucwords(str_replace(array('cbo','txt','_'),array("",""," "),$fieldlevel_arr[str_replace("'","",$cbo_page_id)][str_replace("'","",$$cboFieldId)]));
				$cboIsMandatory="cboIsMandatory_".$i;
				
				//if ($i!=1) $data_array .=",";
				if ($add_comm!=0) $data_array .=",";
				$data_array	.="(".$id.",".$cbo_page_id.",".$$cboFieldId.",'".$txtFieldName."','".trim($txtFieldMessage)."',".$$cboIsMandatory.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";						
				$id++;$add_comm++;
			}          
		
       //	echo "10**insert into mandatory_field (".$field_array.") values ".$data_array;die;
		
		if ($data_array	!='') {
            $rID = sql_insert("mandatory_field",$field_array,$data_array,1);
        }
		else {
             echo "20**Duplicate Field Name Not Allow in The Same Page.";
             die;
		}  
		
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$cbo_page_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$cbo_page_id);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);   
				echo "1**".str_replace("'","",$cbo_page_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$cbo_page_id);
			}
		}
		disconnect($con);
		die;		
	}
	
	//delete
	if($operation==2)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$rID = execute_query("delete from mandatory_field where page_id = $cbo_page_id",1);		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "2**".$cbo_user_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$cbo_user_id;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);   
				echo "2**".$cbo_user_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$cbo_user_id;
			}
		}
		disconnect($con);
		die;	
	}
}



?>