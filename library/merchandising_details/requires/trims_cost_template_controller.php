<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="set_cons_uom")
{
	$cons_uom=return_field_value("trim_uom", "lib_item_group", "id=$data");
	echo create_drop_down( "cbo_cons_uom", 70, $unit_of_measurement,"", "", "",$cons_uom, "",1,"" );
	exit();
}

if ($action=="on_change_data")
{
	$data=explode('_',$data);
	if (str_replace("'","",$data[0])!= "0")$template=" and a.template_name='$data[0]'"; else {
		echo "<span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Please select template name first.";
		die;
	}

	if(str_replace("'","",$data[1])!= "0")
	{
		$buyer="and c.buyer_id in ($data[1])";
	}
	else
	{
		$buyer="";
	}
	
	$lib_buyer=return_library_array( "select buyer_name,id from lib_buyer", "id", "buyer_name"  );
	$trims_group=return_library_array( "select item_name,id from lib_item_group","id","item_name"  );
	$supplier_name=return_library_array( "select supplier_name,id from  lib_supplier","id","supplier_name"  );
	//$brand_library=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name"  );
	$brand_array=return_library_array("select id,brand_name from lib_buyer_brand","id","brand_name");
	$yes_no=array(1=>"Yes",2=>"No"); //2= Deleted,3= Locked
	//echo $data;
	 $main_sql="select a.user_code,a.template_name,a.trims_group,a.item_description,a.cons_uom,a.sup_ref,a.cons_dzn_gmts,a.purchase_rate,a.amount,a.apvl_req,a.supplyer,a.id as mst_id,a.brand_name,c.lib_trim_costing_temp_id,c.buyer_id from  lib_trim_costing_temp a,lib_trim_costing_temp_dtls c where  a.id=c.lib_trim_costing_temp_id and a.is_deleted=0 $template $buyer order by a.id";

	 $brand_sql="select a.id as mst_id,a.brand_name,c.lib_trim_costing_temp_id,c.buyer_id,c.brand_id from  lib_trim_costing_temp a,lib_trim_costing_temp_brand c where  a.id=c.lib_trim_costing_temp_id and a.is_deleted=0 $template $buyer order by a.id";

	$brand_query=sql_select($brand_sql);
	$brandsAraay=array();$brandId_arrays=array();$brandname_arrays=array();
		foreach ($brand_query as $row)
		{
			$brandId_arrays[$row[csf('buyer_id')]][$row[csf('lib_trim_costing_temp_id')]].=$row[csf("brand_id")].",";
			if($brandId_arrays[$row[csf('buyer_id')]][$row[csf('lib_trim_costing_temp_id')]]!='')
			{
				$brandsAraay=explode(',',$brandId_arrays[$row[csf('buyer_id')]][$row[csf('lib_trim_costing_temp_id')]]);
				foreach ($brandsAraay as $branid)
				{
					 //echo $branid.'='.$brand_array[$branid].'<br>';
					$brand_Arr[$row[csf('buyer_id')]][$row[csf('lib_trim_costing_temp_id')]].=$brand_array[$branid].',';
				}
			}

		}
	
	// echo "<pre>";print_r($brand_arrays);die;
	// echo "<pre>";print_r($brand_arrays);die;

	//echo "<pre>";print_r($brandId_arrays);die;
	//$arr=array (0=>$lib_buyer,1=>$brand_array,4=>$trims_group,6=>$unit_of_measurement,10=>$yes_no,11=>$supplier_name);
	//echo  create_list_view ( "list_view", "Related Buyer,Brand Name,Template Name,User Code,Trims Group,Item Desc,Cons. UOM,Brand/Sup Ref.,Cons/Dzn Gmts,Parchase Rate,Amount,Approval Required,Supplier", "150,150,60,100,120,130,70,100,70,70,100,70,80","1340","220",0, $sql, "get_php_form_data", "id", "'load_php_data_to_form'", 1, "buyer_id,brand_id,0,0,trims_group,0,cons_uom,0,0,0,apvl_req,supplyer,0", $arr , "buyer_id,brand_id,template_name,user_code,trims_group,item_description,cons_uom,sup_ref,cons_dzn_gmts,purchase_rate,amount,apvl_req,supplyer", "../merchandising_details/requires/trims_cost_template_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0,2,2,2') ;
	?>
	<div align="left" style=" margin-left:5px;margin-top:10px"> 
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1340" align="left" class="rpt_table" >
		 <thead>
			 <th width="150">Related Buyer</th>
			 <th width="150">Brand Name</th>
			 <th width="60">Template Name</th>
			 <th width="100">User Code</th>               
			 <th width="120">Trims Group</th>
			 <th width="130">Item Desc</th>
			 <th width="70">Cons. UOM</th>
			 <th width="100">Brand/Sup Ref.</th> 
			 <th width="70">Cons/Dzn Gmts</th>
			 <th width="70">Parchase Rate</th>
			 <th width="100">Amount</th>
			 <th width="70">Approval Required</th>  
			 <th width="80">Supplier</th>          
		 </thead>
	 </table>
	<div style="width:1360px; max-height:270px; overflow-y:scroll" id="container_batch" >	 
		 <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1340" class="rpt_table" id="list_view">  
			 <?
			 $i=1;
			 $main_query=sql_select($main_sql);
			 foreach ($main_query as $row)
			 { 
				$buyersids=$brandId_arrays[$row[csf('buyer_id')]][$row[csf('mst_id')]];
				$buyers=chop($buyersids,',');
				$brandsAraay=array_unique(explode(',',$buyers));
				//$brandsAraay=explode(',',$brandId_arrays[$row[csf('buyer_id')]][$row[csf('lib_trim_costing_temp_id')]]);
				$brand_name="";
				foreach ($brandsAraay as $branid)
				{
					 //echo $branid.'='.$brand_array[$branid].'<br>';
					if($brand_name=="") $brand_name=$brand_array[$branid];else $brand_name.=",".$brand_array[$branid];
				}
				 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
				 ?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer" onClick="get_php_form_data('<? echo $row[csf("mst_id")];?>','load_php_data_to_form','requires/trims_cost_template_controller');"> 
					<td width="150" style="word-break:break-all"><? echo $lib_buyer[$row[csf('buyer_id')]]; ?></p></td> 
					<td width="150" style="word-break:break-all" align="center"><?= $brand_name; ?></p></td>
					<td width="60" style="word-break:break-all" align="center"><? echo $row[csf('template_name')]; ?></p></td>
					<td width="100" style="word-break:break-all"><? echo $row[csf('user_code')]; ?></p></td>
					<td width="120" style="word-break:break-all"><? echo $trims_group[$row[csf('trims_group')]]; ?></p></td>
					<td width="130" style="word-break:break-all" align="right"><? echo $row[csf('item_description')]; ?></p></td>
					<td width="70" style="word-break:break-all"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></p></td>
					<td width="100" style="word-break:break-all" align="right"><? echo $row[csf('sup_ref')]; ?></p></td>
					<td width="70" style="word-break:break-all"><? echo change_date_format($row[csf('cons_dzn_gmts')]); ?></p></td>
					<td width="70" style="word-break:break-all"><? echo $row[csf('purchase_rate')]; ?></p></td>
					<td width="100" style="word-break:break-all"><? echo $row[csf('amount')]; ?></p></td>
					<td width="70" style="word-break:break-all"><? echo $yes_no[$row[csf('apvl_req')]]; ?></p></td>
					<td width="80" style="word-break:break-all"><? echo $supplier_name[$row[csf('supplyer')]]; ?></p></td>
				</tr> 
				<? 
				$i++;
			 }
			 ?> 
		 </table>        
	 </div>
 </div>
 <?php
	exit();
}
if ($action=="load_drop_down_brand")
{
	 
	echo create_drop_down( "cbo_brand_name", 150, "select id, brand_name from lib_buyer_brand brand where buyer_id in ($data) and status_active =1 and is_deleted=0 $userbrand_idCond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	exit();

}   
if ($action=="load_drop_down_template")
{
    if($data != 0)
    {
	echo create_drop_down( "cbo_template_name", 150, "select distinct a.template_name,b.buyer_id from lib_trim_costing_temp a,lib_trim_costing_temp_dtls b where a.id=b.lib_trim_costing_temp_id and b.buyer_id =$data and a.is_deleted=0 ORDER BY a.template_name ASC","template_name,template_name", 1, "-- Select Template --", $selected, "" );
	exit();
    }
    else{
        echo create_drop_down( "cbo_template_name", 150, "select template_name from lib_trim_costing_temp where is_deleted=0 group by template_name ORDER BY template_name ASC","template_name,template_name", 1, "-- Select Template --", '', "" );
        exit();
    }
}

if ($action=="load_php_data_to_form")
{
	$brand_array=return_library_array("select id,brand_name from lib_buyer_brand","id","brand_name");
	$nameArray=sql_select( "select id,related_buyer,template_name,user_code,trims_group,item_description,cons_uom,sup_ref,cons_dzn_gmts,ex_per,tot_cons,purchase_rate,amount,apvl_req,supplyer,brand_name,status_active from   lib_trim_costing_temp where id='$data'" );


	foreach ($nameArray as $inf)
	{
		
		echo "document.getElementById('txt_user_code').value  = '".($inf[csf("user_code")])."';\n";
		echo "document.getElementById('cbo_trims_group').value = '".($inf[csf("trims_group")])."';\n"; 
		echo "document.getElementById('txt_desc').value = '".($inf[csf("item_description")])."';\n";    
		echo "document.getElementById('cbo_cons_uom').value  = '".($inf[csf("cons_uom")])."';\n";
		echo "document.getElementById('txt_sub_ref').value  = '".($inf[csf("sup_ref")])."';\n";
		echo "document.getElementById('txt_cons_dzn_gmts').value  = '".($inf[csf("cons_dzn_gmts")])."';\n";
		echo "document.getElementById('txt_tot_cons').value  = '".($inf[csf("tot_cons")])."';\n";
		echo "document.getElementById('txt_ex_per').value  = '".($inf[csf("ex_per")])."';\n";
		echo "document.getElementById('txt_purchase_rate').value  = '".($inf[csf("purchase_rate")])."';\n";
		echo "document.getElementById('txt_amount').value = '".($inf[csf("amount")])."';\n";
		echo "document.getElementById('cbo_apvl_req').value = '".($inf[csf("apvl_req")])."';\n";
		echo "document.getElementById('cbo_supplyer').value = '".($inf[csf("supplyer")])."';\n";   
		echo "document.getElementById('cbo_status').value = '".($inf[csf("status_active")])."';\n"; 
		echo "document.getElementById('template_name').value = '".($inf[csf("template_name")])."';\n";
    	echo "document.getElementById('hidden_template_name').value = '".($inf[csf("template_name")])."';\n";   
    	echo "document.getElementById('update_id').value = '".($inf[csf("id")])."';\n"; 
		echo "set_multiselect('cbo_rel_buyer','0','1','".($inf[csf("related_buyer")])."','0');\n"; 
		echo "set_multiselect('cbo_brand_name','0','1','".($inf[csf("brand_name")])."','0');\n";     
	    echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_trim_cost_temp',1);\n";  
	}
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$sql_brand=sql_select("select ID,BUYER_ID from lib_buyer_brand where status_active=1 and  buyer_id in(".str_replace("'","",$cbo_rel_buyer).") and id in(".str_replace("'","",$cbo_brand_name).")");
	foreach($sql_brand as $row)
	{
		$brandBuyerArr[$row['ID']][$row['BUYER_ID']]=$row['ID'];
	}
	
	if ($operation==0)  // Insert Here
	{
		if (is_duplicate_field( "a.id", "lib_trim_costing_temp a, lib_trim_costing_temp_dtls b", " a.id=b.lib_trim_costing_temp_id and a.trims_group=$cbo_trims_group and a.sup_ref=$txt_sub_ref and b. buyer_id in(".str_replace("'","",$cbo_rel_buyer).") and a.brand_name in(".str_replace("'","",$cbo_brand_name).") and a.template_name=$template_name and is_deleted=0" ) == 1)
		{
			echo "11**0"; disconnect($con);die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$id=return_next_id( "id", "lib_trim_costing_temp", 1 ) ;
			$field_array= "id,related_buyer,template_name,user_code,trims_group,item_description,cons_uom,sup_ref,cons_dzn_gmts,ex_per,tot_cons,purchase_rate,amount,apvl_req,supplyer,brand_name,inserted_by,inserted_date,status_active,is_deleted";
			
			$data_array="(".$id.",".$cbo_rel_buyer.",".trim($template_name).",".$txt_user_code.",".$cbo_trims_group.",".$txt_desc.",".$cbo_cons_uom.",".$txt_sub_ref.",".$txt_cons_dzn_gmts.",".$txt_ex_per.",".$txt_tot_cons.",".$txt_purchase_rate.",".$txt_amount.",".$cbo_apvl_req.",".$cbo_supplyer.",".$cbo_brand_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",'0')";
			$rID=sql_insert("lib_trim_costing_temp",$field_array,$data_array,0);
			//echo "10** insert into lib_trim_costing_temp (".$field_array.") values ".$data_array; die;
			//Insert Data in  lib_trim_costing_temp_dtls Table----------------------------------------
			$data_array="";
			$buyer_type=explode(',',str_replace("'","",$cbo_rel_buyer));
			$brand_idArr=explode(',',str_replace("'","",$cbo_brand_name));
			$lib_trim_costing_temp_dtls_id=return_next_id( "id", "lib_trim_costing_temp_dtls", 1 );
			for($i=0; $i<count($buyer_type); $i++)
			{
				if($i==0) $add_comma=""; else $add_comma=",";
				$data_array.="$add_comma(".$lib_trim_costing_temp_dtls_id.",".$id.",".$buyer_type[$i].")";
				$lib_trim_costing_temp_dtls_id=$lib_trim_costing_temp_dtls_id+1;
			}
			if(str_replace("'","",$cbo_brand_name)!="")
			{
				$lib_trim_costing_temp_brand_id=return_next_id( "id", "lib_trim_costing_temp_brand", 1 );
				for($i=0; $i<count($buyer_type); $i++)
				{
					$buyerid=$buyer_type[$i];
					for($j=0; $j<count($brand_idArr); $j++)
					{
						$brandBuyer=$brandBuyerArr[$brand_idArr[$j]][$buyerid];
						if($brandBuyer!="")
						{
							if($j==0) $add_comma_brand=""; else $add_comma_brand=",";
							$brand_data_array.="$add_comma_brand(".$lib_trim_costing_temp_brand_id.",".$id.",".$buyerid.",".$brandBuyer.")";
							$lib_trim_costing_temp_brand_id=$lib_trim_costing_temp_brand_id+1;
						}
						
					}
				}
				$field_array_brand="id,lib_trim_costing_temp_id, buyer_id,brand_id";
				$rID3=sql_insert("lib_trim_costing_temp_brand",$field_array_brand,$brand_data_array,1);
			}
			
			$field_array="id,lib_trim_costing_temp_id, buyer_id";
			$rID2=sql_insert("lib_trim_costing_temp_dtls",$field_array,$data_array,1);

			
			
			//echo "shajjad".$rID;die;
		
			//----------------------------------------------------------------------------------
			if($db_type==0)
			{
				if($rID && $rID2 ){
					mysql_query("COMMIT");  
					//echo "0**".$rID;
					//echo "0**".str_replace("'",'',$rID)."**".str_replace("'",'',$cbo_rel_buyer);
					echo "0**".str_replace("'",'',$rID)."**".str_replace("'",'',$cbo_rel_buyer)."**".str_replace("'",'',$template_name);
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			
			if($db_type==2 || $db_type==1 )
			{
			 if($rID && $rID2)
			    {
					oci_commit($con);   
					//echo "0**".$rID;
					//echo "0**".str_replace("'",'',$rID)."**".str_replace("'",'',$cbo_rel_buyer);
					echo "0**".str_replace("'",'',$rID)."**".str_replace("'",'',$cbo_rel_buyer)."**".str_replace("'",'',$template_name);
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
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array= "related_buyer*template_name*user_code*trims_group*item_description*cons_uom*sup_ref*cons_dzn_gmts*ex_per*tot_cons*purchase_rate*amount*apvl_req*supplyer*brand_name*updated_by*updated_date*status_active*is_deleted";
		
		$data_array="".$cbo_rel_buyer."*".trim($template_name)."*".$txt_user_code."*".$cbo_trims_group."*".$txt_desc."*".$cbo_cons_uom."*".$txt_sub_ref."*".$txt_cons_dzn_gmts."*".$txt_ex_per."*".$txt_tot_cons."*".$txt_purchase_rate."*".$txt_amount."*".$cbo_apvl_req."*".$cbo_supplyer."*".$cbo_brand_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*'0'";
		
		//Insert Data in  lib_trim_costing_temp_dtls Table----------------------------------------
		
		$data_array1="";
		$buyer_type=explode(',',str_replace("'","",$cbo_rel_buyer));
		$brand_idArr=explode(',',str_replace("'","",$cbo_brand_name));
		for($i=0; $i<count($buyer_type); $i++)
		{
			if($lib_trim_costing_temp_dtls_id=="") $lib_trim_costing_temp_dtls_id=return_next_id( "id", "lib_trim_costing_temp_dtls", 1 ); else $lib_trim_costing_temp_dtls_id=$lib_trim_costing_temp_dtls_id+1;
			if($i==0) $add_comma=""; else $add_comma=",";
			$data_array1.="$add_comma(".$lib_trim_costing_temp_dtls_id.",".$update_id.",".$buyer_type[$i].")";
		}
			if(str_replace("'","",$cbo_brand_name)!="")
			{
				$lib_trim_costing_temp_brand_id=return_next_id( "id", "lib_trim_costing_temp_brand", 1 );
				for($i=0; $i<count($buyer_type); $i++)
				{
					$buyerid=$buyer_type[$i];
					for($j=0; $j<count($brand_idArr); $j++)
					{
						$brandBuyer=$brandBuyerArr[$brand_idArr[$j]][$buyerid];
						//if($brandBuyer=="") $brandBuyer=0;
						if($brandBuyer!="")
						{
							if($j==0) $add_comma_brand=""; else $add_comma_brand=",";
							$brand_data_array.="$add_comma_brand(".$lib_trim_costing_temp_brand_id.",".$update_id.",".$buyerid.",".$brandBuyer.")";
							$lib_trim_costing_temp_brand_id=$lib_trim_costing_temp_brand_id+1;
						}
						
					}
				}
				
			}
//echo "10**=".$brand_data_array.'=A';die;
		if($template_name != $hidden_template_name){
			$field_array_up= "template_name*updated_by*updated_date";

		$data_array_up="".$template_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID3 = sql_update("lib_trim_costing_temp",$field_array_up,$data_array_up,"template_name","".$hidden_template_name."",1);
		}
		
		$rID=sql_update("lib_trim_costing_temp",$field_array,$data_array,"id","".$update_id."",0);
		$rID1=execute_query( "delete from  lib_trim_costing_temp_dtls where  lib_trim_costing_temp_id = $update_id",1);
		$rID3=execute_query( "delete from  lib_trim_costing_temp_brand where  lib_trim_costing_temp_id = $update_id",0);
		if($brand_data_array!="")
		{
			// echo "insert into subcon_delivery_dtls (".$field_array_brand.") values ".$brand_data_array;die;
			$field_array_brand="id,lib_trim_costing_temp_id, buyer_id,brand_id";
			$rID3=sql_insert("lib_trim_costing_temp_brand",$field_array_brand,$brand_data_array,1);

		}
		
		$field_array1="id,lib_trim_costing_temp_id, buyer_id";
		$rID2=sql_insert("lib_trim_costing_temp_dtls",$field_array1,$data_array1,1);
	
		//----------------------------------------------------------------------------------
		if($db_type==0)
		{
			 if($rID && $rID1 && $rID2 )
			   {
				mysql_query("COMMIT");  
				//echo "1**".$rID;
				echo "1**".str_replace("'",'',$rID)."**".str_replace("'",'',$cbo_rel_buyer);
			   }
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			 if($rID && $rID1 && $rID2 )
			{
				oci_commit($con);   
				//echo "1**".$rID;
				//echo "1**".str_replace("'",'',$rID)."**".str_replace("'",'',$cbo_rel_buyer);
				echo "1**".str_replace("'",'',$rID)."**".str_replace("'",'',$cbo_rel_buyer)."**".str_replace("'",'',$template_name);
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
		$field_array="updated_by*updated_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		
		$rID=sql_update("lib_trim_costing_temp",$field_array,$data_array,"id","".$update_id."",1);
		
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$rID)."**".str_replace("'",'',$cbo_rel_buyer)."**".str_replace("'",'',$template_name);
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
				//echo "2**".$rID;
				echo "2**".str_replace("'",'',$rID)."**".str_replace("'",'',$cbo_rel_buyer)."**".str_replace("'",'',$template_name);
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

if($action=="get_template_dropdown")
{
	$template_dropdown=create_drop_down("cbo_template_name",150,"select template_name from lib_trim_costing_temp where is_deleted=0 group by template_name order by template_name asc","template_name,template_name",1,'--Select--','','');
	echo $template_dropdown;
	die;
}

if($action == "copy_template_popup")
{
	//extract($_REQUEST);
    echo load_html_head_contents("Copy Template", "../../../", 1, 1, $unicode);?>
    <script>
    	function copy_template() {
    	if (form_validation('cbo_buyer*cbo_template_name*template_name','Related Buyer','Template Name','New Template Name')==false)
        {
            return;
        }
        else{
        	var buyer= document.getElementById('cbo_buyer').value;
        	var template = document.getElementById('cbo_template_name').value;
        	var new_template_name = document.getElementById('template_name').value;
        	var data="action=copy_template&buyer="+buyer+'&template='+template+'&new_template='+new_template_name;

			http.open("POST","trims_cost_template_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange=copy_template_response;
        }
        function copy_template_response()
        {
            if(http.readyState == 4)
	        {

	        	var reponse=http.responseText.split('**');
	        	var data =reponse[3]+'_'+reponse[2];
	        	document.getElementById('hidden_template_name').value=data;
	        	document.getElementById('hidden_buyer_name').value=reponse[3];
	            parent.emailwindow.hide();
	        }
        }
        }
    </script>
    <style>
    	.combo_boxes{
    		height:23px !important;
    	}
    </style>
    </head>
    <body>
    <div align="center" style="width:100%;">
    	<table cellspacing="0" class="rpt_table" border="1" rules="all" width="300">
    		<tr>
    			<th width="150" align="center" class="must_entry_caption">Copy Template To</th>
    			<td width="150" ><? echo create_drop_down( "cbo_buyer",150, "select buyer_name,id from  lib_buyer where is_deleted=0 and  status_active=1 order by buyer_name", "id,buyer_name", 1, '--Select--','',''); ?></td>
    		</tr>
    		<tr>
    			<th width="150" align="center" class="must_entry_caption">Copy From</th>
    			<td width="150"><? echo create_drop_down("cbo_template_name",150,"select template_name from lib_trim_costing_temp where is_deleted=0 group by template_name ORDER BY template_name ASC","template_name,template_name",1,'--Select--','','');
                         ?></td>
    		</tr>
    		<tr>
    			<th width="150" align="center" class="must_entry_caption">New Template Name</th>
    			<td width="150"><input class="text_boxes" type="text" name="template_name" id="template_name" style="width: 140px" >
    				<input type="hidden" name="hidden_template_name" id="hidden_template_name">
    				<input type="hidden" name="hidden_buyer_name" id="hidden_buyer_name">
    			</td>
    		</tr>
    		<tr>
    			<td colspan="2" align="center"><input class="formbutton" type="button" name="copy" value="Copy Template" onClick="copy_template()"></td>
    		</tr>

    	</table>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}
if($action == "copy_template")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//echo "SELECT distinct a.user_code,a.trims_group,a.cons_uom,a.cons_dzn_gmts,a.purchase_rate,a.amount,a.apvl_req,a.supplyer,a.sup_ref,a.item_description,a.status_active from wo_lib_trim_cost_temp a, wo_lib_trim_cost_temp_dtls b where a.id=b.lib_trim_costing_temp_id and a.template_name='$template' and a.status_active=1 and a.is_deleted=0"; die;
	$template_array=sql_select("SELECT distinct a.user_code,a.trims_group,a.cons_uom,COALESCE(a.cons_dzn_gmts, 0) as cons_dzn_gmts,COALESCE(a.tot_cons, 0) as tot_cons,COALESCE(a.ex_per, 0) as ex_per,COALESCE(a.purchase_rate, 0) as purchase_rate,COALESCE(a.amount, 0)as amount,a.apvl_req,a.supplyer,a.sup_ref,a.item_description,a.status_active from lib_trim_costing_temp a, lib_trim_costing_temp_dtls b where a.id=b.lib_trim_costing_temp_id and a.template_name='$template' and a.status_active=1 and a.is_deleted=0");
	if(count($template_array) > 0)
	{
		$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
		$id=return_next_id( "id", "lib_trim_costing_temp", 1 ) ;
		$field_template_array= "id,related_buyer,template_name,user_code,trims_group,item_description,cons_uom,sup_ref,cons_dzn_gmts,ex_per,tot_cons,purchase_rate,amount,apvl_req,supplyer,inserted_by,inserted_date,status_active,is_deleted";
		$copy_template_array=' ';
		$template_id_array=' ';
		$data_array = ' ';
		$i=0;
		foreach ($template_array as $key => $value) {
			if($i==0) $add_comma=""; else $add_comma=",";
			$copy_template_array.="$add_comma(".$id.",".$buyer.",'".$new_template."','".$value[csf("user_code")]."',".$value[csf("trims_group")].",'".$value[csf("item_description")]."',".$value[csf("cons_uom")].",'".$value[csf("sup_ref")]."',".$value[csf("cons_dzn_gmts")].",".$value[csf("ex_per")].",".$value[csf("tot_cons")].",".$value[csf("purchase_rate")].",".$value[csf("amount")].",".$value[csf("apvl_req")].",".$value[csf("supplyer")].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$value[csf("status_active")].",'0')";
			$template_id_array.="$add_comma".$id."";
			$id = $id+1;
			$i++;
		}
		//echo "INSERT INTO  lib_trim_costing_temp (".$field_template_array.") values ".$copy_template_array.""; die;
		$rID=sql_insert("lib_trim_costing_temp",$field_template_array,$copy_template_array,1);
		//echo $template_id_array; die;
		$template_id=explode(',',str_replace("'","",$template_id_array));
		//var_dump($template_id); die;
		$wo_lib_trim_cost_temp_dtls_id=return_next_id( "id", "lib_trim_costing_temp_dtls", 1 );
		for($i=0; $i<count($template_id); $i++)
		{
			if($i==0) $add_comma=""; else $add_comma=",";
			$data_array.="$add_comma(".$wo_lib_trim_cost_temp_dtls_id.",".$template_id[$i].",".$buyer.")";
			$wo_lib_trim_cost_temp_dtls_id=$wo_lib_trim_cost_temp_dtls_id+1;
		}
		$field_array="id,lib_trim_costing_temp_id, buyer_id";
		//echo "INSERT INTO  wo_lib_trim_cost_temp (".$field_array.") values ".$data_array.""; die;
		$rID2=sql_insert("lib_trim_costing_temp_dtls",$field_array,$data_array,1);
		if($db_type==0)
			{
				if($rID && $rID2 ){
					mysql_query("COMMIT");
					//echo "0**".$rID;
					echo "0**".str_replace("'",'',$rID)."**".$buyer."**".$new_template;
				}
				else{
					mysql_query("ROLLBACK");
					echo "10**".$rID;
				}
			}

			if($db_type==2 || $db_type==1 )
			{
			 if($rID && $rID2)
			    {
					oci_commit($con);
					//echo "0**".$rID;
					echo "0**".str_replace("'",'',$rID)."**".$buyer."**".$new_template;
				}
				else{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
			disconnect($con);
			die;
	}
	else {
		echo '10*AAA'; die;
	}


}
?>