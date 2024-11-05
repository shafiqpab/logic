<?
include('../../includes/common.php');
session_start();

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$trans_Type="1";

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
$size_arr=return_library_array( "select id,size_name from  lib_size where status_active=1 and is_deleted=0",'id','size_name');

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 172, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );	
	exit();	 
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_party_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "" );
	exit();
} 

if ($action=="load_drop_down_buyer_pop")
{
	echo create_drop_down( "cbo_party_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "" );
	exit();
} 

if($action=="load_report_button_setting")
{
	extract($_REQUEST);
	//$process = array( &$_POST );
	//extract(check_magic_quote_gpc( $process ));
	//echo "select format_id from lib_report_template where template_name ='".$data."'  and module_id=2 and report_id=35 and is_deleted=0 and status_active=1";die;

 $print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=8 and report_id=237 and is_deleted=0 and status_active=1");

	$print_report_format_arr=explode(",",$print_report_format);
	echo "$('#print_button').hide();\n";

	foreach($print_report_format_arr as $id){
		if($id==78){echo "$('#print_button').show();\n";}
	}

exit();
}
if ($action=="load_td_gmts_material")
{
	$item_ids_str="";
	$item_ids=sql_select("select distinct(item_id) as item_id from subcon_ord_breakdown where order_id='$data'");
	if(count($item_ids)==1){$selected=$item_ids[0][csf("item_id")];}
	else{ $selected="";}
	foreach ($item_ids as $val) 
	{
		if($item_ids_str==""){ $item_ids_str=$val[csf("item_id")]; }
		else{ $item_ids_str.=",".$val[csf("item_id")]; }
	}		
	echo create_drop_down( "cbo_gmts_material_description", 152, $garments_item,"", 1, "--Select Item--",$selected,"", "",$item_ids_str );
	exit();	 
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$trans_Type="1";
	// Insert Start Here ----------------------------------------------------------
	$order_no_id=str_replace("'",'',$order_no_id);
	$update_dtl_id=str_replace("'",'',$update_id2);
	$colorid=str_replace("'",'',$txt_color);
	$txtgsm=str_replace("'",'',$txt_gsm);
	$fin_dia=str_replace("'",'',$txt_fin_dia);
	$receive_quantity=str_replace("'",'',$txt_receive_quantity);
	$material_description=str_replace("'",'',$txt_material_description);
	
	//$txt_material_description
	$order_cond="";
	if ($operation==1)   
	{
		$order_cond="and  id!='$update_dtl_id'";
	}
	
	$sql_pre_rec="select order_id, material_description,color_id,brand,gsm, quantity as qty from sub_material_dtls where  status_active=2 $order_cond";
	$sql_pre_rec_res=sql_select($sql_pre_rec);
	foreach ($sql_pre_rec_res as $row)
	{
		$pre_rec_qty_arr[$row[csf("order_id")]][$row[csf("material_description")]][$row[csf("color_id")]]+=$row[csf("qty")];
	}
	$previ_rec_qty=$pre_rec_qty_arr[$order_no_id][$material_description][$colorid];
	
	$order_sql=sql_select( "select b.id, b.order_no, b.main_process_id, b.order_quantity,c.color_id,c.item_id,c.qnty,c.gsm,c.grey_dia,c.finish_dia from subcon_ord_dtls b,subcon_ord_breakdown c where b.id=c.order_id and b.id=$order_no_id");
	//echo "17**=select b.id, b.order_no, b.main_process_id, b.order_quantity,c.color_id,c.item_id,c.qnty,c.gsm,c.grey_dia,c.finish_dia from subcon_ord_dtls b,subcon_ord_breakdown c where b.id=c.order_id and b.id=$order_no_id";die;
	$order_arr_color=array();
	foreach($order_sql as $row)
	{
	
		$order_arr_color[$row[csf("id")]][$row[csf("color_id")]][$row[csf("gsm")]][$row[csf("finish_dia")]]['quantity']+=$row[csf("qnty")];

	}
	unset($order_sql);
	
	$orderReq_qty=$order_arr_color[$order_no_id][$colorid][$txtgsm][$fin_dia]['quantity'];

	$tot_previ_rec_qty=$receive_quantity+$previ_rec_qty;
	//echo "17**".$colorid.'='.$txtgsm.'='.$fin_dia.'=';die;
	
	//if($tot_previ_rec_qty>$orderReq_qty)
	
	// if(str_replace("'",'',$cbo_item_category)!= 4){
		
	//    if($tot_previ_rec_qty>$orderReq_qty)
	// 	{
	// 		$msg="Recv Qty is Greater than Order Qty.".'**'.$orderReq_qty.'='.$tot_previ_rec_qty;
	// 		echo "17**$msg"; 
	// 		disconnect($con);
	// 		die;			
	// 	}
	// }
	
	if ($operation==0)   
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$color_id=str_replace("'",'',$txt_color);
		$txt_gsm=str_replace("'",'',$txt_gsm);
		if($txt_gsm!='') $txt_gsm=$txt_gsm;else $txt_gsm=0;
		
		if(str_replace("'","",$txtsize)!="")
		{ 
			if (!in_array(str_replace("'","",$txtsize),$new_array_size))
			{
				$size_id = return_id( str_replace("'","",$txtsize), $size_arr, "lib_size", "id,size_name","288");  
				//echo $$txtColorName.'='.$color_id.'<br>';
				$new_array_size[$size_id]=str_replace("'","",$txtsize);
			}
			else $size_id =  array_search(str_replace("'","",$txtsize), $new_array_size); 
		}
		else
		{
			$size_id=0;
		}
		
		if(str_replace("'",'',$update_id)=="")
		{
			if($db_type==0)
			{
				$new_receive_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name),'', 'RECV' , date("Y",time()), 5, "select id,prefix_no,prefix_no_num from sub_material_mst where company_id=$cbo_company_name and trans_Type='$trans_Type' and entry_form=288 and YEAR(insert_date)=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
			}
			else if($db_type==2)
			{
				$new_receive_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name),'', 'RECV' , date("Y",time()), 5, "select id,prefix_no,prefix_no_num from sub_material_mst where company_id=$cbo_company_name and trans_Type='$trans_Type' and entry_form=288 and TO_CHAR(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
			}
	
			if(is_duplicate_field( "a.chalan_no", "sub_material_mst a, sub_material_dtls b", "a.sys_no='$new_receive_no[0]' and a.chalan_no=$txt_receive_challan and b.order_id=$order_no_id and b.item_category_id=$cbo_item_category and b.material_description=$txt_material_description and b.color_id=$color_id and b.gsm=$txt_gsm and b.grey_dia=$txt_grey_dia and b.fin_dia=$txt_fin_dia" )==1)
			{
				check_table_status( $_SESSION['menu_id'],0);
				echo "11**0"; 
				disconnect($con);
				die;			
			}			
			/*echo "10**"."select id,prefix_no,prefix_no_num from sub_material_mst where company_id=$cbo_company_name and party_id=$cbo_party_name and trans_Type='$trans_Type'  and YEAR(insert_date)=".date('Y',time())." order by id desc ";
			print_r($new_receive_no);die;*/
			if (str_replace("'", "", $cbo_item_category)==30)
			{
				$txt_material_description="'".$garments_item[str_replace("'",'',$cbo_gmts_material_description)]."'";
 			}

			$id=return_next_id("id","sub_material_mst",1) ;
			$field_array="id,prefix_no,prefix_no_num,sys_no,trans_type,entry_form,company_id,location_id,party_id,chalan_no,subcon_date,inserted_by,insert_date";
			$data_array="(".$id.",'".$new_receive_no[1]."','".$new_receive_no[2]."','".$new_receive_no[0]."','".$trans_Type."',288,".$cbo_company_name.",".$cbo_location_name.",".$cbo_party_name.",".$txt_receive_challan.",".$txt_receive_date.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";  
			//echo "INSERT INTO sub_material_mst (".$field_array.") VALUES ".$data_array; die;
			$txt_receive_no=$new_receive_no[0];//change_date_format($data[2], "dd-mm-yyyy", "-",1)
		}
		else
		{
			$id=str_replace("'",'',$update_id);
			$field_array="company_id*location_id*party_id*chalan_no*subcon_date*updated_by*update_date";

			$data_array="".$cbo_company_name."*".$cbo_location_name."*".$cbo_party_name."*".$txt_receive_challan."*".$txt_receive_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			$txt_receive_no=str_replace("'",'',$txt_receive_no); 
			if (str_replace("'", "", $cbo_item_category)==30)
			{
				$txt_material_description="'".$garments_item[str_replace("'",'',$cbo_gmts_material_description)]."'";
 			}
		}
		$id1=return_next_id("id","sub_material_dtls",1) ; 
		$field_array2="id, mst_id, item_category_id, material_description, lot_no,brand, color_id, size_id, quantity, rate, subcon_uom, subcon_roll, rec_cone, gsm,stitch_length,used_yarn_details, grey_dia,mc_dia,mc_gauge, fin_dia, dia_uom,acc_item_colour,acc_item_size, order_id, status_active, inserted_by, insert_date";
		$data_array2="(".$id1.",'".$id."',".$cbo_item_category.",".$txt_material_description.",".$txt_lot_no.",".$txt_brand.",'".$color_id."','".$size_id."',".$txt_receive_quantity.",".$txt_rec_rate.",".$cbo_uom.",".$txt_roll.",".$txt_cone.",".$txt_gsm.",".$txt_stitch_length.",".$txt_used_yarn_details.",".$txt_grey_dia.",".$txt_mc_dia.",".$txt_mc_gauge.",".$txt_fin_dia.",".$cbo_dia_uom.",".$txt_acc_item_colour.",".$txt_acc_item_size.",".$order_no_id.",".$cbo_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
		//echo "INSERT INTO sub_material_dtls (".$field_array2.") VALUES ".$data_array2; die;
		$flag=1;
		if(str_replace("'",'',$update_id)=="")
		{
			$rID=sql_insert("sub_material_mst",$field_array,$data_array,0);
			if($flag==1 && $rID==1) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("sub_material_mst",$field_array,$data_array,"id",$update_id,0); 
			if($flag==1 && $rID==1) $flag=1; else $flag=0;
		}
		$rID2=sql_insert("sub_material_dtls",$field_array2,$data_array2,0);
		if($flag==1 && $rID2==1) $flag=1; else $flag=0;
		
		//echo "10**".$rID."**".$rID2;die;				
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$id);
			}
		}
		if($db_type==2)
		{
			if($flag==1) 
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$id);
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

		$color_id=str_replace("'",'',$txt_color);
		$txt_gsm=str_replace("'",'',$txt_gsm);
		$receive_quantity=str_replace("'",'',$txt_receive_quantity);
		if($txt_gsm!='') $txt_gsm=$txt_gsm;else $txt_gsm=0;
		if(str_replace("'","",$txtsize)!="")
		{ 
			if (!in_array(str_replace("'","",$txtsize),$new_array_size))
			{
				$size_id = return_id( str_replace("'","",$txtsize), $size_arr, "lib_size", "id,size_name","288");  
				//echo $$txtColorName.'='.$color_id.'<br>';
				$new_array_size[$size_id]=str_replace("'","",$txtsize);
			}
			else $size_id =  array_search(str_replace("'","",$txtsize), $new_array_size); 
		}
		else $size_id=0;


		if (str_replace("'", "", $cbo_item_category)==30)
		{
			$txt_material_description="'".$garments_item[str_replace("'",'',$cbo_gmts_material_description)]."'";
		}
		//echo "select b.quantity as quantity from sub_material_mst a,sub_material_dtls b";
		$issue_qty = return_field_value("b.quantity as quantity", "sub_material_mst a,sub_material_dtls b", "a.id=b.mst_id and b.rec_challan=$txt_receive_challan  and b.order_id=$order_no_id  and a.trans_type=2  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "quantity");
		
		//$issue_qty=$issue_qty;
		if($issue_qty>$receive_quantity)
		{
		//if ($issue_qty>0) {
			echo "14**0**$issue_qty";
			disconnect($con);
			die;
		}
					
		$field_array="company_id*location_id*party_id*chalan_no*subcon_date*updated_by*update_date";
		$data_array="".$cbo_company_name."*".$cbo_location_name."*".$cbo_party_name."*".$txt_receive_challan."*".$txt_receive_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		
		//$color_id=return_id( $txt_color, $color_arr, "lib_color", "id,color_name");
		
		$field_array2="item_category_id*material_description*lot_no*brand*color_id*size_id*quantity*rate*subcon_uom*subcon_roll*rec_cone*gsm*stitch_length*used_yarn_details*grey_dia*mc_dia*mc_gauge*fin_dia*dia_uom*acc_item_colour*acc_item_size*order_id*status_active*updated_by*update_date";
		$data_array2="".$cbo_item_category."*".$txt_material_description."*".$txt_lot_no."*".$txt_brand."*'".$color_id."'*'".$size_id."'*".$txt_receive_quantity."*".$txt_rec_rate."*".$cbo_uom."*".$txt_roll."*".$txt_cone."*".$txt_gsm."*".$txt_stitch_length."*".$txt_used_yarn_details."*".$txt_grey_dia."*".$txt_mc_dia."*".$txt_mc_gauge."*".$txt_fin_dia."*".$cbo_dia_uom."*".$txt_acc_item_colour."*".$txt_acc_item_size."*".$order_no_id."*".$cbo_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";  
		$flag=1;
		$rID=sql_update("sub_material_mst",$field_array,$data_array,"id",$update_id,0); 
		if($flag==1 && $rID==1) $flag=1; else $flag=0;			
		$rID2=sql_update("sub_material_dtls",$field_array2,$data_array2,"id",$update_id2,0); //die;
		if($flag==1 && $rID2==1) $flag=1; else $flag=0;
		 
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id);	
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id);	
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id);
			}
		}
		disconnect($con);
	}
	else if ($operation==2)   // delete
	{
		$con = connect();
		
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$sys_no = return_field_value("sys_no as sys_no", "sub_material_mst a,sub_material_dtls b", "a.id=b.mst_id and b.rec_challan=$txt_receive_challan  and b.order_id=$order_no_id  and a.trans_type=2  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "sys_no");
		
		if($sys_no)
		{
			echo "13**Issue Found,Delete not allowed(Issue ID=$sys_no)";
			disconnect($con);
			die;
		}
		 //echo $zero_val;
		if ( $zero_val==1 )
		{
			$field_array="status_active*is_deleted*updated_by*update_date";
			$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			$data_array_dtls="1*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			
			if (str_replace("'",'',$cbo_status)==1)
			{
				$rID=sql_update("sub_material_dtls",$field_array,$data_array_dtls,"id",$update_id2,1); //die;
			}
			else
			{
				$rID=sql_update("sub_material_mst",$field_array,$data_array,"id",$update_id,0);  
				//echo "INSERT INTO sub_material_dtls (".$field_array.") VALUES ".$data_array_dtls; die;

				$rID=sql_update("sub_material_dtls",$field_array,$data_array_dtls,"mst_id",$update_id,1);  
			}
		}
		else
		{
			$rID=0;
		}
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id2);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id2);
			}
		}
		if($db_type==2)
		{
			if($rID)
			{
				oci_commit($con);
				echo "2**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id2);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id2);
			}
		}
		disconnect($con); 
	}
}

function sql_updates($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit,$return_query='')
{

	$strQuery = "UPDATE ".$strTable." SET ";
	$arrUpdateFields=explode("*",$arrUpdateFields);
	$arrUpdateValues=explode("*",$arrUpdateValues);

	if(count($arrUpdateFields)!=count($arrUpdateValues)){
		return "0";
	}

	if(is_array($arrUpdateFields))
	{
		$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
		$Arraysize = count($arrayUpdate);
		$i = 1;
		foreach($arrayUpdate as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value;
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrUpdateFields."=".$arrUpdateValues;
	}
	$strQuery .=" WHERE ";

	$arrRefFields=explode("*",$arrRefFields);
	$arrRefValues=explode("*",$arrRefValues);
	if(is_array($arrRefFields))
	{
		$arrayRef = array_combine($arrRefFields,$arrRefValues);
		$Arraysize = count($arrayRef);
		$i = 1;
		foreach($arrayRef as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value." AND ":$key."=".$value."";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrRefFields."=".$arrRefValues."";
	}
	if($return_query==1){return $strQuery ;}

		return $strQuery;die;
	global $con;
	if( strpos($strQuery, "WHERE")==false)  return "0";
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	
	if ($exestd){user_activities($exestd);}
	if ($exestd)
		return "1";
	else
		return "0";

	die;
	if ( $commit==1 )
	{
		if (!oci_error($stid))
		{
			oci_commit($con);
			return "1";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else
		return 1;
	die;
}

if ($action=="receive_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	?>
	<script>
		function js_set_value(id)
		{ 
			$("#hidden_mst_id").val(id);
			document.getElementById('selected_job').value=id;
			parent.emailwindow.hide();
		}		
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="searchreceivefrm_1"  id="searchreceivefrm_1" autocomplete="off">
                <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="7"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        </tr>
                    	<tr>                	 
                            <th width="120">Company Name</th>
                            <th width="120">Party Name</th>
                            <th width="70">Receive ID</th>
                            <th width="70">Job No.</th>
                            <th width="70">Challan No</th>
                            <th width="170">Date Range</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>         
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> <input type="hidden" id="selected_job"><? $data=explode("_",$data); ?>  <!--  echo $data;-->
								<? 
									echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "load_drop_down( 'sub_contract_material_receive_controller', this.value, 'load_drop_down_buyer_pop', 'buyer_td' );"); ?>
                            </td>
                            <td id="buyer_td">
								<? 
									echo create_drop_down( "cbo_party_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" ); 
                                ?>
                            </td>
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:57px" placeholder="Receive ID" />
                            </td>
                            <td>
                                <input type="text" name="txt_search_job" id="txt_search_job" class="text_boxes_numeric" style="width:57px" placeholder="Job No." />
                            </td>
                            <td>
                                <input type="text" name="txt_search_challan" id="txt_search_challan" class="text_boxes" style="width:57px" placeholder="Challan" />
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:65px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:65px">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_search_challan').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_search_job').value, 'create_receive_search_list_view', 'search_div', 'sub_contract_material_receive_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
                        </tr>
                        <tr>
                            <td colspan="7" align="center" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                            </td>
                        </tr>
                    </tbody>
                </table>  
                <div id="search_div"></div>  
            </form>
        </div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_receive_search_list_view")
{
	$data=explode('_',$data);
	$search_type =$data[6];
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer_cond=" and a.party_id='$data[1]'"; else $buyer_cond="";
	
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $recieve_date = "and a.subcon_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $recieve_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $recieve_date = "and a.subcon_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $recieve_date ="";
	}
	
	if($search_type==1)
	{
		if ($data[4]!='') $rec_id_cond=" and a.prefix_no_num='$data[4]'"; else $rec_id_cond="";
		if ($data[5]!='') $challan_no_cond=" and a.chalan_no='$data[5]'"; else $challan_no_cond="";
		if ($data[7]!='') $job_no_cond=" and d.job_no_prefix_num='$data[7]'"; else $job_no_cond="";
	}
	else if($search_type==4 || $search_type==0)
	{
		if ($data[4]!='') $rec_id_cond=" and a.prefix_no_num like '%$data[4]%'"; else $rec_id_cond="";
		if ($data[5]!='') $challan_no_cond=" and a.chalan_no like '%$data[5]%'"; else $challan_no_cond="";
		if ($data[7]!='') $job_no_cond=" and d.job_no_prefix_num like '%$data[7]%'"; else $job_no_cond="";
	}
	else if($search_type==2)
	{
		if ($data[4]!='') $rec_id_cond=" and a.prefix_no_num like '$data[4]%'"; else $rec_id_cond="";
		if ($data[5]!='') $challan_no_cond=" and a.chalan_no like '$data[5]%'"; else $challan_no_cond="";
		if ($data[7]!='') $job_no_cond=" and d.job_no_prefix_num like '$data[7]%'"; else $job_no_cond="";
	}
	else if($search_type==3)
	{
		if ($data[4]!='') $rec_id_cond=" and a.prefix_no_num like '%$data[4]'"; else $rec_id_cond="";
		if ($data[5]!='') $challan_no_cond=" and a.chalan_no like '%$data[5]'"; else $challan_no_cond="";
		if ($data[7]!='') $job_no_cond=" and d.job_no_prefix_num like '%$data[7]'"; else $job_no_cond="";
	}	
	
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
	$arr=array (2=>$party_arr,5=>$item_category);
	
	if($db_type==0)
	{
		$year_cond= "year(a.insert_date)as year";
	}
	else if($db_type==2)
	{
		$year_cond= "TO_CHAR(a.insert_date,'YYYY') as year";
	}
	if($db_type==0)
	{
		$sql= "select a.id, a.sys_no, a.prefix_no_num, $year_cond, a.location_id, a.party_id, a.subcon_date, a.chalan_no, a.remarks, a.status_active, group_concat(distinct(b.order_id)) as order_id, sum(b.quantity) as quantity, group_concat(distinct(c.job_no_mst)) as job_no_mst from sub_material_mst a, sub_material_dtls b ,subcon_ord_dtls c, subcon_ord_mst d where a.id=b.mst_id and c.id=b.order_id and c.job_no_mst=d.subcon_job and c.mst_id=d.id and a.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=2 and b.is_deleted=0 $recieve_date $company $buyer_cond $rec_id_cond $challan_no_cond $job_no_cond group by a.id order by a.id DESC ";
	}
	else if ($db_type==2)
	{
		$sql= "select a.id, a.sys_no, a.prefix_no_num, $year_cond, a.location_id, a.party_id, a.subcon_date, a.chalan_no, a.remarks, a.status_active, listagg(b.order_id,',') within group (order by b.order_id) as order_id, sum(b.quantity) as quantity, listagg(c.job_no_mst,',') within group (order by c.job_no_mst) as job_no_mst from sub_material_mst a, sub_material_dtls b,subcon_ord_dtls c , subcon_ord_mst d where a.id=b.mst_id  and c.id=b.order_id and c.id=b.order_id and c.job_no_mst=d.subcon_job and c.mst_id=d.id and a.entry_form=288 and a.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=2 and b.is_deleted=0 $recieve_date $company $buyer_cond $rec_id_cond $challan_no_cond $job_no_cond group by a.id, a.sys_no, a.prefix_no_num, a.insert_date, a.location_id, a.party_id, a.subcon_date, a.chalan_no, a.remarks, a.status_active order by a.id DESC ";
	}
	//echo $sql; 
	$result = sql_select($sql);
	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="817" class="rpt_table">
            <thead>
                <th width="40" >SL</th>
                <th width="70" >Receive No</th>
                <th width="70" >Year</th>
                <th width="120" >Party Name</th>
                <th width="100" >Challan No</th>
                <th width="80" >Receive Date</th>
                <th width="100" >Job No.</th>
                <th width="100">Order No</th>
                <th>Receive Qty</th>
            </thead>
     	</table>
     <div style="width:820px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$order_no='';
				$order_id=array_unique(explode(",",$row[csf("order_id")]));
				$job_no_mst=implode(",",array_unique(explode(",",$row[csf("job_no_mst")])));

				foreach($order_id as $val)
				{
					if($order_no=="") $order_no=$po_arr[$val]; else $order_no.=",".$po_arr[$val];
				}
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf("id")];?>);" > 
						<td width="40" align="center"><? echo $i; ?></td>
						<td width="70" align="center"><? echo $row[csf("prefix_no_num")]; ?></td>
                        <td width="70" align="center"><? echo $row[csf("year")]; ?></td>
                        <td width="120" align="center"><? echo $party_arr[$row[csf("party_id")]]; ?></td>		
						<td width="100" align="center"><? echo $row[csf("chalan_no")]; ?></td>
						<td width="80"><? echo change_date_format($row[csf("subcon_date")]);  ?></td>
						<td width="100"><? echo $job_no_mst; ?></td>	
						<td width="100"><p><? echo $order_no; ?></p></td>
						<td><p><? echo $row[csf("quantity")]; ?></p></td>
					</tr>
				<? 
				$i++;
            }
   		?>
			</table>
		</div>
     </div>
     <?	
	exit();
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id, sys_no, company_id, location_id, party_id, subcon_date, chalan_no, status_active from  sub_material_mst where id='$data'" ); 
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_receive_no').value 		= '".$row[csf("sys_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value 	= '".$row[csf("company_id")]."';\n";
		echo "$('#cbo_company_name').attr('disabled','true')".";\n"; 
		echo "load_drop_down( 'requires/sub_contract_material_receive_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n"; 		echo "document.getElementById('cbo_location_name').value	= '".$row[csf("location_id")]."';\n";  
		echo "load_drop_down( 'requires/sub_contract_material_receive_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_buyer', 'buyer_td' );\n";
		echo "get_php_form_data( '".$row[csf("company_id")]."', 'load_report_button_setting','requires/sub_contract_material_receive_controller' );\n";
		echo "document.getElementById('cbo_party_name').value		= '".$row[csf("party_id")]."';\n";
		echo "$('#cbo_party_name').attr('disabled','true')".";\n"; 
		echo "document.getElementById('txt_receive_challan').value	= '".$row[csf("chalan_no")]."';\n"; 
		echo "document.getElementById('txt_receive_date').value 	= '".change_date_format($row[csf("subcon_date")])."';\n";  
	    echo "document.getElementById('update_id').value            = '".$row[csf("id")]."';\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_material_receive',1);\n";
	}
	exit();
}

if($action=="subcontract_receive_dtls_list_view")
{	
	$color_arrey=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$size_arrey=return_library_array( "select id,size_name from  lib_size where status_active=1 and is_deleted=0",'id','size_name');
	//$order_arr=return_library_array( "select id, order_no from subcon_ord_dtls", "id", "order_no");
	$sql_ord=sql_select("select id, order_no, cust_buyer, cust_style_ref from subcon_ord_dtls");
	
	$order_arr=array(); $custbuyerArr=array(); $custStyleArr=array();
	foreach($sql_ord as $row)
	{
		$order_arr[$row[csf("id")]]=$row[csf("order_no")];
		$custbuyerArr[$row[csf("id")]]=$row[csf("cust_buyer")];
		$custStyleArr[$row[csf("id")]]=$row[csf("cust_style_ref")];
	}
	unset($sql_ord);
	
	$sql = "select id, order_id, item_category_id, material_description, color_id, size_id, gsm, quantity, subcon_uom, subcon_roll, grey_dia,mc_dia,mc_gauge, fin_dia, rec_cone,stitch_length ,used_yarn_details,brand,lot_no,acc_item_colour,acc_item_size from sub_material_dtls where status_active=2 and mst_id='$data'"; 

	// echo $sql;
		
	$arr=array(0=>$order_arr,1=>$custbuyerArr,2=>$custStyleArr, 3=>$item_category,5=>$color_arrey,8=>$size_arrey,17=>$unit_of_measurement);
	echo  create_list_view("list_view", "Order No,Cust. Buyer,Cust. Style,Item Category,Lot,Brand,Material Description,Color,GMTS Size, Item Color,Item Size, GSM, Grey Dia/Width, M/C Dia, M/C Gauge, Fin. Dia/Width,Receive Qty,UOM,Roll,Cone,Stitch Length,Used Yarn Desc.", "80,60,60,80,80,80,130,70,70,70,60,60,60,60,60,60,70,60,60,60,60,80","1580","250",0, $sql, "get_php_form_data", "id", "'load_php_data_to_form_dtls'", 1, "order_id,order_id,order_id,item_category_id,0,0,0,color_id,size_id,0,0,0,0,0,0,0,0,subcon_uom,0,0,0,0", $arr,"order_id,order_id,order_id,item_category_id,lot_no,brand,material_description,color_id,size_id,acc_item_colour,acc_item_size,gsm,grey_dia,mc_dia,mc_gauge,fin_dia,quantity,subcon_uom,subcon_roll,rec_cone,stitch_length,used_yarn_details", "requires/sub_contract_material_receive_controller", "", "0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,2,0,2,0,0,0",'0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,quantity,0,subcon_roll,0,0,0','');     
	exit();
}

if($action=="order_dtls_list_view")
{	
	$order_id=$data;
	$item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');	
	$size_arr=return_library_array( "select id,size_name from  lib_size",'id','size_name');
	
	
	
	//if($db_type==0) $color_id_str="group_concat(b.color_id)";
	//else if($db_type==2) $color_id_str="listagg(b.color_id,',') within group (order by b.color_id)";
	
	//$sql = "select a.main_process_id, b.item_id, $color_id_str as color_id, sum(b.qnty) as qnty from subcon_ord_dtls a, subcon_ord_breakdown b where a.id=b.order_id and a.id='$order_id' group by a.main_process_id, b.item_id";
	$previus_rec_qty_arr=array();
	$pre_rec_sql="select b.material_description, b.fin_dia, sum(b.quantity) as qty,color_id from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.order_id='$order_id' and a.trans_type=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=2 group by b.material_description,color_id,  b.fin_dia";
	//echo $pre_rec_sql;
	$pre_rec_arr=sql_select($pre_rec_sql);
	 foreach($pre_rec_arr as $row)
	 {
		 $previus_rec_qty_arr[$row[csf('material_description')]][$row[csf('fin_dia')]][$row[csf('color_id')]]=$row[csf('qty')];
	 }
	//print_r($previus_rec_qty_arr);
	 unset($pre_rec_arr);
	
	$sql = "select a.main_process_id, b.item_id, b.color_id, b.size_id, avg(b.rate) as rate, sum(b.qnty) as qnty, b.gsm, b.grey_dia, b.finish_dia from subcon_ord_dtls a, subcon_ord_breakdown b where a.id=b.order_id and a.id='$order_id' group by  a.main_process_id, b.item_id, b.color_id, b.gsm, b.grey_dia, b.finish_dia, b.size_id";

	$data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="350">
        <thead>
            <th width="15">SL</th>
            <th width="120">Fabric Description</th>
            <th width="50">GSM</th>
            <th width="80">Color</th>
            <th>Order Qty</th>
        </thead>
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$process_id=$row[csf('main_process_id')];
				if($process_id==2 || $process_id==3 || $process_id==4 || $process_id==6 || $process_id==7 || $process_id==26)
				{
					$item_name=$item_arr[$row[csf('item_id')]];	
					$gsm_val=$gsm_arr[$row[csf('item_id')]];	
				}
				else
				{
					$item_name=$garments_item[$row[csf('item_id')]];
					$gsm_val='';
				}
				$excolor_id=array_unique(explode(",",$row[csf('color_id')]));
				$color_name="";	
				foreach ($excolor_id as $color_id)
				{
					if($color_name=="") $color_name=$color_arr[$color_id]; else $color_name.=','.$color_arr[$color_id];
				}
				$prev_rec_qty=$row[csf('qnty')]-$previus_rec_qty_arr[$item_name][$row[csf('finish_dia')]][$color_id];
				//if('Single Jersey Lycra Spandex 5% Cotton 95%'==$item_name) echo "555";
				//echo $row[csf('qnty')].'='.$previus_rec_qty_arr[$item_name];
             ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $row[csf('item_id')]."**".$item_name."**".$row[csf('color_id')]."**".$row[csf('gsm')]."**".$row[csf('grey_dia')]."**".$row[csf('finish_dia')]."**".$size_arr[$row[csf('size_id')]]."**".$row[csf('main_process_id')]."**".$row[csf('rate')]."**".$row[csf('qnty')]."**".$prev_rec_qty; ?>")' style="cursor:pointer" >
                    <td><? echo $i; ?></td>
                    <td><? echo $item_name; ?></td>
                    <td><? echo $row[csf('gsm')]; ?></td>
                    <td><? echo $color_name; ?></td>
                    <td align="right"><? echo number_format($row[csf('qnty')]); ?></td>
                </tr>
            <? 
            $i++; 
            } 
            ?>
        </tbody>
    </table>
<?    
	exit();

} 

if ($action=="load_php_data_to_form_dtls")
{
	//$order_sql=sql_select( "select id, order_no, main_process_id, order_quantity from subcon_ord_dtls");
	$order_sql=sql_select( "select b.id, b.order_no, b.main_process_id, b.order_quantity,c.color_id,c.item_id,c.qnty,c.gsm,c.grey_dia,c.finish_dia from subcon_ord_dtls b,subcon_ord_breakdown c where b.id=c.order_id");
	$order_arr=array();
	foreach($order_sql as $row)
	{
		$order_arr[$row[csf("id")]]['po']=$row[csf("order_no")];
		$order_arr[$row[csf("id")]]['main_process_id']=$row[csf("main_process_id")];
		$order_arr[$row[csf("id")]]['quantity']=$row[csf("order_quantity")];
		//$order_arr_color[$row[csf("id")]][$row[csf("color_id")]]['quantity']=$row[csf("qnty")];
		$order_arr_color[$row[csf("id")]][$row[csf("color_id")]][$row[csf("gsm")]][$row[csf("finish_dia")]]['quantity']+=$row[csf("qnty")];
	}
	unset($order_sql);
	$pre_rec_qty_arr=array();
	
	$sql_pre_rec="select order_id, material_description,color_id,brand,gsm, quantity as qty from sub_material_dtls where id!='$data' and status_active=2 ";
	$sql_pre_rec_res=sql_select($sql_pre_rec);
	foreach ($sql_pre_rec_res as $row)
	{
		$pre_rec_qty_arr[$row[csf("order_id")]][$row[csf("material_description")]][$row[csf("color_id")]]+=$row[csf("qty")];
	}
	unset($sql_pre_rec_res);
	
	$nameArray=sql_select( "select id, item_category_id, material_description, color_id, size_id, quantity, rate, subcon_uom, subcon_roll, rec_cone, gsm, grey_dia,mc_dia,mc_gauge, fin_dia, dia_uom, order_id, status_active,stitch_length,used_yarn_details,lot_no,brand,acc_item_colour,acc_item_size from sub_material_dtls where id='$data'" );
	foreach ($nameArray as $row)
	{	

		echo "hide_material_description(".$row[csf("item_category_id")].");\n";
		if($row[csf("item_category_id")]==30)
		{
			$garments=array_flip($garments_item);
			$indexof_material_description=$garments[$row[csf("material_description")]];
			echo "document.getElementById('cbo_gmts_material_description').value	= '$indexof_material_description';\n";
			echo "document.getElementById('txt_material_description').value	= '';\n";
		}
		else
		{
			echo "document.getElementById('txt_material_description').value	= '".$row[csf("material_description")]."';\n";
			echo "document.getElementById('cbo_gmts_material_description').value	= '0';\n";
		}

		echo "document.getElementById('cbo_item_category').value		= '".$row[csf("item_category_id")]."';\n"; 
		echo "document.getElementById('txtsize').value				= '".$size_arr[$row[csf("size_id")]]."';\n";
		echo "document.getElementById('txt_receive_quantity').value		= '".$row[csf("quantity")]."';\n";  
		echo "document.getElementById('txt_rec_rate').value				= '".$row[csf("rate")]."';\n";  
		echo "document.getElementById('cbo_uom').value		 			= '".$row[csf("subcon_uom")]."';\n";
		echo "document.getElementById('txt_acc_item_colour').value		= '".$row[csf("acc_item_colour")]."';\n";
		echo "document.getElementById('txt_acc_item_size').value		= '".$row[csf("acc_item_size")]."';\n";
		echo "document.getElementById('txt_roll').value		 			= '".$row[csf("subcon_roll")]."';\n";
		echo "document.getElementById('txt_cone').value		 			= '".$row[csf("rec_cone")]."';\n";
		echo "document.getElementById('txt_gsm').value		 			= '".$row[csf("gsm")]."';\n";
		echo "document.getElementById('txt_grey_dia').value		 		= '".$row[csf("grey_dia")]."';\n";
		echo "document.getElementById('txt_mc_dia').value		 		= '".$row[csf("mc_dia")]."';\n";
		echo "document.getElementById('txt_mc_gauge').value		 		= '".$row[csf("mc_gauge")]."';\n";

		echo "document.getElementById('txt_fin_dia').value		 		= '".$row[csf("fin_dia")]."';\n";
		echo "document.getElementById('cbo_dia_uom').value		 		= '".$row[csf("dia_uom")]."';\n";
		echo "document.getElementById('txt_order_no').value		 		= '".$order_arr[$row[csf("order_id")]]['po']."';\n";  
		echo "document.getElementById('cbo_status').value				= '".$row[csf("status_active")]."';\n"; 
        echo "document.getElementById('txt_used_yarn_details').value	= '".$row[csf("used_yarn_details")]."';\n"; 
        echo "document.getElementById('txt_stitch_length').value		= '".$row[csf("stitch_length")]."';\n"; 
		
		echo "$('#txt_order_no').attr('main_process_id','".$order_arr[$row[csf("order_id")]]['main_process_id']."');\n"; 
		
		//$rec_bal=($order_arr_color[$row[csf("order_id")]][$row[csf("color_id")]]['quantity']-$pre_rec_qty_arr[$row[csf("order_id")]][$row[csf("material_description")]]);
		$rec_bal=($order_arr_color[$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("gsm")]][$row[csf("fin_dia")]]['quantity']-$pre_rec_qty_arr[$row[csf("order_id")]][$row[csf("material_description")]][$row[csf("color_id")]]);
		echo "$('#txt_receive_quantity').attr('previous_rec_qty','".$rec_bal."');\n";   
		
		//echo "document.getElementById('cbo_uom').value            	= '".$row[csf("mst_id")]."';\n";
		echo "document.getElementById('update_id2').value            	= '".$row[csf("id")]."';\n";
		echo "document.getElementById('order_no_id').value				= '".$row[csf("order_id")]."';\n"; 
		
		echo "load_drop_down( 'requires/sub_contract_material_receive_controller','".$row[csf("order_id")]."','load_drop_down_color_for_ord', 'color_td');";
		echo "document.getElementById('txt_color').value				= '".$row[csf("color_id")]."';\n";
		echo "document.getElementById('txt_lot_no').value				= '".$row[csf("lot_no")]."';\n";
		echo "document.getElementById('txt_brand').value				= '".$row[csf("brand")]."';\n";
		
		$del_sql="select a.batch_no, b.prod_id from  pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form=36 and b.po_id='".$row[csf("order_id")]."' and b.prod_id='".$row[csf("id")]."' group by a.batch_no, b.prod_id";
		$result_del_sql=sql_select($del_sql,1);
		if(count($result_del_sql)>0)
		{
			echo "document.getElementById('delete_allowed').value            	= '".'1'."';\n";
			echo "document.getElementById('batch_no').value            	= '".$result_del_sql[0][csf('batch_no')]."';\n";
		}
		else
		{
			echo "document.getElementById('delete_allowed').value            	= '".'0'."';\n";
			echo "document.getElementById('batch_no').value            	= '".''."';\n";
		}

		//echo "change_uom('".$row[csf("item_category_id")]."');\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_material_receive',1);\n";	
	}
	
	  /*echo "document.getElementById('txt_material_description').value	= '".$row[csf("material_description")]."';\n";*/

	
	exit();	
}

if ($action=="load_php_data_allow_delete")
{
	//echo $data;
	$ex_data=explode('_',$data);
	$del_sql="select prod_id from pro_batch_create_dtls where po_id='$ex_data[1]' and prod_id='$ex_data[0]'";
	$result_del_sql=sql_select($del_sql,1);
	if(count($result_del_sql)>0)
	{
		echo "1"."_".$result_del_sql[0][csf('prod_id')];
	}
	else
	{
		echo "0"."_";
	}
	exit();
}

if ($action=="job_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('selected_order').value=id;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
                <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="6"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        </tr>
                    	<tr>               	 
                            <th width="140">Company Name</th>
                            <th width="140">Party Name</th>
                            <th width="170">Date Range</th>
                            <th width="100">Search Job</th>
                            <th width="100">Search Order</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>         
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td><input type="hidden" id="selected_order">  
								<?   
									$data=explode("_",$data);
									echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $data[1],"",1 );
                                ?>
                            </td>
                            <td id="buyer_td">
								<? echo create_drop_down( "cbo_party_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[1]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",$data[3], "",1 );   	 
								?>
                            </td>
                            <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td> 
                            <td>
								<input type="text" name="txt_search_job" id="txt_search_job" class="text_boxes" style="width:100px" placeholder="Job" />
                            </td>
                            <td id="search_by_td">
                                <input type="text" name="txt_search_order" id="txt_search_order" class="text_boxes" style="width:100px" placeholder="Order" />
                            </td>
                            <td>
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_job').value+'_'+document.getElementById('txt_search_order').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_job_search_list_view', 'search_div', 'sub_contract_material_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" align="center" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                            </td>
                        </tr>
                    </tbody>
                </table> 
                <div id="search_div"></div>   
            </form>
        </div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_job_search_list_view")
{	
	$data=explode('_',$data);
	$search_job=str_replace("'","",$data[4]);
	$search_order=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	
	if ($data[0]!=0) $company=" and company_id='$data[0]'"; else  $company="";
	if ($data[1]!=0) $buyer=" and party_id='$data[1]'"; else $buyer="";
	
	if($search_type==1)
	{
		if($search_job=='') $search_job_cond=""; else $search_job_cond="and a.job_no_prefix_num='$search_job'";  
		if($search_order=='') $search_order_cond=""; else $search_order_cond=" and b.order_no='$search_order'";
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_job=='') $search_job_cond=""; else $search_job_cond="and a.job_no_prefix_num like '%$search_job%'";  
		if($search_order=='') $search_order_cond=""; else $search_order_cond=" and b.order_no like '%$search_order%'";
	}
	else if($search_type==2)
	{
		if($search_job=='') $search_job_cond=""; else $search_job_cond="and a.job_no_prefix_num like '$search_job%'";  
		if($search_order=='') $search_order_cond=""; else $search_order_cond=" and b.order_no like '$search_order%'";
	}
	else if($search_type==3)
	{
		if($search_job=='') $search_job_cond=""; else $search_job_cond="and a.job_no_prefix_num like '%$search_job'";  
		if($search_order=='') $search_order_cond=""; else $search_order_cond=" and b.order_no like '%$search_order'";
	}	
	
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and b.order_rcv_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $order_rcv_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and b.order_rcv_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $order_rcv_date ="";
	}
	
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	//$color_lib_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	//$arr=array (3=>$comp,6=>$yes_no,7=>$color_lib_arr);
	if($db_type==0)
	{
		//$sql= "select a.id, b.id as ord_id, a.subcon_job, a.job_no_prefix_num, year(a.insert_date)as year, a.company_id, a.location_id, a.party_id, a.status_active, b.id, b.order_no, b.order_rcv_date, b.delivery_date, b.status_active, b.grey_req from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and b.status_active=1 $order_rcv_date $company $buyer $search_job_cond $search_order_cond order by a.id DESC";
		$color_id_str="group_concat(c.color_id)";
		$sql= "select a.id, b.id as ord_id, a.subcon_job, a.job_no_prefix_num, year(a.insert_date)as year, a.company_id, a.location_id, a.party_id,
		 a.status_active, b.id, b.main_process_id, b.order_no, b.order_rcv_date, b.delivery_date, b.status_active, b.grey_req, b.cust_buyer, b.cust_style_ref, $color_id_str as color_id 
		 from subcon_ord_mst a, subcon_ord_dtls b,subcon_ord_breakdown c  
		 where   a.subcon_job=b.job_no_mst and a.status_active=1 and b.status_active=1 $order_rcv_date $company $buyer $search_job_cond $search_order_cond and  b.id=c.order_id 
		 group by  a.id, b.id, a.subcon_job, a.job_no_prefix_num, year(a.insert_date), a.company_id, a.location_id, a.party_id,a.status_active, b.id, b.order_no,
		 b.order_rcv_date, b.delivery_date, b.status_active, b.grey_req, b.main_process_id, b.cust_buyer, b.cust_style_ref 
		 order by a.id DESC";
	}
	else if($db_type==2)
	{
		$color_id_str="listagg(c.color_id,',') within group (order by c.color_id)";
		//$sql= "select a.id, b.id as ord_id, a.subcon_job, a.job_no_prefix_num, TO_CHAR(a.insert_date,'YYYY') as year, a.company_id, a.location_id, a.party_id, a.status_active, b.id, b.order_no, b.order_rcv_date, b.delivery_date, b.status_active, b.grey_req from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and b.status_active=1 $order_rcv_date $company $buyer $search_job_cond $search_order_cond order by a.id DESC";
		 $sql= "select a.id, b.id as ord_id, a.subcon_job, a.job_no_prefix_num, TO_CHAR(a.insert_date,'YYYY') as year, a.company_id, a.location_id, a.party_id,
		 a.status_active, b.id, b.order_no, b.main_process_id, b.order_rcv_date, b.delivery_date, b.status_active, b.grey_req, b.cust_buyer, b.cust_style_ref, $color_id_str as color_id  
		 from subcon_ord_mst a, subcon_ord_dtls b,subcon_ord_breakdown c  
		 where  a.subcon_job=b.job_no_mst and a.status_active=1 and b.status_active=1 $order_rcv_date $company $buyer $search_job_cond $search_order_cond and  b.id=c.order_id  
		 group by  a.id, b.id, a.subcon_job, a.job_no_prefix_num, TO_CHAR(a.insert_date,'YYYY'), a.company_id, a.location_id, a.party_id,a.status_active, b.id, b.order_no,
		  b.order_rcv_date, b.delivery_date, b.status_active, b.grey_req, b.main_process_id, b.cust_buyer, b.cust_style_ref
		 order by a.id DESC";
		// a.entry_form=238

	}
	//echo $sql;die;

	$data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="885">
        <thead>
            <th width="25">SL</th>
            <th width="50">Job No</th>
            <th width="50">Year</th>
            <th width="100">Order No</th>
            <th width="100">Process</th>
            <th width="80">Cust. Style</th>
            <th width="80">Cust. Buyer</th>
            <th width="80">Ord Receive Date</th>
            <th width="80">Delivery Date</th>
            <th width="80">Is Grey Req</th>
            <th>Color</th>
        </thead>
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$excolor_id=array_unique(explode(",",$row[csf('color_id')]));
				$color_name="";	
				foreach ($excolor_id as $color_id)
				{
					if($color_name=="") $color_name=$color_arr[$color_id]; else $color_name.=','.$color_arr[$color_id];
				}
				 ?>
				  <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('ord_id')]."_".$row[csf('grey_req')]; ?>")' style="cursor:pointer" >
                      <td width="25"><? echo $i; ?></td>
	                  <td width="50" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
	                  <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
	                  <td width="100" style="word-break:break-all"><? echo $row[csf('order_no')]; ?></td>
	                  <td width="100" style="word-break:break-all"><? echo $production_process[$row[csf('main_process_id')]]; ?></td>
	                  <td width="80" style="word-break:break-all"><? echo $row[csf('cust_style_ref')]; ?></td>
                      <td width="80" style="word-break:break-all"><? echo $row[csf('cust_buyer')]; ?></td>
	                  <td width="80" align="center"><? echo change_date_format($row[csf('order_rcv_date')]); ?></td>
	                  <td width="80" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
	                  <td width="80" align="center"><? echo $yes_no[$row[csf('grey_req')]]; ?></td>
	                  <td style="word-break:break-all"><? echo $color_name; ?></td>
                </tr>
				<? 
                $i++; 
            } 
            ?>
        </tbody>
    </table>
<?    
	//echo  create_list_view("list_view", "Job No,Year,Order No,Company,Ord Receive Date,Delivery Date,Is Grey Req.,Color","60,60,150,150,80,80,80","885","250",0,$sql, "js_set_value","ord_id,grey_req","",1,"0,0,0,company_id,0,0,grey_req,0",$arr,"job_no_prefix_num,year,order_no,company_id,order_rcv_date,delivery_date,grey_req,color_id", "",'','0,0,0,0,3,3,0,0') ;
	exit();
} 

if($action=="load_php_data_to_form_dtls_order")
{
	$nameArray=sql_select( "select id,order_no,order_uom from subcon_ord_dtls where id='$data'" );
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_order_no').value	= '".$row[csf("order_no")]."';\n"; 
		echo "document.getElementById('order_no_id').value	= '".$row[csf("id")]."';\n"; 
		echo "document.getElementById('cbo_uom').value		= '".$row[csf("order_uom")]."';\n";
	}
	exit();
}

if($action=="load_drop_down_color_for_ord")
{
	$color_arr = return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');

    $color_for_ord=sql_select("select color_id from subcon_ord_breakdown where order_id='$data'");
    foreach ($color_for_ord as $value) 
    {
    	
    		$selected_colors_arr[]=$value[csf('color_id')];
    	
    }
    if(count(array_unique($selected_colors_arr))==1)
    {
    	$selected_colors=implode(",", array_unique($selected_colors_arr));
		echo create_drop_down( "txt_color", 60, $color_arr,"", 1, "-Select-",$selected_colors,"", "","$selected_colors" );
    }
    else
    {
    	$selected_colors=implode(",", array_unique($selected_colors_arr));
		echo create_drop_down( "txt_color", 60, $color_arr,"", 1, "-Select-",0,"", "","$selected_colors" );
    }
	exit();
}
if($action=="show_material_receive_report")
{
	extract($_REQUEST);
	 $receive_no=str_replace("'","",$txt_receive_no);
	 $update_id=str_replace("'","",$update_id);
	 $company_name=str_replace("'","",$cbo_company_name);
	 $location_name=str_replace("'","",$cbo_location_name);
	 $party_name=str_replace("'","",$cbo_party_name);
	 $receive_challan=str_replace("'","",$txt_receive_challan);
	 $receive_date=str_replace("'","",$txt_receive_date);



	$company_arr=return_library_array( "select id,company_name from   lib_company",'id','company_name');
	$location_arr=return_library_array( "select id,location_name from   lib_location",'id','location_name');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$order_sql=sql_select( "select b.id, b.order_no, b.main_process_id, b.order_quantity,c.color_id,c.item_id,c.qnty,c.gsm,c.grey_dia,c.finish_dia from subcon_ord_dtls b,subcon_ord_breakdown c where b.id=c.order_id");
	$order_arr=array();
	foreach($order_sql as $row)
	{
		$order_arr[$row[csf("id")]]['po']=$row[csf("order_no")];
		$order_arr[$row[csf("id")]]['main_process_id']=$row[csf("main_process_id")];
		$order_arr[$row[csf("id")]]['quantity']=$row[csf("order_quantity")];
		//$order_arr_color[$row[csf("id")]][$row[csf("color_id")]]['quantity']=$row[csf("qnty")];
		$order_arr_color[$row[csf("id")]][$row[csf("color_id")]][$row[csf("gsm")]][$row[csf("finish_dia")]]['quantity']+=$row[csf("qnty")];
	}
	unset($order_sql);

	?>
	<div style="width: 1000px;" align="center">
	   <table border="1" align="left" style=" margin:15px;"  cellpadding="0" width="600" cellspacing="0" rules="all" >
          
                        <tr>
                            <td align="center" colspan="4"><h1> <?=$company_arr[$company_name];?></h1></td>
                        </tr>
						<tr>
              			  <td colspan="4" align="center" style="font-size:16px">
							<?
								$nameArray=sql_select( "select a.plot_no,b.address,a.level_no, a.road_no, a.block_no, a.country_id, a.province, a.city, a.zip_code, a.email, a.website, a.vat_number from lib_company a,lib_location b where a.id=b.company_id and  a.id=$company_name and b.id=$location_name and a.status_active=1 and a.is_deleted=0"); 					
								foreach ($nameArray as $result)
								{ 
								?>
									<? //echo $result[csf('plot_no')]; ?>
									<? //echo $result[csf('level_no')]; ?>
									<? //echo $result[csf('road_no')]; ?>
									<? //echo $result[csf('block_no')];?> 
									<? //echo $result[csf('city')]; ?>
									<? //echo $result[csf('zip_code')]; ?>
									<? echo $result[csf('address')]; ?>
									<? echo $result[csf('email')];?><br>
									<?
								}
							?> 
						</td>
					</tr>           
                        <tr>
                            <th align="left"  width="100px" style="font-size:16px">Company Name </th>                          
                            <td align="left"  width="100px" style="font-size:16px">: <?= $company_arr[$company_name];?></td>
							<th align="left"  width="100px" style="font-size:16px">Receive ID</th>                          
                            <td align="left"  width="100px" style="font-size:16px">:<?= $receive_no;?></td>
                     </tr>
					 <tr>
                            <th align="left" style="font-size:16px">Location </th>                          
                            <td align="left" style="font-size:16px">:<?= $location_arr[$location_name];?></td>
							<th align="left" style="font-size:16px">Receive Challan </th>                          
                            <td align="left" style="font-size:16px">:<?=$receive_challan;?></td>
                     </tr>
					 <tr>
                            <th align="left" style="font-size:16px">Party </th>                          
                            <td align="left" style="font-size:16px">:<?=$buyer_name_arr[$party_name];?></td>
							<th align="left" style="font-size:16px">Receive Date </th>                          
                            <td align="left" style="font-size:16px">:<?= $receive_date;?></td>
                     </tr>
                
        </table>
		
		<table  align="left" cellspacing="0" style=" margin:15px;" width="1000"  border="1" rules="all"  >
			
					
					<thead  bgcolor="#dddddd">
						<td align="left" style="border:1px solid black;font-size:20px" colspan="21"><strong>Metarial Details</strong></td>     
                     </thead>
					<thead  bgcolor="#dddddd">
							<th align="left" style="width:30px;border:1px solid black;font-size:16px">SL</th>     
                            <th align="left" style="width:100px;border:1px solid black;font-size:16px">Order No</th>                          
                            <th align="left" style="width:80px;border:1px solid black;font-size:16px">Item Category</th>
							<th align="left" style="width:120px;border:1px solid black;font-size:16px">Material Description</th>                          
                            <th align="left" style="width:60px;border:1px solid black;font-size:16px">Lot No.</th>
							<th align="left" style="width:60px;border:1px solid black;font-size:16px">Brand</th>
							<th align="left" style="width:80px;border:1px solid black;font-size:16px">Color</th>                          
                            <th align="left" style="width:80px;border:1px solid black;font-size:16px">GMTS Size</th>
							<th align="left" style="width:60px;border:1px solid black;font-size:16px">GSM</th>                          
                            <th align="left" style="width:60px;border:1px solid black;font-size:16px">Stitch Length</th>
							<th align="left" style="width:70px;border:1px solid black;font-size:16px">Grey Dia/ Width</th>                          
                            <th align="left" style="width:70px;border:1px solid black;font-size:16px">M/C Dia</th>
							<th align="left" style="width:70px;border:1px solid black;font-size:16px">M/C Gauge</th>                          
                            <th align="left" style="width:70px;border:1px solid black;font-size:16px">Fin. Dia/ Width</th>
							<th align="left" style="width:60px;border:1px solid black;font-size:16px">Dia UOM</th>                          
                            <th align="left" style="width:50px;border:1px solid black;font-size:16px">Roll /Bag</th>
							<th align="left" style="width:70px;border:1px solid black;font-size:16px">Receive Qty</th>                          
                            <th align="left" style="width:70px;border:1px solid black;font-size:16px">Rate</th>
							<th align="left" style="width:60px;border:1px solid black;font-size:16px">UOM</th>
							<th align="left" style="width:60px;border:1px solid black;font-size:16px">Cone</th>                          
                            <th align="left" style="width:60px;border:1px solid black;font-size:16px">Delete</th>
                     </thead>
					 <tbody>
					
				<?
				$nameArray=sql_select( "select id, item_category_id, material_description, color_id, size_id, quantity, rate, subcon_uom, subcon_roll, rec_cone, gsm, grey_dia,mc_dia,mc_gauge, fin_dia, dia_uom, order_id, status_active,stitch_length,used_yarn_details,lot_no,brand from sub_material_dtls where mst_id='$update_id'" );
				$i=1;
				foreach ($nameArray as $row)
				{	?>
					<tr>
							<td align="left" style="width:30px;border:1px solid black;font-size:15px"><?=$i;?></td>     
                            <td align="left" style="width:100px;border:1px solid black;font-size:15px"><?=$order_arr[$row[csf("order_id")]]['po'];?></td>                          
                            <td align="left" style="width:80px;border:1px solid black;font-size:15px"><?=$item_category[$row[csf("item_category_id")]];?></td>
							<td align="left" style="width:120px;border:1px solid black;font-size:15px"><?=$row[csf("material_description")];?></td>                          
                            <td align="left" style="width:60px;border:1px solid black;font-size:15px"><?=$row[csf("lot_no")];?></td>
							<td align="left" style="width:60px;border:1px solid black;font-size:15px"><?=$row[csf("brand")];?></td>
							<td align="left" style="width:80px;border:1px solid black;font-size:15px"><?=$color_arr[$row[csf("color_id")]];?></td>                          
                            <td align="left" style="width:80px;border:1px solid black;font-size:15px"><?=$size_arr[$row[csf("size_id")]];?></td>
							<td align="left" style="width:60px;border:1px solid black;font-size:15px"><?=$row[csf("gsm")];?></td>                          
                            <td align="left" style="width:60px;border:1px solid black;font-size:15px"><?=$row[csf("stitch_length")];?></td>
							<td align="left" style="width:70px;border:1px solid black;font-size:15px"><?=$row[csf("grey_dia")];?></td>                          
                            <td align="left" style="width:70px;border:1px solid black;font-size:15px"><?=$row[csf("mc_dia")];?></td>
							<td align="left" style="width:70px;border:1px solid black;font-size:15px"><?=$row[csf("mc_gauge")];?></td>                          
                            <td align="left" style="width:70px;border:1px solid black;font-size:15px"><?=$row[csf("fin_dia")]?></td>
							<td align="left" style="width:60px;border:1px solid black;font-size:15px"><?=$unit_of_measurement[$row[csf("dia_uom")]]?></td>                          
                            <td align="left" style="width:50px;border:1px solid black;font-size:15px"><?=$row[csf("subcon_roll")];?></td>
							<td align="left" style="width:70px;border:1px solid black;font-size:15px"><?=$row[csf("quantity")];?></td>                          
                            <td align="left" style="width:70px;border:1px solid black;font-size:15px"><?=$row[csf("rate")];?></td>
							<td align="left" style="width:60px;border:1px solid black;font-size:15px"><?=$unit_of_measurement[$row[csf("subcon_uom")]];?></td>
							<td align="left" style="width:60px;border:1px solid black;font-size:15px"><?=$row[csf("rec_cone")]?></td>                          
                            <td align="left" style="width:60px;border:1px solid black;font-size:15px"><?=$yes_no[$row[csf("status_active")]];?></td>
                     </tr>
				<?
				$i++;
							$tot_recv_qnty+=$row[csf("quantity")];
				
				}?>
					<tr>
							<th align="left" style="width:30px;border:1px solid black"></th>     
                            <th align="left" style="width:100px;border:1px solid black"></th>                          
                            <th align="left" style="width:80px;border:1px solid black"></th>
							<th align="left" style="width:120px;border:1px solid black"></th>                          
                            <th align="left" style="width:60px;border:1px solid black"></th>
							<th align="left" style="width:60px;border:1px solid black"></th>
							<th align="left" style="width:80px;border:1px solid black"></th>                          
                            <th align="left" style="width:80px;border:1px solid black"></th>
							<th align="left" style="width:60px;border:1px solid black"></th>    

                            <th align="left" style="width:60px;border:1px solid black"></th>
							<th align="left" style="width:70px;border:1px solid black"></th>                          
                            <th align="left" style="width:70px;border:1px solid black"></th>
							<th align="left" style="width:70px;border:1px solid black"></th>                          
                            <th align="left" style="width:70px;border:1px solid black"></th>
							<th align="left" style="width:60px;border:1px solid black;font-size:16px">Total</th>                          
                            <th align="left" style="width:50px;border:1px solid black"></th>
							<th align="left" style="width:70px;border:1px solid black;font-size:16px"><?=$tot_recv_qnty;?></th>                          
                            <th align="left" style="width:70px;border:1px solid black"></th>
							<th align="left" style="width:60px;border:1px solid black"></th>
							<th align="left" style="width:60px;border:1px solid black"></th>                          
                            <th align="left" style="width:60px;border:1px solid black"></th>
                     </tr>

			</tbody>
 		</table>

    </div>

    <div>
		<?
        	echo signature_table(294, $cbo_company_name, "1000px");
        ?>
    </div>

   
		<script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
   

<?
exit();
}

?>