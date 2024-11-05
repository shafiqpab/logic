<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 160,"select id,location_name from lib_location where company_id=$data and is_deleted=0  and status_active=1  $location_credential_cond","id,location_name", 1, "-- Select --", $selected, "" );
	die;
}

if($action=="openpopup_itemgroup")
{
	echo load_html_head_contents("Item Group Select","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	?>
    <script>
		var selected_name = new Array();
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'item_table' ).rows.length;
			tbl_row_count = tbl_row_count-1;
			if(document.getElementById('check_all').checked){
				for( var i = 1; i <= tbl_row_count; i++ ) {
					document.getElementById( 'search' + i ).style.backgroundColor = 'yellow';
					if( jQuery.inArray( $('#txttrimgroupdata_' + i).val(), selected_name ) == -1 ) {
						selected_name.push($('#txttrimgroupdata_' + i).val());
					}
				}
				var trimgroupdata='';
                for( var i = 0; i < selected_name.length; i++ ) {
                    trimgroupdata += selected_name[i] + ',';
                }
                trimgroupdata = trimgroupdata.substr( 0, trimgroupdata.length - 1 );
                $('#itemdata').val( trimgroupdata );
			}else{
				for( var i = 1; i <= tbl_row_count; i++ ) {
					if(i%2==0  ){
						document.getElementById('search'+i).style.backgroundColor = '#FFFFFF';
					}
					if(i%2!=0 ){
						document.getElementById('search'+i).style.backgroundColor = '#E9F3FF';
					}
					for( var j = 0; j < selected_name.length; j++ ) {
							if( selected_name[j] == $('#txttrimgroupdata_' + i).val() ) break;
					}
					selected_name.splice( j,1 );
				}
				var trimgroupdata='';
                for( var i = 0; i < selected_name.length; i++ ) {
                    trimgroupdata += selected_name[i] + ',';
                }
                trimgroupdata = trimgroupdata.substr( 0, trimgroupdata.length - 1 );
                $('#itemdata').val( trimgroupdata );

			}

		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function onlyUnique(value, index, self) {
			return self.indexOf(value) === index;
		}

		//var selected_name = new Array();

		/*function js_set_value( str ) {
			if($("#search"+str).css("display") !='none'){
				//toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
				if(str%2==0  ){
					toggle( document.getElementById( 'search' + str ), '#FFFFFF');
				}
				if(str%2!=0 ){
					toggle( document.getElementById( 'search' + str ), '#E9F3FF');
				}
				if( jQuery.inArray( $('#txttrimgroupdata_' + str).val(), selected_name ) == -1 ) {
					selected_name.push($('#txttrimgroupdata_' + str).val());
				}
				else{
					for( var i = 0; i < selected_name.length; i++ ) {
						if( selected_name[i] == $('#txttrimgroupdata_' + str).val() ) break;
					}
					selected_name.splice( i,1 );
				}
			}
			var trimgroupdata='';
			for( var i = 0; i < selected_name.length; i++ ) {
				trimgroupdata += selected_name[i] + ',';
			}
			trimgroupdata = trimgroupdata.substr( 0, trimgroupdata.length - 1 );

			$('#itemdata').val( trimgroupdata );
		}*/

		function js_set_value( str ) {
			if($("#search"+str).css("display") !='none'){
				//toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
				if(str%2==0  ){
					toggle( document.getElementById( 'search' + str ), '#FFFFFF');
				}
				if(str%2!=0 ){
					toggle( document.getElementById( 'search' + str ), '#E9F3FF');
				}
				if( jQuery.inArray( $('#txttrimgroupdata_' + str).val(), selected_name ) == -1 ) {
					selected_name.push($('#txttrimgroupdata_' + str).val());
				}
				else{
					for( var i = 0; i < selected_name.length; i++ ) {
						if( selected_name[i] == $('#txttrimgroupdata_' + str).val() ) break;
					}
					selected_name.splice( i,1 );
				}
			}
			var trimgroupdata='';
			for( var i = 0; i < selected_name.length; i++ ) {
				trimgroupdata += selected_name[i] + ',';
			}
			trimgroupdata = trimgroupdata.substr( 0, trimgroupdata.length - 1 );

			$('#itemdata').val( trimgroupdata );
		}

	/*function js_set_value(id, name)
	{
		document.getElementById('gid').value=id;
		document.getElementById('gname').value=name;
		parent.emailwindow.hide();
	}*/
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;">
        <input type="hidden" id="gid" name="gid"/>
        <input type="hidden" id="itemdata" name="itemdata"/>
        <? $sql_tgroup=sql_select( "select id, item_name, trim_uom from lib_item_group where item_category=4 and is_deleted=0 and status_active=1 order by item_name"); ?>
        <table width="420" cellspacing="0" class="rpt_table" border="0" rules="all">
            <thead>
            	<th width="40">SL</th>
            	<th width="220">Item Group</th>
            	<th>Order UOM</th>
            </thead>
        </table>
        <div style="width:420px; overflow-y:scroll; max-height:340px;" >
        <table width="400" cellspacing="0" class="rpt_table" border="0" rules="all" id="item_table">
            <tbody>
				<?
                $i=1;
                foreach($sql_tgroup as $row_tgroup)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$str="";
					$str=$row_tgroup[csf('id')].'***'.$row_tgroup[csf('item_name')].'***'.$row_tgroup[csf('trim_uom')];
					?>
					<tr id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)" bgcolor="<? echo $bgcolor; ?>">
						<td width="40"><? echo $i; ?></td>
						<td width="220"><? echo $row_tgroup[csf('item_name')]; ?>
                        <input type="hidden" name="txttrimgroupdata_<? echo $i; ?>" id="txttrimgroupdata_<? echo $i; ?>" value="<? echo $str; ?>"/>
                        </td>
						<td><? echo $unit_of_measurement[$row_tgroup[csf('trim_uom')]]; ?></td>
					</tr>
					<?
					$i++;
                }
                ?>
            </tbody>
        </table>
        </div>
        <table width="420" cellspacing="0" cellpadding="0" style="border:none" align="center">
        <tr>
            <td align="center" height="30" valign="bottom">
                <div style="width:100%">
                    <div style="width:50%; float:left" align="left">
                    	<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                    </div>
                    <div style="width:50%; float:left" align="left">
                    	<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                    </div>
                </div>
            </td>
        </tr>
	</table>
    </div>
    </body>
	<script>setFilterGrid('item_table',-1);</script>
	<!--<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>-->
	</html>
	<?
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$entry_form = 466;

	// echo "10**$cbo_company_name";

	if ($operation==0)  // Insert Here=======================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		if($db_type==0)
		{
			$new_requ_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '','FTPR', date("Y",time()), 5, "select requ_no_prefix, requ_prefix_num from trims_finish_purchase_req_mst where company_id=$cbo_company_name and YEAR(insert_date)=".date('Y',time())." and entry_form=$entry_form order by id desc ", "requ_no_prefix", "requ_prefix_num" ));
		}
	    else if($db_type==2)
		{
			$new_requ_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '','FTPR', date("Y",time()), 5, "select requ_no_prefix,requ_prefix_num from trims_finish_purchase_req_mst where company_id=$cbo_company_name and TO_CHAR(insert_date,'YYYY')=".date('Y',time())." and entry_form=$entry_form order by id desc ", "requ_no_prefix", "requ_prefix_num" ));
		}

		$id=return_next_id("id","trims_finish_purchase_req_mst",1);
		$field_array="id,entry_form,requ_no,requ_no_prefix,requ_prefix_num,company_id,location_id,requisition_date,pay_mode,source,currency_id,delivery_date,remarks,manual_req,req_by,template_id,inserted_by,insert_date,status_active,is_deleted";
		$data_array="(".$id.",$entry_form,'".$new_requ_no[0]."','".$new_requ_no[1]."',".$new_requ_no[2].",".$cbo_company_name.",".$cbo_location_name.",".$txt_req_date.",".$cbo_pay_mode.",".$cbo_source.",".$cbo_currency_name.",".$txt_date_delivery.",".$txt_remarks.",".$txt_manual_req.",".$txt_req_by.",".$cbo_template_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";

		// echo "10**insert into trims_finish_purchase_req_mst (".$field_array.") values ".$data_array;die;

		$rID=sql_insert("trims_finish_purchase_req_mst",$field_array,$data_array,0);

		if($db_type==0)
		{
			if($rID){
			 	mysql_query("COMMIT");
			  	echo "0**".str_replace("'", '', $new_requ_no[0])."**".$id;
			}
			else{
			  	mysql_query("ROLLBACK");
			  	echo "10**".$id;
			}
		}
		else if($db_type==2)
		{
			if($rID)
			{
			 	oci_commit($con);
			 	echo "0**".str_replace("'", '', $new_requ_no[0])."**".$id;
			}
			else
			{
				oci_rollback($con);
			 	echo "10**".$id;
			}
		}

		check_table_status( $_SESSION['menu_id'],0);
		// if($db_type==2) {oci_commit($con);}

		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here==================================================================
	{
		$con = connect();
		if($db_type==0)
	  	{
			mysql_query("BEGIN");
	  	}

		$field_array="location_id*requisition_date*pay_mode*source*currency_id*delivery_date*remarks*manual_req*req_by*template_id*updated_by*update_date";
		$data_array="".$cbo_location_name."*".$txt_req_date."*".$cbo_pay_mode."*".$cbo_source."*".$cbo_currency_name."*".$txt_date_delivery."*".$txt_remarks."*".$txt_manual_req."*".$txt_req_by."*".$cbo_template_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

	  	$rID=sql_update("trims_finish_purchase_req_mst",$field_array,$data_array,"id",$update_id,1);

		if($db_type==0)
		  {
			  if($rID)
			  {
				  mysql_query("COMMIT");
				  echo "1**".str_replace("'",'',$txt_requisition_no)."**".str_replace("'",'',$update_id);
			  }
			  else
			  {
				  mysql_query("ROLLBACK");
				  echo "10**".str_replace("'",'',$txt_requisition_no)."**".str_replace("'",'',$update_id);
			  }
		  }
		 else if($db_type==2)
		  {
			  if($rID)
			  {
				 oci_commit($con);
				 echo "1**".str_replace("'",'',$txt_requisition_no)."**".str_replace("'",'',$update_id);
			  }
			  else
			  {
				  oci_rollback($con);
				  echo "10**".str_replace("'",'',$txt_requisition_no)."**".str_replace("'",'',$update_id);
			  }
		  }
		  disconnect($con);
		  die;
	}
	else if ($operation==2)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		/*$prev_pay_mode=return_field_value("pay_mode","trims_finish_purchase_req_mst","id=$update_id","pay_mode");
		$next_opp_check=0;
		if($prev_pay_mode==4)
		{
			$next_opp_check=return_field_value("id","inv_receive_master","booking_id=$update_id and entry_form in(4,20) and receive_basis = 7","id");
		}
		else
		{
			$next_opp_check=return_field_value("id","wo_non_order_info_dtls","requisition_no='".str_replace("'","",$update_id)."'","id");
		}
		if($next_opp_check)
		{
			echo "11**Next Operation Found, Delete Not Allow";disconnect($con);die;
		}*/


		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="'".$user_id."'*'".$pc_date_time."'*0*1";

		$rID=sql_delete("trims_finish_purchase_req_mst",$field_array,$data_array,"id","".$update_id."",1);
		$rID2=sql_update("trims_finish_purchase_req_dtls",'status_active*is_deleted','0*1',"mst_id",$update_id,1);
		  if($db_type==0)
		  {
			  if($rID && $rID2)
			  {
				  mysql_query("COMMIT");
				  echo "2**";
			  }
			  else
			  {
				  mysql_query("ROLLBACK");
				  echo "10**".str_replace("'",'',$txt_requisition_no)."**".str_replace("'",'',$update_id);
			  }
		  }
		  else if($db_type==2)
		  {
			  if($rID && $rID2)
			  {
				oci_commit($con);
				  echo "2**";
			  }
			  else
			  {
				 oci_rollback($con);
				  echo "10**".str_replace("'",'',$txt_requisition_no)."**".str_replace("'",'',$update_id);
			  }
		  }
		  disconnect($con);
		  die;
	}
}

if ($action=="save_update_delete_trim_cost_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$str_rep=array("+", "&", "*", "(", ")", "=","'","\r", "\n",'"','#');

	if($operation==0)
	{
	    $con = connect();
		if($db_type==0) mysql_query("BEGIN");
		$approved=0;
		$update_id = str_replace("'", '', $update_id);
		/*$sql=sql_select("select approved from wo_pre_cost_mst where job_no=$update_id");
		foreach($sql as $row){
			//if($row[csf('approved')]==3) $approved=1; else $approved=$row[csf('approved')];
			$approved=$row[csf('approved')];
		}
		if($approved==1){
			echo "approved**".str_replace("'","",$update_id);
			disconnect($con);die;
		}
		if($approved==3){
			echo "papproved**".str_replace("'","",$update_id);
			disconnect($con);die;
		}
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}*/
		
		$color_library_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
		$size_library_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
		 
		$id=return_next_id( 'id', 'trims_finish_purchase_req_dtls', 1);
		 /*$idsup=return_next_id( "id", "wo_pre_cost_trim_supplier", 1);
		 $id1=return_next_id( "id", "wo_pre_cost_trim_co_cons_dtls", 1);*/
		 
		$field_array="id, mst_id, quantity, item_group_id, item_description, item_color, color_id, rate, amount, remarks, item_size, size_id, uom, job_id, job_no, status_active, inserted_by, insert_date";
		 
		 /*$field_supp_arr="id, job_id, job_no, trimid, supplier_id, inserted_by, insert_date, status_active, is_deleted";
		 //item_number_id,color_number_id,item_color_number_id,size_number_id,rate,amount,gmts_pcs,color_size_table_id
		 $field_array1="id, job_id, wo_pre_cost_trim_cost_dtls_id, job_no, po_break_down_id, item_size, cons, place, pcs, country_id, excess_per, tot_cons, ex_cons, item_number_id, color_number_id, size_number_id, rate, amount, color_size_table_id";*/
		$add_comma=0; $fail1=0; $dataSuppArr=""; $q=1;
		$new_array_color=array(); $new_array_size=array();
		for ($i=1;$i<=$tot_row;$i++)
		{
			// $product_id="txtremark_".$i;
			$quantity="quantity_".$i;
			$item_group_id="hdnItemGroupId_".$i;
			$item_description="itemdescription_".$i;
			$item_color="itemColor_".$i;
			$item_size="itemsize_".$i;
			$rate="rate_".$i;
			$amount="amount_".$i;
			$remarks="txtRemarks_".$i;
			// $size_id="hdnItemSizeId_".$i;
			$color_id=0;
			$size_id=0;
			$uom="cboUom_".$i;
			$status="cbostatus_".$i;
			$job_id='';
			$job_no='';

			if(str_replace("'","",$$item_color)!="")
			{ 
				if (!in_array(str_replace("'","",$$item_color), $new_array_color))
				{
					$color_id = return_id( str_replace("'","",$$item_color), $color_library_arr, "lib_color", "id,color_name","255");  
					//echo $$txtColorName.'='.$color_id.'<br>';
					$new_array_color[$color_id]=str_replace("'","",$$item_color);
				}
				else $color_id =  array_search(str_replace("'","",$$item_color), $new_array_color); 
			}

			if(str_replace("'","",$$item_size)!="")
			{ 
				if (!in_array(str_replace("'","",$$item_size),$new_array_size))
				{
					$size_id = return_id( str_replace("'","",$$item_size), $size_library_arr, "lib_size", "id,size_name","255");  
					//echo $$txtColorName.'='.$color_id.'<br>';
					$new_array_size[$size_id]=str_replace("'","",$$item_size);
				}
				else $size_id =  array_search(str_replace("'","",$$item_size), $new_array_size); 
			}

			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$update_id.",".$$quantity.",".$$item_group_id.",".$$item_description.",".$$item_color.",".$color_id.",".$$rate.",".$$amount.",".$$remarks.",".$$item_size.",".$size_id.",".$$uom.",'".$job_id."','".$job_no."',".$$status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		   $id++;
		 }
		// echo "10**insert into trims_finish_purchase_req_dtls (".$field_array.") values ".$data_array;die;
		$rID=sql_insert("trims_finish_purchase_req_dtls",$field_array,$data_array,0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "0**".$update_id."**".$rID."**".$tot_com_amount;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$update_id."**".$rID."**".$tot_com_amount;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$update_id."**".$rID."**".$tot_com_amount;
			}
			else{
				oci_rollback($con);
				echo "10**".$update_id."**".$rID."**".$tot_com_amount;
			}
		}
		disconnect($con);
		die;
	}
	else if($operation==1)
	{
		$user_id = $_SESSION['logic_erp']['user_id'];
		$dtls_id = str_replace("'", '', $dtls_id);
		$update_id = str_replace("'", '', $update_id);
	    $con = connect();
	    $color_id=0;
		$size_id=0;
		$new_array_color=array(); $new_array_size=array();

		if($db_type==0) mysql_query("BEGIN");

		$color_library_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
		$size_library_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");

		if(str_replace("'","",$itemColor_1)!="")
		{ 
			if (!in_array(str_replace("'","",$itemColor_1), $new_array_color))
			{
				$color_id = return_id( str_replace("'","",$itemColor_1), $color_library_arr, "lib_color", "id,color_name","255");  
				//echo $$txtColorName.'='.$color_id.'<br>';
				$new_array_color[$color_id]=str_replace("'","",$itemColor_1);
			}
			else $color_id =  array_search(str_replace("'","",$itemColor_1), $new_array_color); 
		}

		if(str_replace("'","",$itemsize_1)!="")
		{ 
			if (!in_array(str_replace("'","",$itemsize_1),$new_array_size))
			{
				$size_id = return_id( str_replace("'","",$itemsize_1), $size_library_arr, "lib_size", "id,size_name","255");  
				//echo $$txtColorName.'='.$color_id.'<br>';
				$new_array_size[$size_id]=str_replace("'","",$itemsize_1);
			}
			else $size_id =  array_search(str_replace("'","",$itemsize_1), $new_array_size); 
		}

		$field_array_up="quantity*item_group_id*item_description*item_color*color_id*item_size*size_id*rate*amount*remarks*uom*status_active*updated_by*update_date";

		$data_array_up ="".$quantity_1."*".$hdnItemGroupId_1."*".$itemdescription_1."*".$itemColor_1."*".$color_id."*".$itemsize_1."*".$size_id."*".$rate_1."*".$amount_1."*".$txtRemarks_1."*".$cboUom_1."*".$cbostatus_1."*".$user_id."*'".$pc_date_time."'";		

		$rID = sql_update("trims_finish_purchase_req_dtls",$field_array_up,$data_array_up,"id",$dtls_id,0, 0);
		
		// $add_comma=0; $fail1=0; $dataSuppArr=""; $q=1;
		/*for ($i=1;$i<=$tot_row;$i++) {
			 // $product_id="txtremark_".$i;
			$quantity="quantity_".$i;
			$item_group_id="hdnItemGroupId_".$i;
			$item_description="itemdescription_".$i;
			$color_id="hdnItemColorId_".$i;
			$rate="rate_".$i;
			$amount="amount_".$i;
			$remarks="txtRemarks_".$i;
			$size_id="hdnItemSizeId_".$i;
			$uom="cboUom_".$i;
			$status="cbostatus_".$i;
			$hdnDtlsUpdateId 		= "hdnDtlsId_".$i;

			$aa = str_replace("'",'',$$hdnDtlsUpdateId);

			$data_array_up[$aa]=explode("*",("".$$quantity."*".$$item_group_id."*".$$item_description."*".$$color_id."*".$$rate."*".$$amount."*".$$remarks."*".$$size_id."*".$$uom."*".$$status."*".$user_id."*'".$pc_date_time."'"));
				$hdn_dtls_id_arr[]=str_replace("'",'',$$hdnDtlsUpdateId);
		   $id++;
		}*/

		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "1**".$update_id."**".$rID;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$update_id."**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "1**".$update_id."**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$update_id."**".$rID;
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="purchase_manual_requisition_popup")
{
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
?>
<script>
	  function js_set_value(manual_req)
	  {
		document.getElementById('txt_manual_req').value=manual_req;
		parent.emailwindow.hide();
	  }
</script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="purchaserequisition_6"  id="purchaserequisition_6" autocomplete="off">
	<table width="800" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                    <thead>
                        <th width="150">Company Name</th>
                        <th width="150">Item Category</th>
                        <th width="200">Date Range</th>
                        <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                    </thead>
        			<tr>
                    	<td> <input type="hidden" id="txt_manual_req">
							<?
								echo create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $cbo_company_name, "");
                            ?>
                    	</td>
                   		<td>
							<?
								echo create_drop_down( "cbo_item_category_id", 170,$item_category,"", 1, "-- Select --", $selected, "","","","","","1,2,3,4,12,13,14");
                            ?>
                        </td>
                    	<td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 	</td>
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_item_category_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'manual_purchase_requisition_list_view', 'search_div1', 'finish_trims_purchase_requisition_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                       </td>
        			</tr>
             	</table>
          	</td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle">
				<? echo load_month_buttons(1);  ?>
            </td>
        </tr>
        <tr>
            <td align="center" valign="top" id="search_div1">
            </td>
        </tr>
    </table>
    </form>
   </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="manual_purchase_requisition_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $item_category_id=" and item_category_id='$data[1]'"; else $item_category_id="";//{ echo "Please Select item category."; die; }
	 if($db_type==2)
	 {
	if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = " and requisition_date between '".change_date_format($data[2], 'mm-dd-yyyy','/',1)."' and '".change_date_format($data[3],'mm-dd-yyyy','/',1)."'"; else $order_rcv_date ="";
	 }
	 if($db_type==0)
	 {
	if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = " and requisition_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $order_rcv_date ="";
	 }

	 $sql= "select id,requ_prefix_num,requisition_date,company_id,item_category_id,location_id,department_id,section_id,manual_req from trims_finish_purchase_req_mst where status_active=1 and is_deleted=0 $company  $item_category_id $order_rcv_date order by id asc";

	$company=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$location=return_library_array("select id,location_name from lib_location",'id','location_name');
	$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');

	$arr=array (2=>$company,3=>$item_category,4=>$location,5=>$department,6=>$section);

	echo  create_list_view("list_view", "Requisition No,Requisition Date,Company,Item Category,Location,Department,Section,Manual Req", "80,80,100,100,100,90,90,80","850","250",0, $sql , "js_set_value", "manual_req", "",1,"0,0,company_id,item_category_id,location_id,department_id,section_id,0", $arr , "requ_prefix_num,requisition_date,company_id,item_category_id,location_id,department_id,section_id,manual_req","finish_trims_purchase_requisition_controller","",'0,3,0,0,0,0,0,0') ;
	exit();
}

if ($action=="purchase_requisition_list_view_dtls")
{
	$item_group_library=return_library_array( "select id, item_name from lib_item_group where item_category=4 and is_deleted=0 and status_active=1", 'id', 'item_name');
	$arr=array(0=>$item_group_library,4=>$unit_of_measurement,9=>$row_status);
	$sql="select id, b.item_group_id, b.item_description, b.item_color, b.item_size, b.uom, b.quantity, b.rate, b.amount, b.remarks, b.status_active
		from trims_finish_purchase_req_dtls b
		where b.mst_id=$data";
	// echo $sql;
	echo create_list_view ("list_view","Item Group,Item Description,Item Color,Item Size,UOM,Quantity,Rate,Amount,Remarks,Status", "110,200,110,110,70,70,70,70,230","1160","300",0, $sql, "get_php_form_data", "id", "'order_details_form_data'", '', "item_group_id,0,0,0,uom,0,0,0,0,status_active", $arr , "item_group_id,item_description,item_color,item_size,uom,quantity,rate,amount,remarks,status_active", "requires/finish_trims_purchase_requisition_controller", '', '0,0,0,0,0,6,6,2,0,0','',0 );
	// create_list_view($table_id, $tbl_header_arr, $td_width_arr, $tbl_width, $tbl_height, $tbl_border, $query, $onclick_fnc_name, $onclick_fnc_param_db_arr, $onclick_fnc_param_sttc_arr, $show_sl, $field_printed_from_array_arr, $data_array_name_arr, $qry_field_list_array, $controller_file_path, $filter_grid_fnc, $fld_type_arr, $summary_flds, $check_box_all, $new_conn)
	exit();
}

if ($action=="order_details_form_data")
{
	/*$nameArray=sql_select( "select a.id,a.required_for,a.quantity,a.rate,a.amount,a.stock,a.remarks,a.status_active,b.item_account,b.item_description,b.item_size,b.item_group_id,b.unit_of_measure,b.re_order_label,c.item_name from inv_purchase_requisition_dtls a,product_details_master b,lib_item_group c where a.id='$data' and a.is_deleted=0 and a.product_id=b.id and b.item_group_id=c.id" );*/

	/*------------ADDITIONAL CODE----------------*/
	$nameArray=sql_select( "select b.id, b.item_group_id, b.item_description, b.item_color, b.item_size, b.uom, b.quantity, b.rate, b.amount, b.remarks, b.status_active
		from trims_finish_purchase_req_dtls b
		where b.id=$data and is_deleted=0");

	$item_group_library=return_library_array( "select id, item_name from lib_item_group where item_category=4 and is_deleted=0 and status_active=1", 'id', 'item_name');

	foreach ($nameArray as $row)
	{
		echo "$('#hdnDtlsId_1').val('".$row[csf('id')]."');\n";
		echo "$('#hdnItemGroupId_1').val('".$row[csf('item_group_id')]."');\n";
		echo "$('#hdnItemColorId_1').val('".$row[csf('color_id')]."');\n";
		echo "$('#txtitemgroup_1').val('".$item_group_library[$row[csf('item_group_id')]]."');\n";
		echo "$('#itemdescription_1').val('".$row[csf('item_description')]."');\n";
		echo "$('#itemColor_1').val('".$row[csf('item_color')]."');\n";
		// echo "$('#cboItemCategory_1').val('".$row[csf('item_category_id')]."');\n";
		echo "$('#itemsize_1').val('".$row[csf('item_size')]."');\n";
		echo "$('#cboUom_1').val('".$row[csf('uom')]."');\n";
		echo "$('#quantity_1').val('".$row[csf('quantity')]."');\n";
		echo "$('#rate_1').val('".$row[csf('rate')]."');\n";
		echo "$('#amount_1').val('".$row[csf('amount')]."');\n";
		echo "$('#txtRemarks_1').val('".$row[csf('remarks')]."');\n";
		echo "$('#cbostatus_1').val('".$row[csf('status_active')]."');\n";
		// echo "$('#quantity_1').val('".$row[csf('quantity')]."');\n";
		/*echo "$('#quantity_1').val('".number_format($row[csf('quantity')],3,'.','')."');\n";
		echo "$('#rate_1').val('".number_format($row[csf('rate')],2, '.', '')."');\n";*/
		/*echo "$('#txt_remarks_1').val('".$row[csf('remarks')]."');\n";
		echo "$('#stock_1').val('".$row[csf('stock')]."');\n";
		echo "$('#reorderlable_1').val('".$row[csf('re_order_label')]."');\n";
		echo "$('#txtvehicle_1').val('".$row[csf('vehicle_no')]."');\n";
		echo "$('#cbostatus_1').val('".$row[csf('status_active')]."');\n";
		echo "$('#txtbrand_1').val('".$row[csf('brand_name')]."');\n";
		echo "$('#txtmodelname_1').val('".$row[csf('model')]."');\n";
		echo "$('#cboOrigin_1').val('".$row[csf("origin")]."');\n";
 		echo "$('#hiddenid_1').val('".$row[csf('id')]."');\n";*/
 		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_purchase_requisition_dtls', 2);\n";
 	}
	exit();
}

if ($action=="purchase_requisition_popup")
{
 	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
?>
<script>
	  function js_set_value(id)
	  {
		  document.getElementById('selected_job').value=id;
		  parent.emailwindow.hide();
	  }
</script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="purchaserequisition_2"  id="purchaserequisition_2" autocomplete="off">
	<table width="900" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all" uwidth="900">
                    <thead>
                        <th width="180">Company Name</th>
                        <th width="50" style="display:none">Item Category</th>
                        <th width="100">Location</th>
                        <th width="100">Requisition No</th>
                        <th width="200">Date Range</th>
                        <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                    </thead>
        			<tr class="general">
                    	<td align="center"> <input type="hidden" id="selected_job">
							<?
								/*echo create_drop_down( "cbo_company_name", 152, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond $company_credential_cond order by company_name","id,company_name", 1, "-- Select Company --", $cbo_company_name, "",1);*/

								echo create_drop_down( "cbo_company_name", 160,"select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond $company_credential_cond order by company_name","id,company_name", 1, "-- Select --", $cbo_company_name, "load_drop_down( 'finish_trims_purchase_requisition_controller', this.value, 'load_drop_down_location','location_td');" );
                            ?>
                    	</td>
                   		<td style="display:none">
							<?
								echo create_drop_down( "cbo_item_category_id", 50, $item_category,"", 1, "-- Select --", $selected, "","","","","","1,2,3,12,13,14,24,25");
                            ?>
                        </td>
                        <td align="center" id="location_td">
							<?
								echo create_drop_down( "cbo_location_name", 160, $blank_array, "", 1, "-- Select --", $selected, "" );
                            ?>
                        </td>
                        <td align="center">

                            <input name="txt_requisition_no" id="txt_requisition_no" class="text_boxes" style="width:100px">
					 	</td>
                    	<td align="center">
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 	</td>
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_location_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_requisition_no').value, 'purchase_requisition_list_view', 'search_div', 'finish_trims_purchase_requisition_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                       </td>
        			</tr>
             	</table>
          	</td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle">
				<? echo load_month_buttons(1);  ?>
            </td>
        </tr>
        <tr>
            <td align="center" valign="top" id="search_div">
            </td>
        </tr>
    </table>
    </form>
   </div>
</body>
<script>
	load_drop_down( 'finish_trims_purchase_requisition_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location','location_td');
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="purchase_requisition_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	// if ($data[1]!=0) $item_category_id=" and item_category_id='$data[1]'"; else $item_category_id="";//{ echo "Please Select item category."; die; }

	$requisition_no=trim(str_replace("'","",$data[4]));
	//if(str_replace("'","",$requisition_no)!="") $get_cond .= "and requ_prefix_num  = '".trim(str_replace("'","",$requisition_no))."'  "; else  $get_cond="";

	if(str_replace("'","",$requisition_no)!="") $get_cond .= "and requ_no like '%$requisition_no'  "; else  $get_cond="";
	//echo $requisition_no;

	$location_cond = ($data[1]) ? " and location_id = '" . $data[1] ."'" :  "";

	if($db_type==0)
				{
	if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = " and requisition_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $order_rcv_date ="";
				}
	if($db_type==2)
	{
	if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = " and requisition_date between '".change_date_format($data[2], 'mm-dd-yyyy','/',1)."' and '".change_date_format($data[3], 'mm-dd-yyyy','/',1)."'"; else $order_rcv_date ="";
	}

	$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id='$user_id'");
	$cre_company_id = $userCredential[0][csf('company_id')];
	$cre_location_id = $userCredential[0][csf('location_id')];
	$cre_store_location_id = $userCredential[0][csf('store_location_id')];
	$cre_item_cate_id = $userCredential[0][csf('item_cate_id')];

	$credientian_cond="";
	//if($cre_company_id>0) $credientian_cond=" and company_id in($cre_company_id)";
	//if($cre_location_id>0) $credientian_cond.=" and location_id in($cre_location_id)";
	//if($cre_store_location_id>0) $credientian_cond.=" and store_name in($cre_store_location_id)";
	//if($cre_item_cate_id!="") $credientian_cond.=" and b.item_category in($cre_item_cate_id)";


	$sql= "select id, requ_prefix_num, requisition_date, company_id, location_id, manual_req
	from trims_finish_purchase_req_mst
	where status_active=1 and is_deleted=0 $company $order_rcv_date $get_cond $credientian_cond $location_cond order by id desc";
	// echo $sql;

	$company=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$location=return_library_array("select id,location_name from lib_location",'id','location_name');
	/*$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');
	$store=return_library_array("select id,store_name from lib_store_location where company_id='$data[0]' and status_active=1",'id','store_name');*/
	//$arr=array (2=>$company,3=>$item_category,4=>$location,5=>$department,6=>$section,7=>$store);

	$arr=array (2=>$company,3=>$location);

	echo create_list_view("list_view", "Requisition No,Requisition Date,Company,Location,Manual Req", "80,80,150,150","600","250",0, $sql , "js_set_value", "id", "",1,"0,0,company_id,location_id,0", $arr , "requ_prefix_num,requisition_date,company_id,location_id,manual_req","purchase_requisition_controller", '', '0,3,0,0,0,0,0,0,0') ;
	exit();
}

if ($action=="load_php_requ_popup_to_form")
{
	$nameArray=sql_select( "select id, requ_no, company_id, location_id, requisition_date, pay_mode, source, manual_req, currency_id, delivery_date, req_by, remarks, template_id, status_active
		from trims_finish_purchase_req_mst
		where id='$data' and is_deleted=0");	

	/*---------------additional code--------------*/

	/*$nameArray=sql_select( "select id,requ_no,company_id,item_category_id,location_id,division_id,department_id,section_id,requisition_date,store_name,pay_mode,source,cbo_currency,delivery_date,remarks,brand_name,model,origin,manual_req,is_approved from inv_purchase_requisition_mst where id='$data'" );*/
  /*$sql_cause="select MAX(id) as id from fabric_booking_approval_cause where  entry_form=1  and booking_id='$data' and approval_type=0 and status_active=1 and is_deleted=0";
	//echo $sql_cause; //die;
  	$app_cause = '';
	$nameArray_cause=sql_select($sql_cause);
	if(count($nameArray_cause)>0){
		foreach($nameArray_cause as $row)
		{
			$app_cause1=return_field_value("approval_cause", "fabric_booking_approval_cause", "id='".$row[csf("id")]."' and status_active=1 and is_deleted=0");
			$app_cause = str_replace(array("\r", "\n"), ' ', $app_cause1);
		}
	}*/

  foreach ($nameArray as $row) {

	  echo "document.getElementById('update_id').value				= '".$row[csf("id")]."';\n";
	  echo "document.getElementById('txt_requisition_no').value 		= '".$row[csf("requ_no")]."';\n";
	  //echo "document.getElementById('txt_not_approve_cause').value 		= '".$app_cause."';\n";
	  echo "document.getElementById('cbo_company_name').value 			= '".$row[csf("company_id")]."';\n";
	  //echo "$('#cbo_company_name').attr('disabled','true')".";\n";
	  //echo "document.getElementById('cbo_item_category_id').value 		= '".$row[csf("item_category_id")]."';\n";
	  //echo "$('#cbo_item_category_id').attr('disabled',true);\n";
	  //echo "show_list_view('".$row[csf("item_category_id")]."','item_category_details', 'item_category_div', 'requires/purchase_requisition_controller', '' );\n";
	  echo "load_drop_down( 'requires/finish_trims_purchase_requisition_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location','location_td');\n";
	  echo "document.getElementById('cbo_location_name').value			= '".$row[csf("location_id")]."';\n";
	  
	  echo "document.getElementById('txt_req_date').value				= '".change_date_format($row[csf("requisition_date")])."';\n";
	  
	  echo "document.getElementById('cbo_pay_mode').value				= '".$row[csf("pay_mode")]."';\n";
	  echo "document.getElementById('cbo_source').value				= '".$row[csf("source")]."';\n";
	  echo "document.getElementById('txt_manual_req').value				= '".$row[csf("manual_req")]."';\n";
	  echo "document.getElementById('cbo_currency_name').value			= '".$row[csf("currency_id")]."';\n";
	  echo "document.getElementById('txt_date_delivery').value			= '".change_date_format($row[csf("delivery_date")])."';\n";
	  echo "document.getElementById('txt_req_by').value					= '".$row[csf("req_by")]."';\n";
	  echo "document.getElementById('txt_remarks').value				= '".$row[csf("remarks")]."';\n";
	  echo "document.getElementById('cbo_template_id').value				= '".$row[csf("template_id")]."';\n";

	  echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_purchase_requisition',1);\n";
  }

  exit();
}


if($action=="generate_report_1") // Print Report
{
	?>
	<link rel="stylesheet" href="../css/style_common.css" type="text/css" />
	<?
	extract($_REQUEST);
	$data=explode('*',$data);
	//print($data[5]);
	$update_id=$data[1];
	// $formate_id=$data[3];
	$template_id=$data[3];
	$company=$data[0];
	$location=$data[7];
	/*$sql="select id, requ_no, item_category_id, requisition_date, location_id, delivery_date, source, manual_req, department_id, section_id, store_name, pay_mode, cbo_currency, remarks,inserted_by from trims_finish_purchase_req_mst where id=$update_id";*/
	$sql="select id, requ_no, requisition_date, location_id, delivery_date, source, manual_req, pay_mode, currency_id, remarks, req_by
		from trims_finish_purchase_req_mst
		where id=$update_id";
	// echo $sql;
	$dataArray=sql_select($sql);
 	// $company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	// $supplier_array=return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
	// $country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');

	// $country_full_name = return_library_array("SELECT id,country_name from lib_country", "id", "country_name");
	$company_sql = "SELECT * FROM lib_company WHERE id = $data[0]  AND is_deleted=0 AND status_active=1 ORDER BY company_name ASC";
	$result = sql_select( $company_sql );
	foreach( $result as $row  )
	{
		if($row[csf("company_name")])	$company_name = $row[csf("company_name")].', ';
		if($row[csf("level_no")])		$level_no	= $row[csf("level_no")].', ';
		if($row[csf("plot_no")])		$plot_no 	= $row[csf("plot_no")].', ';
		if($row[csf("road_no")]) 		$road_no 	= $row[csf("road_no")].', ';
		if($row[csf("block_no")]!='')	$block_no 	= $row[csf("block_no")].', ';
		if($row[csf("zip_code")]!='')	$zip_code 	= $row[csf("zip_code")].', ';
		if($row[csf("city")]!='') $city 			= $row[csf("city")];
		if($row[csf("country_id")]!='')	$country 	= $country_arr[$row[csf("country_id")]].'.';
		if($row[csf("contact_no")]!='')	$contact_no = $row[csf("contact_no")];
		if($row[csf("email")]!='')		$email 		= $row[csf("email")];
		if($row[csf("website")]!='')	$website 	= $row[csf("website")].'.'; 
	}
	$head_ofice= "House #".$plot_no."Road #".$road_no."Sector #".$block_no.$zip_code.$country;
	$company_address="Head Office :".$head_ofice.'</br> Factory address : '.$city.'</br> Email : '.$email.'</br> Mobile : '.$contact_no;

	/*$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
	$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');*/

	$pay_cash=$dataArray[0][csf('pay_mode')];
	$inserted_by=$dataArray[0][csf('inserted_by')];
	$com_dtls = fnc_company_location_address($company, $location, 2);

	$item_group_library=return_library_array( "select id, item_name from lib_item_group where item_category=4 and is_deleted=0 and status_active=1", 'id', 'item_name');
	// $com_dtls = substr($com_dtls[1], 0, strpos($com_dtls[1], ", Website:"));
	?>
	<table width="1200">
		<tr rowspan="2">
			<td colspan="6"></td>
		</tr>
		<tr>
			<td colspan="1" rowspan="3" style="width: 100px;">
				<img src="../../<? echo $com_dtls[2]; ?>" height="60" width="200" style="float:left;">
			</td>
			<td colspan="2" align="center" style="font-size:x-large">
				<strong><? echo $company_name; ?></strong>
			</td>
			<td colspan="3" align="center"></td>
		</tr>
		<tr>
			<td colspan="3" align="center" style="font-size:large;"><? echo $company_address; ?> </td>
			<td colspan="3" align="center"></td>
		</tr>
		<tr>
            <td colspan="2" style="font-size:x-large; text-align:center;" align="center">
            	<strong ><? echo $data[2]; ?></strong>
            </td>
            <td colspan="3" align="center"></td>
        </tr>
		<tr><td colspan="6">&nbsp;</td></tr>
	</table>
	<table width="1200">
		<tr>
			<td><strong>Req. No</strong></td>
			<td><strong>:</strong></td>
			<td><? echo $dataArray[0][csf('requ_no')];?></td>
			<td><strong>Req. Date</strong></td>
			<td><strong>:</strong></td>
			<td><? if($dataArray[0][csf('requisition_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('requisition_date')]);?></td>
			<td><strong>Pay Mode</strong></td>
			<td><strong>:</strong></td>
			<td><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
		</tr>
		<tr>
			<td><strong>Manual Req.</strong></td>
			<td><strong>:</strong></td>
			<td><? echo $dataArray[0][csf('manual_req')]; ?></td>
			<td><strong>Req. By</strong></td>
			<td><strong>:</strong></td>
			<td><? echo $dataArray[0][csf('req_by')]; ?></td>
			<td><strong>Currency</strong></td>
			<td><strong>:</strong></td>
			<td><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
		</tr>
		<tr>
			<td><strong>Remarks</strong></td>
			<td><strong>:</strong></td>
			<td colspan="4"><? echo $dataArray[0][csf('remarks')]; ?></td>
		</tr>
	</table>
	<table cellspacing="0" width="1300"  border="1" rules="all" class="rpt_table" >
		<thead bgcolor="#dddddd" align="center">
			<tr>
				<th width="30">SL</th>
				<th width="150">Item Group</th>
				<th width="250">Item Description</th>
                <th width="80">Item Color</th>
				<th width="80">Item Size</th>
                <th width="80">Order UOM</th>
                <th width="60">Quantity</th>
                <th width="70">Rate</th>
                <th width="70">Amount</th>
                <th>Remarks</th>
			</tr>
		</thead>
		<tbody>
		<?
		$item_name_arr=return_library_array("select id, item_name from lib_item_group",'id','item_name');
		$receive_array=array();

		$i=1;
		$sql= "select a.id, b.id as dtls_id, b.item_group_id, b.item_description, b.item_size, b.item_color, b.uom, b.quantity, b.rate, b.amount, b.remarks
        from trims_finish_purchase_req_mst a, trims_finish_purchase_req_dtls b
        where a.id = b.mst_id and a.id=$update_id and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0";
	    // echo $sql;
		$sql_result=sql_select($sql);
		foreach($sql_result as $row)
		{
			$all_data_array[$row[csf('dtls_id')]]['id'] = $row[csf('id')];
			$all_data_array[$row[csf('dtls_id')]]['item_group_id'] = $row[csf('item_group_id')];
			$all_data_array[$row[csf('dtls_id')]]['item_description'] = $row[csf('item_description')];
			$all_data_array[$row[csf('dtls_id')]]['item_color'] = $row[csf('item_color')];
			$all_data_array[$row[csf('dtls_id')]]['item_size'] = $row[csf('item_size')];
			$all_data_array[$row[csf('dtls_id')]]['uom'] = $row[csf('uom')];
			$all_data_array[$row[csf('dtls_id')]]['quantity'] = $row[csf('quantity')];
			$all_data_array[$row[csf('dtls_id')]]['rate'] = $row[csf('rate')];
			$all_data_array[$row[csf('dtls_id')]]['amount'] = $row[csf('amount')];
			$all_data_array[$row[csf('dtls_id')]]['remarks'] = $row[csf('remarks')];
		}

		$total_amount=0;$last_qnty=0;$total_reqsit_value=0;
		$total_monthly_rej=0;$total_monthly_iss=0;$total_stock=0;
		foreach ($all_data_array as $dtls_id => $row) {
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$quantity=$row['quantity'];
			$quantity_sum += $quantity;
			$amount = $row['quantity'] * $row['rate'];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:20px">
				<td align="center"><? echo $i; ?></td>
                <td><div style="word-wrap:break-word:50px;"><? echo $item_group_library[$row['item_group_id']]; ?></div></td>
                <td><p><? echo $row["item_description"];?> </p></td>
                <td><p><?php echo $row["item_color"]; ?></p></td>
                <td><p><? echo $row["item_size"]; ?>&nbsp;</p></td>
                <td><p><? echo $unit_of_measurement[$row["uom"]]; ?></p></td>
                <td align="right"><? echo $row['quantity']; ?></td>
                <td align="right"><? echo $row['rate']; ?></td>
                <td align="right"><? echo number_format($amount, 2); ?></td>
                <td><? echo $row['remarks']; ?></td>
			</tr>
			<?
			$total_req_qnty += $row['quantity'];
			$total_req_amt += $amount;
			// $total_stock += $row['stock'];
			$i++;
		}

		$carrency_id = $dataArray[0][csf('currency_id')];
		if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";}
		?>
		</tbody>
		<tr bgcolor="#B0C4DE">
			<td align="right" colspan="8"><strong>Total :</strong></td>
			<td align="right"><strong><? echo number_format($total_req_amt, 2); ?></strong></td>
            <td></td>
		</tr>
	</table>
	<span><strong>Total Amount (In Word): <? echo number_to_words(number_format($total_req_amt, 2), $currency[$dataArray[0][csf('currency_id')]], $paysa_sent); ?></strong></span>
	<br>
	<?
	echo signature_table(25, $company, "1200px",$template_id,70,$user_lib_name[$inserted_by]);
	exit();
}