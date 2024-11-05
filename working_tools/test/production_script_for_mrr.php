<?
header('Content-type:text/html; charset=utf-8');
session_start();

//include('../includes/common.php');

function connect() 
{
	//$con = odbc_connect("ORACLE_LINUX", "logicdb", "logicdb");
	//$con = oci_connect('FALORACLE', 'FALORACLE', 'TEST');
	//$con = oci_connect('LOGIC3RDVERSION', 'LOGIC3RDVERSION', 'TEST');
	$con = oci_connect('microerp', 'microerp', '192.168.1.204/microdb');

	//db2_autocommit($con, false);
	
	if(!$con)
	{
		trigger_error("Problem connecting to server");
	}	
	// oci_commit($con);
	/*$db = mssql_select_db('logic_erp', $con);
	if(!$db)
	{
		trigger_error("Problem selecting database");
	}	*/
	return $con;
}

function disconnect($con) 
{
	//$discdb = mssql_close($con);
	$discdb =oci_close($con);
	if(!$discdb)
	{
		trigger_error("Problem disconnecting database");
	}	
}

$con=connect();
function sql_select($strQuery, $is_single_row="", $new_conn="", $un_buffered="", $connection="")
{	
	if($connection==""){
		$con_select = connect();
	}else{
		$con_select = $connection;
	}
	//echo  $strQuery;
	$result = oci_parse($con_select, $strQuery);
	oci_execute($result);
	$rows = array();
	 while($summ=oci_fetch_assoc($result))
	 {
		if($is_single_row==1) 
		{
			$rows[] = $summ;
			if($connection=="") disconnect($con_select);
			return $rows;
			
			die;
		}
		else
		{
		$rows[] = $summ;
		}
	 }
	if($connection=="")  disconnect($con_select);
	return $rows;
	 //echo $row['mychars']->load(); for clob data type, mychars is clob
	die;
}

function execute_query( $strQuery, $commit="" )
{
	global $con ;
	$result =  oci_parse($con, $strQuery);
	$exestd=oci_execute($result,OCI_NO_AUTO_COMMIT);
	if ($exestd) 
		return "1";
	else 
		return "0";
	
	die;
	
	if ( $commit==1 )
	{
		if (!oci_error($result))
		{
			oci_commit($con); 
			return "0";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else return "1";
}

function csf($data)								// checked 3
{
	$db_type=2;
	if ($db_type==0 || $db_type==1 )  return strtolower($data); else return strtoupper($data);
}

function return_library_array( $query, $id_fld_name, $data_fld_name,$new_conn  )
{
	
	//global $new_conn;
	/*$query=explode("where", $query);
	$nameArray=sql_select( $query[0] );*/
	$nameArray=sql_select( $query, '', $new_conn);
	
	foreach ( $nameArray as $result )
	{
		$new_array[$result[csf($id_fld_name)]]=$result[csf($data_fld_name)];
	}
	return $new_array;
}


$all_company=return_library_array( "select id,id  from  lib_company where status_active=1 and is_deleted=0",'id','id',$con);
$tables=array(0=>"pro_gmts_cutting_qc_mst",1=>"pro_gmts_delivery_mst");
$production_type=array(2=>"issue",3=>"receive",4=>"input",5=>"output",7=>"iron",10=>"trans");
$embel_types=array(1=>"print",2=>"emb",3=>"wash",4=>"special");
$year=date("Y",time()); 
foreach($all_company as $k=>$val)
{
	foreach($tables as $k_table=>$table_val)
	{
		if($k_table==0)
		{
			$max_seq=sql_select("select max(cut_qc_prefix_no) as next_id from pro_gmts_cutting_qc_mst where company_id=$k  and to_char(insert_date,'YYYY')=$year");
	 	   $max_id=$max_seq[0][csf("next_id")];  
	 	    if(!$max_id) {$max_id="1";}
	      $insert=execute_query("INSERT into  platform_sequence_pk(table_name,next_id ,company_id,entry_form ,year,item_category_id,booking_type,production_type,emblishment_type,transfer_criteria) values('".strtoupper($table_val)."','$max_id','$k','0','$year','0','0','0','0','0')" );
		}
		else
		{
			foreach($production_type as $k_type=>$val)
			{
				if($k_type==4 || $k_type==5 ||  $k_type==7 ||  $k_type==10)
				{
					$max_seq=sql_select("select max(sys_number_prefix_num) as next_id from pro_gmts_delivery_mst where company_id=$k  and to_char(insert_date,'YYYY')=$year and production_type=$k_type");
	 	  			 $max_id=$max_seq[0][csf("next_id")];  
	 	  			 if(!$max_id) {$max_id="1";}
	      			$insert=execute_query("INSERT into  platform_sequence_pk(table_name,next_id ,company_id,entry_form ,year,item_category_id,booking_type,production_type,emblishment_type,transfer_criteria) values('".strtoupper($table_val)."','$max_id','$k','0','$year','0','0','$k_type','0','0')" );


				}
				if($k_type==2 || $k_type==3 )
				{
					foreach($embel_types as $emb_key=>$emb_type) 
					{
						 
						$max_seq=sql_select("select max(sys_number_prefix_num) as next_id from pro_gmts_delivery_mst where company_id=$k  and to_char(insert_date,'YYYY')=$year and production_type='$k_type' and embel_name=$emb_key ");
						$max_id=$max_seq[0][csf("next_id")]; 
						if(!$max_id) {$max_id="1";} 
						$insert=execute_query("INSERT into  platform_sequence_pk(table_name,next_id ,company_id,entry_form ,year,item_category_id,booking_type,production_type,emblishment_type,transfer_criteria) values('".strtoupper($table_val)."','$max_id','$k','0','$year','0','0','$k_type','$emb_key','0')" );
 

					}

				}


				 
			}

		}
	} 
	 
}

oci_commit($con); 
echo "Success";

 
?>