<?
include('../../../includes/common.php');
session_start();

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	if($data[1]==1) $dropdown_name="cbo_location_name";
	else $dropdown_name="cbo_party_location";
	
	echo create_drop_down( $dropdown_name, 150, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );	
	exit();
}

if ($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);

	if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
	else $load_function="";
	
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $data[2], "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );
	}	
	exit();	 
} 

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$user_id=$_SESSION['logic_erp']['user_id'];
	
	if ($operation==0) // Insert Start Here=================================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		//echo "10**".$operation; die;
		if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
		else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";
		
		$new_job_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'AOP', date("Y",time()), 5, "select job_no_prefix, job_no_prefix_num from subcon_ord_mst where entry_form=278 and company_id=$cbo_company_name $insert_date_con order by id desc ", "job_no_prefix", "job_no_prefix_num" ));

		$id=return_next_id("id","subcon_ord_mst",1);
		$id1=return_next_id("id", "subcon_ord_dtls",1) ;
		//$id=$id1=true;
		$field_array="id, entry_form, subcon_job, job_no_prefix, job_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id, receive_date, delivery_date, rec_start_date, rec_end_date, order_id, order_no,aop_reference, inserted_by, insert_date, status_active, is_deleted";

		$data_array="(".$id.", 278, '".$new_job_no[0]."', '".$new_job_no[1]."', '".$new_job_no[2]."', ".$cbo_company_name.", ".$cbo_location_name.", ".$cbo_within_group.", ".$cbo_party_name.", ".$cbo_party_location.", ".$cbo_currency.", ".$txt_order_receive_date.", ".$txt_delivery_date.",".$txt_rec_start_date.",".$txt_rec_end_date.", ".$hid_order_id.", ".$txt_order_no.", ".$txt_aop_ref.", ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."',1,0)";
		$txt_job_no=$new_job_no[0];
		$field_array2="id, mst_id, job_no_mst, order_id, order_no, buyer_po_id, body_part, construction, composition, gsm, grey_dia, gmts_color_id, item_color_id, fin_dia, aop_color_id, lib_yarn_deter, booking_dtls_id, order_quantity, order_uom, rate, amount, wastage,artwork_no ,design_no , inserted_by, insert_date, status_active, is_deleted";
		
		$color_library_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
		$size_library_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
		$data_array2="";  $add_commaa=0;

		for($i=1; $i<=$total_row; $i++)
		{			
			$txtbuyerPoId			= "txtbuyerPoId_".$i; 
			$cboBodyPart			= "cboBodyPart_".$i;
			$txtConstruction		= "txtConstruction_".$i;
			$txtComposition			= "txtComposition_".$i;
			$txtGsm					= "txtGsm_".$i;
			$txtDia					= "txtDia_".$i;
			$txtGmtsColor 			= "txtGmtsColor_".$i;
			$txtItemColor 			= "txtItemColor_".$i;
			$txtFinDia 				= "txtFinDia_".$i;			
			$txtAopColor 			= "txtAopColor_".$i;
			$txtQty 				= "txtQty_".$i;
			$cboUom 				= "cboUom_".$i;
			$txtRateUnit 			= "txtRateUnit_".$i;
			$txtAmount 				= "txtAmount_".$i;
			$txtProcessLoss 		= "txtProcessLoss_".$i;
			$txtArtwork 			= "txtArtwork_".$i;
			$txtDesignNo 			= "txtDesignNo_".$i;
			$hdnlibyarndetar 		= "hdnlibyarndetar_".$i;
			$hdnbookingDtlsId 		= "hdnbookingDtlsId_".$i;
			$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
			
			if(str_replace("'","",$$txtGmtsColor)!="")
			{ 
				if (!in_array(str_replace("'","",$$txtGmtsColor),$new_arr_color_gmts))
				{
					$gmtsColor_id = return_id( str_replace("'","",$$txtGmtsColor), $color_library_arr, "lib_color", "id,color_name","278");  
					$new_arr_color_gmts[$gmtsColor_id]=str_replace("'","",$$txtGmtsColor);
				}
				else $gmtsColor_id =  array_search(str_replace("'","",$$txtGmtsColor), $new_arr_color_gmts); 
			}
			else $gmtsColor_id=0;
			
			if(str_replace("'","",$$txtItemColor)!="")
			{ 
				if (!in_array(str_replace("'","",$$txtItemColor),$new_arr_color_item))
				{
					$itemColor_id = return_id( str_replace("'","",$$txtItemColor), $color_library_arr, "lib_color", "id,color_name","278");  
					$new_arr_color_item[$itemColor_id]=str_replace("'","",$$txtItemColor);
				}
				else $itemColor_id =  array_search(str_replace("'","",$$txtItemColor), $new_arr_color_item); 
			}
			else $itemColor_id=0;
			
			if(str_replace("'","",$$txtAopColor)!="")
			{ 
				if (!in_array(str_replace("'","",$$txtAopColor),$new_arr_color_aop))
				{
					$aopColor_id = return_id( str_replace("'","",$$txtAopColor), $color_library_arr, "lib_color", "id,color_name","278");  
					$new_arr_color_aop[$aopColor_id]=str_replace("'","",$$txtAopColor);
				}
				else $aopColor_id =  array_search(str_replace("'","",$$txtAopColor), $new_arr_color_aop); 
			}
			else $aopColor_id=0;

			if(str_replace("'",'',$$txtbuyerPoId)=="") $txtbuyerPoId=0; else $txtbuyerPoId=str_replace("'",'',$$txtbuyerPoId);
			if ($add_commaa!=0) $data_array2 .=","; $add_comma=0;
			$field_array2="id, mst_id, job_no_mst, order_id, order_no, buyer_po_id, body_part, construction, composition, gsm, grey_dia, gmts_color_id, item_color_id, fin_dia, aop_color_id, lib_yarn_deter, booking_dtls_id, order_quantity, order_uom, rate, amount, wastage,artwork_no ,design_no , inserted_by, insert_date, status_active, is_deleted";
			$data_array2 .="(".$id1.",".$id.",'".$new_job_no[0]."',".$hid_order_id.",".$txt_order_no.",'".$txtbuyerPoId."',".$$cboBodyPart.",".$$txtConstruction.",".$$txtComposition.",".$$txtGsm.",".$$txtDia.",'".$gmtsColor_id."','".$itemColor_id."',".$$txtFinDia.",'".$aopColor_id."',".$$hdnlibyarndetar.",".$$hdnbookingDtlsId.",".str_replace(",",'',$$txtQty).",".$$cboUom.",".$$txtRateUnit.",".str_replace(",",'',$$txtAmount).",".$$txtProcessLoss.",".$$txtArtwork.",".$$txtDesignNo.",'".$user_id."','".$pc_date_time."',1,0)";
			
			$id1=$id1+1; $add_commaa++;
		}
		//echo "10**INSERT INTO subcon_ord_dtls (".$field_array2.") VALUES ".$data_array2; die;
		$flag=1;
		$rID=sql_insert("subcon_ord_mst",$field_array,$data_array,0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		$rID1=sql_insert("subcon_ord_dtls",$field_array2,$data_array2,0);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		
		if(str_replace("'","",$cbo_within_group)==1)
		{
			$rIDBooking=execute_query( "update wo_booking_mst set lock_another_process=1 where booking_no =".$txt_order_no."",1);
			if($rIDBooking==1 && $flag==1) $flag=1; else $flag=0;
		}
		//echo "10**".$rID."**".$rID1."**".$rIDBooking; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		/*$rec_number=return_field_value( "sys_no", "sub_material_mst"," embl_job_no=$txt_job_no and status_active=1 and is_deleted=0 and trans_type=1");
		if($rec_number){
			echo "aopRec**".str_replace("'","",$txt_job_no)."**".$rec_number;
			die;
		}
		
		$recipe_number=return_field_value( "recipe_no", "pro_recipe_entry_mst"," job_no=$txt_job_no and status_active=1 and is_deleted=0 and entry_form=220");
		if($recipe_number){
			echo "aopRecipe**".str_replace("'","",$txt_job_no)."**".$recipe_number;
			die;
		}*/
		
		$color_library_arr=return_library_array( "select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name"  );
		$size_library_arr=return_library_array( "select id,size_name from lib_size  where status_active=1 and is_deleted=0", "id", "size_name"  );

		$field_array="location_id*within_group*party_id*party_location*currency_id*receive_date*delivery_date*rec_start_date*rec_end_date*order_id*order_no*aop_reference*updated_by*update_date";		
		$data_array="".$cbo_location_name."*".$cbo_within_group."*".$cbo_party_name."*".$cbo_party_location."*".$cbo_currency."*".$txt_order_receive_date."*".$txt_delivery_date."*".$txt_rec_start_date."*".$txt_rec_end_date."*".$hid_order_id."*".$txt_order_no."*".$txt_aop_ref."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		//$field_array2="id, mst_id, job_no_mst, order_id, order_no, buyer_po_id, body_part, construction, composition, gsm, grey_dia, gmts_color_id, item_color_id, fin_dia, aop_color_id, lib_yarn_deter, booking_dtls_id, order_quantity, order_uom, rate, amount, wastage, inserted_by, insert_date, status_active, is_deleted";
		$field_array2="order_id*order_no*buyer_po_id*body_part*construction*composition*gsm*grey_dia*gmts_color_id*item_color_id*fin_dia*aop_color_id*lib_yarn_deter*booking_dtls_id*order_quantity*order_uom*rate*amount*wastage*artwork_no*design_no*updated_by*update_date";
		
		$add_comma=0;	$flag="";
		for($i=1; $i<=$total_row; $i++)
		{			
			$txtbuyerPoId			= "txtbuyerPoId_".$i; 
			$cboBodyPart			= "cboBodyPart_".$i;
			$txtConstruction		= "txtConstruction_".$i;
			$txtComposition			= "txtComposition_".$i;
			$txtGsm					= "txtGsm_".$i;
			$txtDia					= "txtDia_".$i;
			$txtGmtsColor 			= "txtGmtsColor_".$i;
			$txtItemColor 			= "txtItemColor_".$i;
			$txtFinDia 				= "txtFinDia_".$i;			
			$txtAopColor 			= "txtAopColor_".$i;
			$txtQty 				= "txtQty_".$i;
			$cboUom 				= "cboUom_".$i;
			$txtRateUnit 			= "txtRateUnit_".$i;
			$txtAmount 				= "txtAmount_".$i;
			$txtProcessLoss 		= "txtProcessLoss_".$i;
			$txtArtwork 			= "txtArtwork_".$i;
			$txtDesignNo 			= "txtDesignNo_".$i;
			$hdnlibyarndetar 		= "hdnlibyarndetar_".$i;
			$hdnbookingDtlsId 		= "hdnbookingDtlsId_".$i;
			$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
			
			$aa=str_replace("'",'',$$hdnDtlsUpdateId);
			
			if(str_replace("'","",$$txtGmtsColor)!="")
			{ 
				if (!in_array(str_replace("'","",$$txtGmtsColor),$new_arr_color_gmts))
				{
					$gmtsColor_id = return_id( str_replace("'","",$$txtGmtsColor), $color_library_arr, "lib_color", "id,color_name","278");  
					$new_arr_color_gmts[$gmtsColor_id]=str_replace("'","",$$txtGmtsColor);
				}
				else $gmtsColor_id =  array_search(str_replace("'","",$$txtGmtsColor), $new_arr_color_gmts); 
			}
			else $gmtsColor_id=0;
			
			if(str_replace("'","",$$txtItemColor)!="")
			{ 
				if (!in_array(str_replace("'","",$$txtItemColor),$new_arr_color_item))
				{
					$itemColor_id = return_id( str_replace("'","",$$txtItemColor), $color_library_arr, "lib_color", "id,color_name","278");  
					$new_arr_color_item[$itemColor_id]=str_replace("'","",$$txtItemColor);
				}
				else $itemColor_id =  array_search(str_replace("'","",$$txtItemColor), $new_arr_color_item); 
			}
			else $itemColor_id=0;
			
			if(str_replace("'","",$$txtAopColor)!="")
			{ 
				if (!in_array(str_replace("'","",$$txtAopColor),$new_arr_color_aop))
				{
					$aopColor_id = return_id( str_replace("'","",$$txtAopColor), $color_library_arr, "lib_color", "id,color_name","278");  
					$new_arr_color_aop[$aopColor_id]=str_replace("'","",$$txtAopColor);
				}
				else $aopColor_id =  array_search(str_replace("'","",$$txtAopColor), $new_arr_color_aop); 
			}
			else $aopColor_id=0;

			if(str_replace("'",'',$$txtbuyerPoId)=="") $txtbuyerPoId=0; else $txtbuyerPoId=str_replace("'",'',$$txtbuyerPoId);
			
			$data_array2[$aa]=explode("*",("".$hid_order_id."*".$txt_order_no."*'".$txtbuyerPoId."'*".$$cboBodyPart."*".$$txtConstruction."*".$$txtComposition."*".$$txtGsm."*".$$txtDia."*'".$gmtsColor_id."'*'".$itemColor_id."'*".$$txtFinDia."*'".$aopColor_id."'*".$$hdnlibyarndetar."*".$$hdnbookingDtlsId."*".str_replace(",",'',$$txtQty)."*".$$cboUom."*".$$txtRateUnit."*".str_replace(",",'',$$txtAmount)."*".$$txtProcessLoss."*".$$txtArtwork."*".$$txtDesignNo."*".$user_id."*'".$pc_date_time."'"));
			$hdn_dtls_id_arr[]=str_replace("'",'',$$hdnDtlsUpdateId);
		}
		$flag=1;	
		//echo "10**";
		//print_r($data_array2); die;
		//echo "INSERT INTO subcon_ord_breakdown (".$field_array3.") VALUES ".$data_array3; die;
		//echo "10**".bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr); die;
		$rID=sql_update("subcon_ord_mst",$field_array,$data_array,"id",$update_id,0);  
		if($rID==1 && $flag==1) $flag=1; else $flag=0;

		if($data_array2!="")
		{
			$rID1=execute_query(bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr),0);
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}
		//$rID1=sql_update("subcon_ord_dtls",$field_array2,$data_array2,"id",$update_id2,0); 
		//if($rID1) $flag=1; else $flag=0;
		
		//echo "10**".$rID.'='.$rID1.'='.$flag; die;
		//==============================================================================================================================================
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id);
			}
		}
		else if($db_type==2)
		{  
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // delete here=======================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");  
		}
		
		$rec_number=return_field_value( "sys_no", "sub_material_mst"," embl_job_no=$txt_job_no and status_active=1 and is_deleted=0 and trans_type=1");
		if($rec_number){
			echo "aopRec**".str_replace("'","",$txt_job_no)."**".$rec_number;
			die;
		}

		//if ( $delete_master_info==1 )
		//{
		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		$flag=1;
		$rID=sql_update("subcon_ord_mst",$field_array,$data_array,"id",$update_id,1);  
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		$rID1=sql_update("subcon_ord_dtls",$field_array,$data_array,"job_no_mst",$txt_job_no,1);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;  
		//$rID = sql_delete("subcon_ord_dtls","status_active*is_deleted*updated_by*update_date","0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'",'id',$update_id2,1);
		$rID2=execute_query( "delete from subcon_ord_breakdown where job_no_mst=$txt_job_no",0);
		if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID3=execute_query( "update wo_booking_mst set lock_another_process=0 where booking_no =".$txt_order_no."",1);
		if($rID3==1 && $flag==1) $flag=1; else $flag=0;
		//}
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "2**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id);;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die; 
	}
}

if ($action=="job_popup")
{
	echo load_html_head_contents("Job Popup Info","../../../", 1, 1, $unicode,'','');
	?>
	<script>
		function js_set_value(id)
		{ 
			$("#hidden_mst_id").val(id);
			document.getElementById('selected_job').value=id;
			parent.emailwindow.hide();
		}
		
		function fnc_load_party_popup(type,within_group)
		{
			var company = $('#cbo_company_name').val();
			var party_name = $('#cbo_party_name').val();
			var location_name = $('#cbo_location_name').val();
			load_drop_down( 'aop_order_entry_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('Aop. Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Job');
			else if(val==4) $('#search_by_td').html('Buyer Po');
			else if(val==5) $('#search_by_td').html('Buyer Style');
		}
	</script>
</head>
<body>
<div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="940" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead> 
                <tr>
                    <th colspan="8"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>               	 
                    <th width="140" class="must_entry_caption">Company Name</th>
                    <th width="100">Within Group</th>                           
                    <th width="140">Party Name</th>
                    <th width="100">Search By</th>
                    <th width="100" id="search_by_td">Aop. Job No</th>
                    <th width="100">Year</th>
                    <th width="170">Date Range</th>                            
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                </tr>           
            </thead>
            <tbody>
                <tr class="general">
                    <td><input type="hidden" id="selected_job"><? $data=explode("_",$data); ?>  <!--  echo $data;-->
                        <? 
                        echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "fnc_load_party_popup(1,document.getElementById('cbo_within_group').value);",1); ?>
                    </td>
                    <td>
                        <?php echo create_drop_down( "cbo_within_group", 100, $yes_no,"", 0, "--  --", $data[3], "fnc_load_party_popup(1,this.value);" ); ?>
                    </td>
                    <td id="buyer_td">
                        <? echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );   	 
                        ?>
                    </td>
                    <td>
						<?
                            $search_by_arr=array(1=>"Aop. Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
                            echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                        ?>
                    </td>
                    <td align="center">
                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                    </td>
                    <td align="center"><? echo create_drop_down( "cbo_year_selection", 100, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value, 'create_job_search_list_view', 'search_div', 'aop_order_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center" valign="middle">
                            <? echo load_month_buttons();  ?>
                            <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center" valign="top" id=""><div id="search_div"></div></td>
                    </tr>
                </tbody>
            </table>    
            </form>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}
	
if($action=="create_job_search_list_view")
{	
	$data=explode('_',$data);
	$party_id=str_replace("'","",$data[1]);
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	$within_group =$data[7];
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[8]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";}

	if($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	//echo $search_type; die;
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $po_cond=" and b.po_number = '$search_str' ";
			else if ($search_by==5) $style_cond=" and a.style_ref_no = '$search_str' ";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str%'"; 
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str%'";   
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '$search_str%'";
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '$search_str%'";  
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str'";
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str'";  
		}
	}	

	if($party_id!=0) $party_id_cond=" and a.party_id='$party_id'"; else $party_id_cond="";

	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $order_rcv_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $order_rcv_date ="";
	}
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	if($within_group==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}
	
	$po_ids='';
	
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
	if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
	}
	//echo $po_ids;
	if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$buyer_po_arr=array();
	if($within_group==1)
	{
		$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
			$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		}
		unset($po_sql_res);
	}
	
	if($db_type==0)
	{
		$ins_year_cond="year(a.insert_date)";
		$aopcolor_id_str="group_concat(b.aop_color_id)";
		$buyer_po_id_str="group_concat(b.buyer_po_id)";
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		$aopcolor_id_str="listagg(b.aop_color_id,',') within group (order by b.aop_color_id)";
		$buyer_po_id_str="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
	}
	 $sql= "select a.id, a.subcon_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date, $aopcolor_id_str as color_id, $buyer_po_id_str as buyer_po_id
	 from subcon_ord_mst a, subcon_ord_dtls b 
	 where a.entry_form=278 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond $withinGroup
	 group by a.id, a.subcon_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date
	 order by a.id DESC";
	 //echo $sql;
	 $data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="885" >
        <thead>
            <th width="30">SL</th>
            <th width="60">Job No</th>
            <th width="60">Year</th>
            <th width="120">W/O No</th>
            <th width="100">Buyer Po</th>
            <th width="100">Buyer Style</th>
            <th width="80">Ord Receive Date</th>
            <th width="80">Delivery Date</th>
            <th>Aop Color</th>
        </thead>
        </table>
        <div style="width:885px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="865" class="rpt_table" id="tbl_po_list">
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
				$buyer_po=""; $buyer_style="";
				$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
				foreach($buyer_po_id as $po_id)
				{
					if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
					if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
				}
				$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
				$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('subcon_job')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                    <td width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
                    <td width="120"><? echo $row[csf('order_no')]; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_po; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_style; ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('delivery_date')]); ?></td>	
                    <td style="word-break:break-all"><? echo $color_name; ?></td>
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
 
if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id, subcon_job, company_id, location_id, party_id, currency_id, party_location, delivery_date, rec_start_date, rec_end_date, receive_date, within_group, party_location, order_id, order_no,aop_reference from subcon_ord_mst where subcon_job='$data' and status_active=1" );
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_job_no').value 			= '".$row[csf("subcon_job")]."';\n";  
		echo "document.getElementById('cbo_company_name').value 	= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_within_group').value 	= '".$row[csf("within_group")]."';\n";  
		echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		echo "fnc_load_party(1,".$row[csf("within_group")].");\n";	
	//echo "load_drop_down( 'requires/aop_order_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";	
		echo "document.getElementById('cbo_location_name').value 		= '".$row[csf("location_id")]."';\n";
	//echo "load_drop_down( 'requires/aop_order_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_buyer', 'buyer_td' );\n";
		echo "document.getElementById('cbo_party_name').value			= '".$row[csf("party_id")]."';\n";
		echo "document.getElementById('cbo_currency').value				= '".$row[csf("currency_id")]."';\n";
		echo "fnc_load_party(2,".$row[csf("within_group")].");\n";	 
		echo "document.getElementById('cbo_party_location').value		= '".$row[csf("party_location")]."';\n";	
		echo "document.getElementById('txt_order_receive_date').value	= '".change_date_format($row[csf("receive_date")])."';\n"; 
		echo "document.getElementById('txt_delivery_date').value		= '".change_date_format($row[csf("delivery_date")])."';\n"; 
		echo "document.getElementById('txt_rec_start_date').value		= '".change_date_format($row[csf("rec_start_date")])."';\n"; 
		echo "document.getElementById('txt_rec_end_date').value		= '".change_date_format($row[csf("rec_end_date")])."';\n"; 
		echo "document.getElementById('hid_order_id').value          	= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('txt_order_no').value         	= '".$row[csf("order_no")]."';\n";
		echo "document.getElementById('txt_aop_ref').value         		= '".$row[csf("aop_reference")]."';\n";
		echo "$('#txt_order_no').attr('disabled','true')".";\n";
		echo "$('#cbo_within_group').attr('disabled','true')".";\n";
		echo "$('#cbo_party_name').attr('disabled','true')".";\n";
		echo "$('#cbo_currency').attr('disabled','true')".";\n";
		echo "document.getElementById('update_id').value          		= '".$row[csf("id")]."';\n";	
		echo "set_button_status(1,'".$_SESSION['page_permission']."', 'fnc_job_order_entry',1);\n";	
	}
	exit();	
}

if ($action=="order_popup")
{	
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
	function js_set_value(id)
	{
		//alert(booking_no); 
		document.getElementById('hidd_booking_data').value=id;
		parent.emailwindow.hide();
	}

	function fnc_load_party_order_popup(company,party_name)
	{   	
		load_drop_down( 'aop_order_entry_controller', company+'_'+1+'_'+party_name, 'load_drop_down_buyer', 'buyer_td' );
		$('#cbo_party_name').attr('disabled',true);
	}
	
	function search_by(val,type)
	{
		if(type==1)
		{
			$('#txt_search_common').val('');
			if(val==1 || val==0) $('#search_td').html('W/O No');
			else if(val==2) $('#search_td').html('Job NO');
			else if(val==3) $('#search_td').html('Style Ref.');
			else if(val==4) $('#search_td').html('Buyer Po');
		}
	}
</script>
</head>
<body onLoad="fnc_load_party_order_popup(<? echo $company;?>,<? echo $party_name;?>)">
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="750" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                <thead>
                    <tr>
                        <th colspan="7" align="center">
                            <? echo create_drop_down( "cbo_search_category", 110, $string_search_type,'', 1, "-- Search Catagory --" ); ?>
                        </th>
                    </tr>
                    <tr>                	 
                        <th width="150">Party Name</th>
                        <th width="80">Search Type</th>
                        <th width="100" id="search_td">W/O No</th>
                        <th width="60">W/O Year</th>
                        <th colspan="2" width="120">W/O Date Range</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                            <input type="hidden" id="hidd_booking_data">
                        </th>
                    </tr>                                 
                </thead>
                <tr class="general">
                    <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 150, $blank_array,"", 1, "-- Select Buyer --" ); ?></td>
                    <td>
                        <? 
                            $searchtype_arr=array(1=>"W/O No",2=>"Buyer Job",3=>"Buyer Style Ref.",4=>"Buyer Po");
                            echo create_drop_down( "cbo_search_type", 80, $searchtype_arr,"", 0, "", 1, "search_by(this.value,1)",0,"" );
                        ?>
                    </td>
                    <td><input name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:90px"></td>
                    <td><? echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                    <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From"></td>
                    <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To"></td> 
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_search_category').value+'_'+<? echo $company;?>+'_'+document.getElementById('cbo_search_type').value, 'create_booking_search_list_view', 'search_div', 'aop_order_entry_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                    <td colspan="7"align="center" valign="middle"><?  echo load_month_buttons(); ?></td>
                </tr>
            </table>
            <div id="search_div"></div>   
        </form>
    </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_booking_search_list_view")
{	
	$data=explode('_',$data);
	$search_type=$data[7];
	
	if ($data[6]!=0) $party_cond=" and a.supplier_id='$data[6]'"; else { echo "Please Select Company First."; die; }
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Party First."; die; }
	//if ($data[0]!=0) $buyer=" and buyer_id='$data[1]'"; else  $buyer="";{ echo "Please Select Buyer First."; die; }
	if($data[4]!=0)
	{
		if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[4]"; } else if($db_type==2) { $year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]"; }
	} else $year_cond="";
	$master_company=$data[6];

	$woorder_cond=""; $job_cond=""; $style_cond=""; $po_cond="";
	if($data[5]==1)
	{
		if ($data[1]!="")
		{
			if ($search_type==1) $woorder_cond=" and a.booking_no = '$data[1]' ";
			if ($search_type==2) $job_cond=" and a.job_no_prefix_num = '$data[1]' ";
			if ($search_type==3) $style_cond=" and a.style_ref_no = '$data[1]' ";
			if ($search_type==4) $po_cond=" and b.po_number = '$data[1]' ";
		}
	}
	if($data[5]==2)
	{
		if ($data[1]!="")
		{
			if ($search_type==1) $woorder_cond=" and a.booking_no like '$data[1]%' ";
			if ($search_type==2) $job_cond=" and a.job_no_prefix_num like '$data[1]%' ";
			if ($search_type==3) $style_cond=" and a.style_ref_no like '$data[1]%' ";
			if ($search_type==4) $po_cond=" and b.po_number like '$data[1]%' ";
		}
	}
	if($data[5]==3)
	{
		if ($data[1]!="")
		{
			if ($search_type==1) $woorder_cond=" and a.booking_no like '%$data[1]' ";
			if ($search_type==2) $job_cond=" and a.job_no_prefix_num like '%$data[1]' ";
			if ($search_type==3) $style_cond=" and a.style_ref_no like '%$data[1]' ";
			if ($search_type==4) $po_cond=" and b.po_number like '%$data[1]' ";
		}	
	}
	if($data[5]==4 || $data[5]==0)
	{
		if ($data[1]!="")
		{
			if ($search_type==1) $woorder_cond=" and a.booking_no like '%$data[1]%' ";
			if ($search_type==2) $job_cond=" and a.job_no_prefix_num like '%$data[1]%' ";
			if ($search_type==3) $style_cond=" and a.style_ref_no like '%$data[1]%' ";
			if ($search_type==4) $po_cond=" and b.po_number like '%$data[1]%' ";
		}
	}
	
	$po_ids='';
	
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	if(($job_cond!="" && $search_type==2) || ($style_cond!="" && $search_type==3)|| ($po_cond!="" && $search_type==4))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
		if ($po_ids=="")
		{
			$po_idsCond="";
			echo "Not Found."; die;
		}
	}
	
	if ($po_ids!="") $po_idsCond=" and b.po_break_down_id in ($po_ids)"; else $po_idsCond="";
	$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
	}
	unset($po_sql_res);
	
	/*$pre_cost_conv_arr=array();
	$pre_sql ="Select id, emb_name, emb_type, body_part_id  from wo_pre_cost_embe_cost_dtls where process=35 and status_active=1 and is_deleted=0";
	$pre_sql_res=sql_select($pre_sql);
	foreach ($pre_sql_res as $row)
	{
		$pre_cost_conv_arr[$row[csf("id")]]['emb_name']=$row[csf("emb_name")];
		$pre_cost_conv_arr[$row[csf("id")]]['emb_type']=$row[csf("emb_type")];
		$pre_cost_conv_arr[$row[csf("id")]]['body_part_id']=$row[csf("body_part_id")];
	}
	unset($pre_sql_res);*/
	
	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date = "and a.booking_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
		$wo_year="YEAR(a.insert_date)";
		$pre_cost_conv_cond="group_concat(b.pre_cost_fabric_cost_dtls_id)";
		$gmts_item_cond="group_concat(b.gmt_item)";
		$po_id_cond="group_concat(b.po_break_down_id)";
	}
	else if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date = "and a.booking_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
		$wo_year="to_char(a.insert_date,'YYYY')";
		$pre_cost_conv_cond="listagg(b.pre_cost_fabric_cost_dtls_id,',') within group (order by b.pre_cost_fabric_cost_dtls_id)";
		$gmts_item_cond="listagg(b.gmt_item,',') within group (order by b.gmt_item)";
		$po_id_cond="listagg(b.po_break_down_id,',') within group (order by b.po_break_down_id)";
	} 
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (0=>$comp,1=>$buyer_arr);
	
	//$sql= "select $wo_year as year, a.id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, $pre_cost_trims_cond as pre_cost_trims_id, $gmts_item_cond as gmts_item, $po_id_cond as po_id from  wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_embe_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.booking_type=3 and a.status_active=1 and c.emb_name=1 and a.lock_another_process!=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $booking_date $company $woorder_cond $year_cond $po_idsCond group by a.insert_date, a.id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id order by a.id DESC";
	
	$sql= "select $wo_year as year, a.id, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, $pre_cost_conv_cond as pre_cost_conv_id, $po_id_cond as po_id 
	from  wo_booking_mst a, wo_booking_dtls b
	where a.booking_no=b.booking_no and b.process=35 and a.booking_type=3 and a.pay_mode in (3,5) and a.lock_another_process!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $booking_date $company $party_cond $woorder_cond $year_cond $po_idsCond group by a.insert_date, a.id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id order by a.id DESC";
	//echo $sql;
	$data_array=sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="750" >
        <thead>
            <th width="30">SL</th>
            <th width="60">W/O Year</th>
            <th width="60">W/O No</th>
            <th width="70">W/O Date</th>
            <th width="100">Buyer</th>
            <th width="150">Buyer Po</th>
            <th width="120">Buyer Style</th>
            <th>Buyer Job</th>
        </thead>
        </table>
        <div style="width:750px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table" id="list_view">
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$expo_id=array_unique(explode(",",$row[csf('po_id')]));
				$buyer_name=""; $po_no=""; $buyer_style=""; $buyer_job="";
				foreach ($expo_id as $po_id)
				{
					if($buyer_name=="") $buyer_name=$buyer_arr[$buyer_po_arr[$po_id]['buyer']]; else $buyer_name.=','.$buyer_arr[$buyer_po_arr[$po_id]['buyer']];
					if($po_no=="") $po_no=$buyer_po_arr[$po_id]['po']; else $po_no.=','.$buyer_po_arr[$po_id]['po'];
					if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
					if($buyer_job=="") $buyer_job=$buyer_po_arr[$po_id]['job']; else $buyer_job.=','.$buyer_po_arr[$po_id]['job'];
				}
				
				$buyer_name=implode(", ",array_unique(explode(",",$buyer_name)));
				$po_no=implode(", ",array_unique(explode(",",$po_no)));
				$buyer_style=implode(", ",array_unique(explode(",",$buyer_style)));
				$buyer_job=implode(", ",array_unique(explode(",",$buyer_job)));
				
				/*$expre_cost_trims_id=array_unique(explode(",",$row[csf('pre_cost_trims_id')]));
				$body_part_name=""; $embl_name=""; $embl_type="";
				foreach ($expre_cost_trims_id as $pre_cost_id)
				{
					if($body_part_name=="") $body_part_name=$body_part[$pre_cost_trims_arr[$pre_cost_id]['body_part_id']]; else $body_part_name.=','.$body_part[$pre_cost_trims_arr[$pre_cost_id]['body_part_id']];
					
					if($embl_name=="") $embl_name=$emblishment_name_array[$pre_cost_trims_arr[$pre_cost_id]['emb_name']]; else $embl_name.=','.$emblishment_name_array[$pre_cost_trims_arr[$pre_cost_id]['emb_name']];
					
					if($pre_cost_trims_arr[$pre_cost_id]['emb_name']==1) $emb_type=$emblishment_print_type;
					else if($pre_cost_trims_arr[$pre_cost_id]['emb_name']==2) $emb_type=$emblishment_embroy_type;
					else if($pre_cost_trims_arr[$pre_cost_id]['emb_name']==3) $emb_type=$emblishment_wash_type;
					else if($pre_cost_trims_arr[$pre_cost_id]['emb_name']==4) $emb_type=$emblishment_spwork_type;
					else if($pre_cost_trims_arr[$pre_cost_id]['emb_name']==5) $emb_type=$emblishment_gmts_type;
					
					if($embl_type=="") $embl_type=$emb_type[$pre_cost_trims_arr[$pre_cost_id]['emb_type']]; else $embl_type.=','.$emb_type[$pre_cost_trims_arr[$pre_cost_id]['emb_type']]; 
				}
				
				$body_part_name=implode(", ",array_unique(explode(",",$body_part_name)));
				$embl_name=implode(", ",array_unique(explode(",",$embl_name)));
				$embl_type=implode(", ",array_unique(explode(",",$embl_type)));
				
				$gmts_item_name="";
				$exgmts_item_id=explode(",",$row[csf('gmts_item')]);
				foreach($exgmts_item_id as $item_id)
				{
					if($gmts_item_name=="") $gmts_item_name=$garments_item[$item_id]; else $gmts_item_name.=','.$garments_item[$item_id];
				}
				$gmts_item_name=implode(", ",array_unique(explode(",",$gmts_item_name)));*/
				
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('booking_no')].'_'.$row[csf('currency_id')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60" align="center"><? echo $row[csf('year')]; ?></td>
                    <td width="60" align="center"><? echo $row[csf('booking_no_prefix_num')]; ?></td>
                    <td width="70"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_name; ?></td>
                    <td width="150" style="word-break:break-all"><? echo $po_no; ?></td>
                    <td width="120" style="word-break:break-all"><? echo $buyer_style; ?></td>
                    <td style="word-break:break-all"><? echo $buyer_job; ?></td>
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

if( $action=='order_dtls_list_view' ) 
{
	//echo $data;
	$data=explode('_',$data);
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$tblRow=0;
	$buyer_po_arr=array();
	$buyer_po_sql = sql_select("select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst");
	foreach($buyer_po_sql as $row)
	{
		$buyer_po_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
		$buyer_po_arr[$row[csf('id')]]['buyerpo']=$row[csf('po_number')];
	}
	unset($buyer_po_sql);
	if($data[2]==1) // within_group Yes
	{
		$aop_po_arr=array();
		if($data[0]==2) //Update
		{
			$sql_up = "select id, order_no, booking_dtls_id, wastage from subcon_ord_dtls where job_no_mst='$data[1]' and status_active=1 and is_deleted=0 order by id ASC";
			$data_arrup=sql_select($sql_up);
			foreach($data_arrup as $row)
			{
				$data[1]=$row[csf('order_no')];
				$aop_po_arr[$row[csf('booking_dtls_id')]]['id']=$row[csf('id')];
				$aop_po_arr[$row[csf('booking_dtls_id')]]['wastage']=$row[csf('wastage')];
			}
			$is_used_cond="";
			$readonly="readonly";
		}
		else $is_used_cond="and a.lock_another_process!=1";
		//print_r($aop_po_arr);
		$sql = "select a.currency_id, a.exchange_rate, b.id as booking_dtls_id, b.po_break_down_id, b.dia_width, b.gmts_color_id, b.fabric_color_id as aop_color_id, b.fin_dia, b.printing_color_id as item_color_id, b.wo_qnty, b.uom, b.rate, b.amount,b.artwork_no, d.body_part_id, d.lib_yarn_count_deter_id, d.construction, d.composition, d.gsm_weight from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fab_conv_cost_dtls c, wo_pre_cost_fabric_cost_dtls d where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and c.fabric_description=d.id and a.booking_no=trim('$data[1]') and b.process=35 and a.booking_type=3 and a.pay_mode in (3,5) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $is_used_cond order by b.id ASC";
	}
	else
	{
		//$field_array2="id, mst_id, job_no_mst, order_id, order_no, buyer_po_id, body_part, construction, composition, gsm, grey_dia, gmts_color_id, item_color_id, fin_dia, aop_color_id, lib_yarn_deter, booking_dtls_id, order_quantity, order_uom, rate, amount, wastage,artwork_no ,design_no , inserted_by, insert_date, status_active, is_deleted";

		$sql = "select id, order_no, buyer_po_id, booking_dtls_id, gmts_item_id as gmt_item, main_process_id as emb_name, embl_type as emb_type, body_part as body_part_id, order_quantity as wo_qnty, order_uom as uom, rate, amount, smv, delivery_date, wastage,artwork_no,design_no,construction, composition, gsm as gsm_weight, grey_dia, gmts_color_id, item_color_id, fin_dia as dia_width, aop_color_id, lib_yarn_deter from subcon_ord_dtls where job_no_mst='$data[1]' and mst_id='$data[3]' and status_active=1 and is_deleted=0 order by id ASC";
		$readonly="";
		// aop_color_id  aopColor_id    fin_dia  txtFinDia    item_color_id  itemColor_id    gmts_color_id  gmtsColor_id
	}
	//echo $sql; //die; 
	$data_array=sql_select($sql);
	if(count($data_array) > 0)
	{
		foreach($data_array as $row)
		{
			$tblRow++;
			$dtls_id=0; $smv=0; $wastage=0;  $order_uom=0; $wo_qnty=0;
			if($data[2]==1)
			{
				if($data[0]==2)
				{
					$dtls_id=$aop_po_arr[$row[csf('booking_dtls_id')]]['id']; 
					$wastage=$aop_po_arr[$row[csf('booking_dtls_id')]]['wastage'];
					$wo_qnty=$row[csf('wo_qnty')];
				}
				else
				{
					$wo_qnty=0;
				}
			}
			else if($data[2]==2)
			{
				if($data[0]==2)
				{
					$dtls_id=$row[csf('id')]; 
					$wastage=$row[csf('wastage')];
					$order_uom=$row[csf('order_uom')];
					$wo_qnty=$row[csf('wo_qnty')];
				}
				else
				{
					$wo_qnty=0;
				}
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
				<td><input name="txtbuyerPo_<? echo $tblRow; ?>" id="txtbuyerPo_<? echo $tblRow; ?>" value="<? echo $buyer_po_arr[$row[csf('po_break_down_id')]]['buyerpo']; ?>" class="text_boxes" type="text"  style="width:87px" readonly <? //echo $readonly; ?> />
					<input name="txtbuyerPoId_<? echo $tblRow; ?>" id="txtbuyerPoId_<? echo $tblRow; ?>" value="<? echo $row[csf('po_break_down_id')]; ?>" class="text_boxes" type="hidden" style="width:70px" />
				</td>
				<td><input name="txtstyleRef_<? echo $tblRow; ?>" id="txtstyleRef_<? echo $tblRow; ?>" value="<? echo $buyer_po_arr[$row[csf('po_break_down_id')]]['style']; ?>" class="text_boxes" type="text"  style="width:87px" readonly <? //echo $readonly; ?> /></td>
				<td><? echo create_drop_down( "cboBodyPart_".$tblRow, 80, $body_part,"", 1, "--Select--",$row[csf('body_part_id')],"", 1 ); ?></td>	
                <td><input type="text" id="txtConstruction_<? echo $tblRow; ?>" name="txtConstruction_<? echo $tblRow; ?>" value="<? echo $row[csf('construction')]; ?>" class="text_boxes" style="width:77px" <? echo $readonly; ?> /></td>
                <td><input type="text" id="txtComposition_<? echo $tblRow; ?>" name="txtComposition_<? echo $tblRow; ?>" value="<? echo $row[csf('composition')]; ?>" class="text_boxes" style="width:77px" <? echo $readonly; ?> /></td>
                <td><input type="text" id="txtGsm_<? echo $tblRow; ?>" name="txtGsm_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('gsm_weight')]; ?>" style="width:47px" <? echo $readonly; ?> /></td>
                <td><input type="text" id="txtDia_<? echo $tblRow; ?>" name="txtDia_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('dia_width')]; ?>" style="width:47px" <? echo $readonly; ?> /></td>
                <td><input type="text" id="txtGmtsColor_<? echo $tblRow; ?>" name="txtGmtsColor_<? echo $tblRow; ?>" value="<? echo $color_arr[$row[csf('gmts_color_id')]]; ?>" class="text_boxes txt_color" style="width:67px" <? echo $readonly; ?> /></td>
                <td><input type="text" id="txtItemColor_<? echo $tblRow; ?>" name="txtItemColor_<? echo $tblRow; ?>" value="<? echo $color_arr[$row[csf('item_color_id')]]; ?>" class="text_boxes txt_color" style="width:67px" <? echo $readonly; ?> /></td>
                <td><input type="text" id="txtFinDia_<? echo $tblRow; ?>" name="txtFinDia_<? echo $tblRow; ?>" value="<? echo $row[csf('dia_width')]; ?>" class="text_boxes" style="width:47px" <? echo $readonly; ?> /></td>
                <td><input type="text" id="txtAopColor_<? echo $tblRow; ?>" name="txtAopColor_<? echo $tblRow; ?>" value="<? echo $color_arr[$row[csf('aop_color_id')]]; ?>" class="text_boxes txt_color" style="width:67px" <? echo $readonly; ?> /></td>
                <td><input type="text" id="txtArtwork_<? echo $tblRow; ?>" name="txtArtwork_<? echo $tblRow; ?>" class="text_boxes" style="width:67px" value="<? echo $row[csf('artwork_no')]; ?>" /></td>
                <td><input type="text" id="txtQty_<? echo $tblRow; ?>" name="txtQty_<? echo $tblRow; ?>" value="<? echo number_format($row[csf('wo_qnty')],4,'.',''); ?>" class="text_boxes_numeric" style="width:57px"  onkeyup="cal_amount(<? echo $tblRow; ?>);" <? echo $readonly; ?> /></td>
                <td><? echo create_drop_down( "cboUom_".$tblRow, 60, $unit_of_measurement,'', 1, '-Select-', $row[csf('uom')], "",1,"1,12,15,23,27" ); ?></td>
                <td><input type="text" id="txtRateUnit_<? echo $tblRow; ?>" name="txtRateUnit_<? echo $tblRow; ?>" value="<? echo number_format($row[csf('rate')],4,'.',''); ?>"  onkeyup="cal_amount(<? echo $tblRow; ?>);" class="text_boxes_numeric" style="width:47px" <? echo $readonly; ?> /></td>
                <td><input type="text" id="txtAmount_<? echo $tblRow; ?>" name="txtAmount_<? echo $tblRow; ?>" value="<? echo number_format($row[csf('amount')],4,'.',''); ?>" class="text_boxes_numeric" style="width:57px" readonly /></td>
                <td><input type="text" id="txtProcessLoss_<? echo $tblRow; ?>" name="txtProcessLoss_<? echo $tblRow; ?>" value="<? echo $wastage; ?>" class="text_boxes_numeric" style="width:47px" />
                    <input type="hidden" name="hdnDtlsUpdateId_<? echo $tblRow; ?>" id="hdnDtlsUpdateId_<? echo $tblRow; ?>" value="<? echo $dtls_id; ?>">
                    <input type="hidden" name="hdnlibyarndetar_<? echo $tblRow; ?>" id="hdnlibyarndetar_<? echo $tblRow; ?>" value="<? echo $row[csf('lib_yarn_count_deter_id')]; ?>">
                    <input type="hidden" name="hdnbookingDtlsId_<? echo $tblRow; ?>" id="hdnbookingDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('booking_dtls_id')]; ?>">
                </td>
                <td><input type="text" id="txtDesignNo_<? echo $tblRow; ?>" name="txtDesignNo_<? echo $tblRow; ?>" value="<? echo $row[csf('design_no')]; ?>" class="text_boxes" style="width:57px" />
                </td>
                <td id="image_<? echo $tblRow; ?>"><input type="button" class="image_uploader" name="txtFile_<? echo $tblRow; ?>" id="txtFile_<? echo $tblRow; ?>" onClick="file_uploader ( '../../', document.getElementById('hdnDtlsUpdateId_<? echo $tblRow; ?>').value,'', 'aoporderentry_1', 0 ,1)" style="" value="ADD IMAGE"></td>
			</tr>
			<?
		}
	}
	else
	{
		?>		
		<tr>
			<td><input name="txtbuyerPo_1" id="txtbuyerPo_1" type="text" class="text_boxes" style="width:87px" placeholder="Display" readonly />
                <input name="txtbuyerPoId_1" id="txtbuyerPoId_1" type="hidden" class="text_boxes" style="width:70px" />
            </td>
            <td><input name="txtstyleRef_1" id="txtstyleRef_1" type="text" class="text_boxes" style="width:87px" placeholder="Display" readonly /></td>
            <td><? echo create_drop_down( "cboBodyPart_1", 80, $body_part,"", 1, "--Select--",0,"", 0 ); ?></td>
            <td><input type="text" id="txtConstruction_1" name="txtConstruction_1" class="text_boxes" style="width:77px" /></td>
            <td><input type="text" id="txtComposition_1" name="txtComposition_1" class="text_boxes" style="width:77px" /></td>
            <td><input type="text" id="txtGsm_1" name="txtGsm_1" class="text_boxes_numeric" style="width:47px" /></td>
            <td><input type="text" id="txtDia_1" name="txtDia_1" class="text_boxes" style="width:47px" /></td>
            <td><input type="text" id="txtGmtsColor_1" name="txtGmtsColor_1" class="text_boxes" style="width:67px" /></td>
            <td><input type="text" id="txtItemColor_1" name="txtItemColor_1" class="text_boxes" style="width:67px" /></td>
            <td><input type="text" id="txtFinDia_1" name="txtFinDia_1" class="text_boxes" style="width:47px" /></td>
            <td><input type="text" id="txtAopColor_1" name="txtAopColor_1" class="text_boxes" style="width:67px" /></td>
            <td><input type="text" id="txtArtwork_1" name="txtArtwork_1" class="text_boxes" style="width:67px" /></td>
            <td><input type="text" id="txtQty_1" name="txtQty_1" class="text_boxes_numeric" style="width:57px" /></td>
            <td><? echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,'', 1, '-Select-', $uom, "","","1,12,15,23,27" ); ?></td>
            <td><input type="text" id="txtRateUnit_1" name="txtRateUnit_1" class="text_boxes_numeric" style="width:47px" /></td>
            <td><input type="text" id="txtAmount_1" name="txtAmount_1" class="text_boxes_numeric" style="width:57px" /></td>
            <td><input type="text" id="txtProcessLoss_1" name="txtProcessLoss_1" class="text_boxes_numeric" style="width:47px" />
                <input type="hidden" name="hdnDtlsUpdateId_1" id="hdnDtlsUpdateId_1">
                <input type="hidden" name="hdnlibyarndetar_1" id="hdnlibyarndetar_1">
                <input type="hidden" name="hdnbookingDtlsId_1" id="hdnbookingDtlsId_1">
            </td>
            <td><input type="text" id="txtDesignNo_1" name="txtDesignNo_1" class="text_boxes" style="width:57px" />
		</tr>
		<?
	}
	exit();
}
?>