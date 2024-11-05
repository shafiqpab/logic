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
		$buyer="and b.buyer_id in ($data[1])";
	}
	else
	{
		$buyer="";
	}
	$lib_buyer=return_library_array( "select buyer_name,id from lib_buyer", "id", "buyer_name"  );
	$trims_group=return_library_array( "select item_name,id from lib_item_group","id","item_name"  );
	$supplier_name=return_library_array( "select supplier_name,id from  lib_supplier","id","supplier_name"  );
	$yes_no=array(1=>"Yes",2=>"No"); //2= Deleted,3= Locked
	//echo $data; die;
	$arr=array (0=>$lib_buyer,3=>$trims_group,5=>$unit_of_measurement,10=>$yes_no,13=>$supplier_name);
	//echo "select  b.buyer_id,a.template_name,a.user_code,a.trims_group,a.item_description,a.cons_uom,a.sup_ref,a.cons_dzn_gmts,a.purchase_rate,a.amount,a.apvl_req,a.supplyer,a.id from  wo_lib_trim_cost_temp a,wo_lib_trim_cost_temp_dtls b where a.id=b.lib_trim_costing_temp_id $template $buyer and a.is_deleted=0 order by a.template_name"; die;

	
	echo  create_list_view ( "list_view","Related Buyer,Template Name,User Code,Trims Group,Item Desc,Cons.UOM,Brand/Sup Ref.,Cons/ Gmts, Ex.% ,Total Cons.,Parchase Rate,Amount,Approval Required,Supplier","120,60,90,120,130,50,90,60,60,60,50,40,50,160,","1200","410",0, "select  b.buyer_id,a.template_name,a.user_code,a.trims_group,a.item_description,a.cons_uom,a.sup_ref,a.cons_dzn_gmts,a.purchase_rate,a.amount,a.apvl_req,a.supplyer,a.id,a.ex_per,a.tot_cons from  wo_lib_trim_cost_temp a,wo_lib_trim_cost_temp_dtls b where a.id=b.lib_trim_costing_temp_id $template $buyer and a.is_deleted=0 order by a.id", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "buyer_id,0,0,trims_group,0,cons_uom,0,0,0,0,0,0,apvl_req,supplyer", $arr , "buyer_id,template_name,user_code,trims_group,item_description,cons_uom,sup_ref,cons_dzn_gmts,ex_per,tot_cons,purchase_rate,amount,apvl_req,supplyer", "requires/woven_trims_cost_template_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0,0,0,0,0') ;
	exit();
}
if ($action=="load_drop_down_template")
{
    if($data != 0)
    {
	echo create_drop_down( "cbo_template_name", 150, "select distinct a.template_name,b.buyer_id from wo_lib_trim_cost_temp a,wo_lib_trim_cost_temp_dtls b where a.id=b.lib_trim_costing_temp_id and b.buyer_id =$data and a.is_deleted=0 ORDER BY a.template_name ASC","template_name,template_name", 1, "-- Select Template --", $selected, "" );
	exit();
    }
    else{
        echo create_drop_down( "cbo_template_name", 150, "select template_name from wo_lib_trim_cost_temp where is_deleted=0 group by template_name ORDER BY template_name ASC","template_name,template_name", 1, "-- Select Template --", '', "" );
        exit();
    }
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id,related_buyer,template_name,user_code,trims_group,item_description,cons_uom,sup_ref,ex_per,tot_cons,cons_dzn_gmts,purchase_rate,amount,apvl_req,supplyer,status_active from   wo_lib_trim_cost_temp where id='$data'" );
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

    	echo "document.getElementById('update_id').value = '".($inf[csf("id")])."';\n";
    	echo "document.getElementById('template_name').value = '".($inf[csf("template_name")])."';\n";
    	echo "document.getElementById('hidden_template_name').value = '".($inf[csf("template_name")])."';\n";
	    echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_trim_cost_temp',1);\n";
		echo "set_multiselect('cbo_rel_buyer','0','1','".($inf[csf("related_buyer")])."','0');\n";
	}
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		//echo "select a.template_name,a.id,a.trims_group from wo_lib_trim_cost_temp a, wo_lib_trim_cost_temp_dtls b where a.id=b.lib_trim_costing_temp_id and a.trims_group=$cbo_trims_group and a.item_description=$txt_desc and a.sup_ref=$txt_sub_ref and a.template_name=$template_name and b. buyer_id in(".str_replace("'","",$cbo_rel_buyer).") and is_deleted=0";die;
		if (is_duplicate_field( "a.id", "wo_lib_trim_cost_temp a, wo_lib_trim_cost_temp_dtls b", "a.id=b.lib_trim_costing_temp_id and a.item_description=$txt_desc and a.trims_group=$cbo_trims_group and a.sup_ref=$txt_sub_ref and a.template_name=$template_name and b. buyer_id in(".str_replace("'","",$cbo_rel_buyer).") and is_deleted=0" ) == 1)
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
			$id=return_next_id( "id", "wo_lib_trim_cost_temp", 1 ) ;
			$field_array= "id,related_buyer,template_name,user_code,trims_group,item_description,cons_uom,sup_ref,cons_dzn_gmts,ex_per,tot_cons,purchase_rate,amount,apvl_req,supplyer,inserted_by,inserted_date,status_active,is_deleted";

			$data_array="(".$id.",".$cbo_rel_buyer.",".$template_name.",".$txt_user_code.",".$cbo_trims_group.",".$txt_desc.",".$cbo_cons_uom.",".$txt_sub_ref.",".$txt_cons_dzn_gmts.",".$txt_ex_per.",".$txt_tot_cons.",".$txt_purchase_rate.",".$txt_amount.",".$cbo_apvl_req.",".$cbo_supplyer.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",'0')";
			//echo "INSERT INTO  wo_lib_trim_cost_temp (".$field_array.") values ".$data_array.""; die;
			$rID=sql_insert("wo_lib_trim_cost_temp",$field_array,$data_array,0);
			//Insert Data in  wo_lib_trim_cost_temp_dtls Table
			$data_array="";
			$buyer_type=explode(',',str_replace("'","",$cbo_rel_buyer));
			//echo var_dump($buyer_type); die;
			$wo_lib_trim_cost_temp_dtls_id=return_next_id( "id", "wo_lib_trim_cost_temp_dtls", 1 );
			for($i=0; $i<count($buyer_type); $i++)
			{
				if($i==0) $add_comma=""; else $add_comma=",";
				$data_array.="$add_comma(".$wo_lib_trim_cost_temp_dtls_id.",".$id.",".$buyer_type[$i].")";
				$wo_lib_trim_cost_temp_dtls_id=$wo_lib_trim_cost_temp_dtls_id+1;
			}
			$field_array="id,lib_trim_costing_temp_id, buyer_id";
			//echo "INSERT INTO  wo_lib_trim_cost_temp_dtls (".$field_array.") values ".$data_array.""; die;
			$rID2=sql_insert("wo_lib_trim_cost_temp_dtls",$field_array,$data_array,1);

			//echo "shajjad".$rID;die;

			//----------------------------------------------------------------------------------
			if($db_type==0)
			{
				if($rID && $rID2 ){
					mysql_query("COMMIT");
					//echo "0**".$rID;
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
		$field_array= "related_buyer*template_name*user_code*trims_group*item_description*cons_uom*sup_ref*cons_dzn_gmts*ex_per*tot_cons*purchase_rate*amount*apvl_req*supplyer*updated_by*updated_date*status_active*is_deleted";

		$data_array="".$cbo_rel_buyer."*".$template_name."*".$txt_user_code."*".$cbo_trims_group."*".$txt_desc."*".$cbo_cons_uom."*".$txt_sub_ref."*".$txt_cons_dzn_gmts."*".$txt_ex_per."*".$txt_tot_cons."*".$txt_purchase_rate."*".$txt_amount."*".$cbo_apvl_req."*".$cbo_supplyer."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*'0'";

		//Insert Data in  wo_lib_trim_cost_temp_dtls Table----------------------------------------

		$data_array1="";
		$buyer_type=explode(',',str_replace("'","",$cbo_rel_buyer));
		for($i=0; $i<count($buyer_type); $i++)
		{
			if($wo_lib_trim_cost_temp_dtls_id=="") $wo_lib_trim_cost_temp_dtls_id=return_next_id( "id", "wo_lib_trim_cost_temp_dtls", 1 ); else $wo_lib_trim_cost_temp_dtls_id=$wo_lib_trim_cost_temp_dtls_id+1;
			if($i==0) $add_comma=""; else $add_comma=",";
			$data_array1.="$add_comma(".$wo_lib_trim_cost_temp_dtls_id.",".$update_id.",".$buyer_type[$i].")";
		}
		if($template_name != $hidden_template_name){
			$field_array_up= "template_name*updated_by*updated_date";

		$data_array_up="".$template_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID3 = sql_update("wo_lib_trim_cost_temp",$field_array_up,$data_array_up,"template_name","".$hidden_template_name."",1);
		}
		$rID=sql_update("wo_lib_trim_cost_temp",$field_array,$data_array,"id","".$update_id."",0);
		$rID1=execute_query( "delete from  wo_lib_trim_cost_temp_dtls where  lib_trim_costing_temp_id = $update_id",0);
		$field_array1="id,lib_trim_costing_temp_id, buyer_id";
		$rID2=sql_insert("wo_lib_trim_cost_temp_dtls",$field_array1,$data_array1,1);

		//----------------------------------------------------------------------------------
		if($db_type==0)
		{
			 if($rID && $rID1 && $rID2 )
			   {
				mysql_query("COMMIT");
				//echo "1**".$rID;
				echo "1**".str_replace("'",'',$rID)."**".str_replace("'",'',$cbo_rel_buyer)."**".str_replace("'",'',$template_name);
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

		$rID=sql_update("wo_lib_trim_cost_temp",$field_array,$data_array,"id","".$update_id."",1);

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
	$template_dropdown=create_drop_down("cbo_template_name",150,"select template_name from wo_lib_trim_cost_temp where is_deleted=0 group by template_name order by template_name asc","template_name,template_name",1,'--Select--','','');
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

			http.open("POST","woven_trims_cost_template_controller.php",true);
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
    			<td width="150"><? echo create_drop_down("cbo_template_name",150,"select template_name from wo_lib_trim_cost_temp where is_deleted=0 group by template_name ORDER BY template_name ASC","template_name,template_name",1,'--Select--','','');
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
	$template_array=sql_select("SELECT distinct a.user_code,a.trims_group,a.cons_uom,COALESCE(a.cons_dzn_gmts, 0) as cons_dzn_gmts,COALESCE(a.tot_cons, 0) as tot_cons,COALESCE(a.ex_per, 0) as ex_per,COALESCE(a.purchase_rate, 0) as purchase_rate,COALESCE(a.amount, 0)as amount,a.apvl_req,a.supplyer,a.sup_ref,a.item_description,a.status_active from wo_lib_trim_cost_temp a, wo_lib_trim_cost_temp_dtls b where a.id=b.lib_trim_costing_temp_id and a.template_name='$template' and a.status_active=1 and a.is_deleted=0");
	if(count($template_array) > 0)
	{
		$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
		$id=return_next_id( "id", "wo_lib_trim_cost_temp", 1 ) ;
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
		//echo "INSERT INTO  wo_lib_trim_cost_temp (".$field_template_array.") values ".$copy_template_array.""; die;
		$rID=sql_insert("wo_lib_trim_cost_temp",$field_template_array,$copy_template_array,1);
		//echo $template_id_array; die;
		$template_id=explode(',',str_replace("'","",$template_id_array));
		//var_dump($template_id); die;
		$wo_lib_trim_cost_temp_dtls_id=return_next_id( "id", "wo_lib_trim_cost_temp_dtls", 1 );
		for($i=0; $i<count($template_id); $i++)
		{
			if($i==0) $add_comma=""; else $add_comma=",";
			$data_array.="$add_comma(".$wo_lib_trim_cost_temp_dtls_id.",".$template_id[$i].",".$buyer.")";
			$wo_lib_trim_cost_temp_dtls_id=$wo_lib_trim_cost_temp_dtls_id+1;
		}
		$field_array="id,lib_trim_costing_temp_id, buyer_id";
		//echo "INSERT INTO  wo_lib_trim_cost_temp (".$field_array.") values ".$data_array.""; die;
		$rID2=sql_insert("wo_lib_trim_cost_temp_dtls",$field_array,$data_array,1);
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
		echo '10*zakaria'; die;
	}


}
?>