<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$sample_name_library=return_library_array( "select sample_name,id from lib_sample where is_deleted=0 and status_active=1 order by sample_name", "id", "sample_name"  );
$item_arrs=return_library_array("select id,item_name from lib_garment_item where status_active=1 and is_deleted=0 order by item_name","id","item_name");
$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

function my_old($dob){
	$now = date('d-m-Y');
	$dob = explode('-', $dob);
	$now = explode('-', $now);
	$mnt = array(1 => 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	if (($now[2]%400 == 0) or ($now[2]%4==0 and $now[2]%100!=0)) $mnt[2]=29;
	if($now[0] < $dob[0]){
		$now[0] += $mnt[$now[1]-1];
		$now[1]--;
	}
	if($now[1] < $dob[1]){
		$now[1] += 12;
		$now[2]--;
	}
	if($now[2] < $dob[2]) return false;
	return  array('year' => $now[2] - $dob[2], 'mnt' => $now[1] - $dob[1], 'day' => $now[0] - $dob[0]);
}

if($action=="save_update_delete")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$user_id = $_SESSION['logic_erp']['user_id'];
	$unclose_id=str_replace("'","",$unclose_id);
	
		//die();
	
		if($only_full==false)
		{
			//$closing_status=1;
		}
		// $closing_status=$unclose_id;
//	echo "10**".$unclose_id.'=='.$only_full;die;
	if ($operation==0)
	{
		/*		if (is_duplicate_field( "inv_pur_req_id","inv_reference_closing","inv_pur_req_id=$txt_subsection" )==1)
		{
			echo "11**0"; die;
		}*/
		$con = connect();

		if($db_type==0)
		{

			mysql_query("BEGIN");
		}
		$type=str_replace("'","",$cbo_ref_type);
		
		if($type==69 || $type==70)
		{
		  $db_table='inv_purchase_requisition_mst';
		}
		elseif($type==4)
		{
		  $db_table='inv_receive_master';
		}
		elseif($type==117)
		{
			$db_table='sample_development_dtls';
		}
		elseif($type==104)
		{
			$db_table='com_pi_master_details';

		}
		elseif($type==105)
		{
			$db_table='com_btb_lc_master_details';
		}
		elseif($type==106)
		{
			$db_table='com_export_lc';
		}
		elseif($type==2)//Knit Closing
		{
			$db_table='inv_receive_master';
		}
		elseif($type==163)
		{
			$db_table_1='wo_po_break_down';
			$db_table_2='wo_po_color_size_breakdown';
			$db_table_3='pro_ex_factory_mst';
		}
		elseif($type==370)//Sweater
		{
			$db_table_1='wo_po_break_down';
			$db_table_2='wo_po_color_size_breakdown';
			$db_table_3='pro_garments_production_mst';
		}
		/*elseif($type==107)
		{
			$db_table='sample_development_dtls';
		}*/
		else
		{
			$db_table='wo_non_order_info_mst';
			//$db_table_rcv_trns='inv_receive_master';
		}
		//txt_subsection*cbo_section*cbo_status*txt_remark*update_id
		$id=return_next_id( "id", "inv_reference_closing", 1 ) ;//closing_status
		$field_array="id,company_id,closing_date,reference_type,closing_status,inv_pur_req_mst_id,mrr_system_no,inserted_by,insert_date";

		if($type==4)
		{
			$field_array_update="ref_closing_status*updated_by*update_date";
		}
		elseif($type==117)
		{
			$field_array_update="is_complete_prod";
		}
		elseif($type==163 || $type==370)
		{
			$field_array_update="shiping_status*updated_by*update_date";
			$field_array_update3="shiping_status*updated_by*update_date";
			$field_array_update4="ref_closing_status*updated_by*update_date";
		}
		else
		{
			$field_array_update="ref_closing_status";
		}
		//print_r($total_id); die;
		$totid=str_replace("'","",$total_id);
		$all_id= explode("***",$totid);
		//echo "10**";print_r($all_id); die;
		$totids="";
		$all_id_arr_1=array();
		$all_id_arr_2=array();
		foreach($all_id as $all_ids)
		{
			list($ids,$mrr_no)= explode('**', $all_ids);
			$all_id_arr_1[] =$ids;
			$all_id_arr_2[] =$mrr_no;
			$totids.=$ids.',';
			$mstIDtotids.=$mrr_no.',';
		}
		$totidss=chop($totids,",");
		$MIdtotids=chop($mstIDtotids,",");
		//echo "10**=".$totidss.'='.$MIdtotids; die;
		//echo $all_id_arr[1];



		$data_array="";
		for($j=0;$j<count($all_id_arr_1);$j++)
  		{
			//$data_array.="(".$id.",".$cbo_company_name.",".$txt_ref_cls_date.",".$cbo_ref_type.",'".$all_id_arr_1[$j]."','".$all_id_arr_2[$j]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";


			if($data_array==''){
				$data_array.="(".$id.",".$cbo_company_name.",".$txt_ref_cls_date.",".$cbo_ref_type.",".$unclose_id.",'".$all_id_arr_1[$j]."','".$all_id_arr_2[$j]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
			else
			{
				$data_array.=",(".$id.",".$cbo_company_name.",".$txt_ref_cls_date.",".$cbo_ref_type.",".$unclose_id.",'".$all_id_arr_1[$j]."','".$all_id_arr_2[$j]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}



			if($type==163)
			{
				$data_array_update="".'3'."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				$data_array_update3="".'3'."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			}
			else if($type==370)
			{
				$data_array_update="".'3'."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				$data_array_update3="".'3'."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				$data_array_update4="".'1'."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			}
			elseif($type==4)
			{
				$data_array_update="".'1'."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

			}
			else
			{
				$data_array_update="".'1'."";
			}
			$id=$id+1;
		}
		//print_r($data_array_update);die;
		
		//echo "10**insert into inv_reference_closing (".$field_array.") values".$data_array; die;
		$rID=sql_insert("inv_reference_closing", $field_array, $data_array, 1);
		if($rID)
		{
			$flag=1;
		}
		else
		{
			$flag=0;
		}
	//	echo "10**".$flag.'='.$type.'='.$only_full; die;
			 
		if($flag)
		{
			if($type==163)
			{
				if($only_full=='true')
				{
					$ex_fac_sql="SELECT po_break_down_id  from pro_ex_factory_mst where status_active=1 and po_break_down_id in ($totidss) ";
					$ex_fac_arr=array();
					foreach(sql_select($ex_fac_sql) as $v )
					{
						$ex_fac_arr[$v[csf("po_break_down_id")]]=$v[csf("po_break_down_id")];
					}
					$all_po_arr=explode(",", $totidss);
					//echo "10**".$totidss;print_r($ex_fac_arr);die;
					foreach($all_po_arr as $key)
					{
						
						$po_in_exfac=$ex_fac_arr[$key];
						$sp_status=1;
						if($po_in_exfac)
							$sp_status=2;

						$rID_up_1=execute_query("UPDATE wo_po_break_down SET shiping_status='$sp_status', updated_by='$user_id',update_date='$pc_date_time' where id=$key",1);
						$rID_up_2=execute_query("UPDATE wo_po_color_size_breakdown SET shiping_status='$sp_status', updated_by='$user_id',update_date='$pc_date_time' where po_break_down_id=$key",1);
						$rID_up_3=execute_query("UPDATE pro_ex_factory_mst SET shiping_status='$sp_status', updated_by='$user_id',update_date='$pc_date_time' where po_break_down_id=$key",1);
					}

				}
				else
				{
					$rID_up_1=sql_multirow_update($db_table_1, $field_array_update, $data_array_update,"id",$totidss,1);
					$rID_up_2=sql_multirow_update($db_table_2, $field_array_update, $data_array_update3,"po_break_down_id",$totidss,1);
					$rID_up_3=sql_multirow_update($db_table_3, $field_array_update3, $data_array_update3,"po_break_down_id",$totidss,1);
				}

					
			}
			else if($type==370)//Sweater
			{
				if($only_full=='true')
				{
					$qc_sql="SELECT b.order_id  from pro_gmts_cutting_qc_mst a,pro_gmts_cutting_qc_dtls b where a.id=b.mst_id and a.status_active=1 and b.order_id in ($totidss) and a.garments_nature=100";
					$qc_kint_arr=array();
					foreach(sql_select($qc_sql) as $v )
					{
						$qc_kint_arr[$v[csf("order_id")]]=$v[csf("order_id")];
					}
					$all_po_arr=explode(",", $totidss);
					//$all_po_ids=implode(",",array_unique(explode(",", $totidss)));
					//echo "10**".$totidss;print_r($qc_kint_arr).'='.$qc_sql;die;
					foreach($all_po_arr as $key)
					{
						
						$po_in_qc_knit=$qc_kint_arr[$key];
						$sp_status=1;
						if($po_in_qc_knit)
							$sp_status=2;

						$rID_up_1=execute_query("UPDATE wo_po_break_down SET shiping_status='$sp_status', updated_by='$user_id',update_date='$pc_date_time' where id=$key",1);
						$rID_up_2=execute_query("UPDATE wo_po_color_size_breakdown SET shiping_status='$sp_status', updated_by='$user_id',update_date='$pc_date_time' where po_break_down_id=$key",1);
						$rID_up_3=execute_query("UPDATE pro_garments_production_mst SET ref_closing_status='0', updated_by='$user_id',update_date='$pc_date_time' where po_break_down_id=$key and production_type=52",1);
						$rID_up_3=execute_query("UPDATE pro_gmts_cutting_qc_dtls SET ref_closing_status='0', updated_by='$user_id',update_date='$pc_date_time' where order_id=$key",1);
					}

				}
				else
				{
					$rID_up_1=sql_multirow_update($db_table_1, $field_array_update, $data_array_update,"id",$totidss,1);
					$rID_up_2=sql_multirow_update($db_table_2, $field_array_update, $data_array_update3,"po_break_down_id",$totidss,1);
				//	$rID_up_3=sql_multirow_update($db_table_3, $field_array_update4, $data_array_update4,"po_break_down_id",$totidss,1);
					$rID_up_3=execute_query("UPDATE pro_garments_production_mst SET ref_closing_status='1', updated_by='$user_id',update_date='$pc_date_time' where po_break_down_id in($totidss) and production_type=52",1);
					$rID_up_3=execute_query("UPDATE pro_gmts_cutting_qc_dtls SET ref_closing_status='1', updated_by='$user_id',update_date='$pc_date_time' where order_id in($totidss)",1);
				}

					
			}
			else if($type==105)//BTB LC
			{
				if($only_full=='true')
				{
					
					$all_po_arr=explode(",", $totidss);
					//$all_po_ids=implode(",",array_unique(explode(",", $totidss)));
					//echo "10**".$totidss;die;
					foreach($all_po_arr as $key)
					{
						$rID_up=execute_query("UPDATE com_btb_lc_master_details SET ref_closing_status='0', updated_by='$user_id',update_date='$pc_date_time' where id=$key",1);
					}

				}
				else
				{
				
					$rID_up=execute_query("UPDATE com_btb_lc_master_details SET ref_closing_status='1', updated_by='$user_id',update_date='$pc_date_time' where id in($totidss)",1);
				}

					
			}
			else if($type==106)//Export LC
			{
				if($only_full=='true')
				{
					
					$all_po_arr=array_unique(explode(",", $totidss));
					//$all_po_ids=implode(",",array_unique(explode(",", $totidss)));
					//echo "10**".$totidss;die;
					foreach($all_po_arr as $key)
					{
						$rID_up=execute_query("UPDATE com_export_lc SET ref_closing_status='0', updated_by='$user_id',update_date='$pc_date_time' where id=$key",1);
					}

				}
				else
				{
				
					$rID_up=execute_query("UPDATE com_export_lc SET ref_closing_status='1', updated_by='$user_id',update_date='$pc_date_time' where id in($totidss)",1);
				}

					
			}
			else if($type==2)//Kniting Closing...
			{
				if($only_full=='true')
				{
					
					$all_mst_arr=array_unique(explode(",", $MIdtotids));
					//$all_po_ids=implode(",",array_unique(explode(",", $totidss)));
					//echo "10**".$totidss;die;
					foreach($all_mst_arr as $key)
					{
						$rID_up=execute_query("UPDATE inv_receive_master SET ref_closing_status='0', updated_by='$user_id',update_date='$pc_date_time' where id=$key and entry_form=2",1);
					}

				}
				else
				{
				
					
					$rID_up=execute_query("UPDATE inv_receive_master SET ref_closing_status='1', updated_by='$user_id',update_date='$pc_date_time' where id in($MIdtotids) and entry_form=2",1);
					//echo $rID_up."10**UPDATE inv_receive_master SET ref_closing_status='1', updated_by='$user_id',update_date='$pc_date_time' where id in($MIdtotids)";die;
				}

					
			}
			else
			{
				$rID_up=sql_multirow_update($db_table, $field_array_update, $data_array_update,"id",$totidss,1);
			}
		}
		//echo $db_table." ".$field_array_update.' '.$data_array_update.' '.$totid.'dd'.$rID_up;die;
		//function sql_multirow_update($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues, $commit)
		//die;

		//echo "10** $rID && $rID_up";oci_rollback($con);die;
		//echo $db_table." ".$field_array_update.' '.$data_array_update.' '.$totid.'dd'.$rID_up;die;
		//function sql_multirow_update($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues, $commit)
		//die;

		if($type==163 || $type==370)
		{

			if($db_type==0)
			{
				if($rID && $rID_up_1 && $rID_up_2)
				{
					mysql_query("COMMIT");
					echo "0**".$type;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$type;
				}
			}

			if($db_type==2 || $db_type==1 )
			{
				if($rID && $rID_up_1 && $rID_up_2)
				{
					oci_commit($con);
					echo "0**".$type;
				}
				else
				{
					oci_rollback($con);
					echo "10**".$type;
				}
			}

		}
		else
		{
			if($db_type==0)
			{
				if($rID && $rID_up)
				{
					mysql_query("COMMIT");
					echo "0**".$type;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$type;
				}
			}

			if($db_type==2 || $db_type==1 )
			{
				if($rID && $rID_up)
				{
					oci_commit($con);
					echo "0**".$type;
				}
				else
				{
					oci_rollback($con);
					echo "10**".$type;
				}
			}
		}
	disconnect($con);
	die;
	}

	elseif ($operation==1)//Update Here but not used
	{
		if (is_duplicate_field( "inv_pur_req_id","inv_reference_closing","inv_pur_req_id=$txt_subsection and id!=$update_id and is_deleted=0") == 1)
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

			$field_array="subsection_name*section_id*status_active*remark*updated_by*update_date";
			$data_array="".$txt_subsection."*".$cbo_section."*".$cbo_status."*".$txt_remark."*".$_SESSION['logic_erp']['user_id']."*".$insert_update_date_time."";
			//echo "update lib_subsection set(".$field_array.")=".$data_array[0]; die;
			$rID=sql_update("lib_subsection", $field_array, $data_array,"id",$update_id,1);
			//echo $rID; die;
			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT");
					echo "1**".$rID;
				}
				else
				{
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
				else
				{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
			disconnect($con);
			die;
		}
	}
	else if ($operation==2)   // Delete Here
	{

		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*".$insert_update_date_time."*0*1";

		$rID=sql_delete("lib_subsection",$field_array,$data_array,"id",$update_id,1);
		//$rID=sql_delete("tbl_department_test",$field_array,$data_array,"id","$update_id",0);

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

if($action=="show_details")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company = str_replace("'","",$cbo_company_name);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$only_full=str_replace("'","",$only_full);
	$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");
	$lib_supplier_arr=return_library_array( "select id,supplier_name from lib_supplier where is_deleted=0 and status_active=1", "id", "supplier_name");
	if($only_full=='true') $unclose_id=1;else $unclose_id=0;
	//echo $only_full.'dd'.$unclose_id;
 	?>
 	<script>
	 	//check all
		/*function check_all_data()
		{
		var tbl_row_count = document.getElementById( 'tbl_list').rows.length;

		tbl_row_count = tbl_row_count - 1;
		alert(tbl_row_count);
			for( var i = 1; i <= tbl_row_count; i++)  {


			}
		}*/

		//this function for only ID
		var selected_id = new Array;
		function js_set_value(str)
		{
			if( jQuery.inArray( $('#chk_id_' + str).val(), selected_id ) == -1 )
			{
				selected_id.push($('#chk_id_' + str).val());
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#chk_id_' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + '***';
			}
			id = id.substr( 0, id.length - 1 );
			$('#total_id').val( id );
		}



		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value(i);
			}

			if($('#tbl_list_head thead tr #all_chk').is(':checked'))
			{
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = true;
				});
			}
			else
			{
				//var selected_id = new Array;
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = false;
				});
				//$('#total_id').val('');
			}
		}
		var ref_type=$('#cbo_ref_type').val();
		var tableFilters1 ="";

			$(document).ready(function() {
				var ref_type=$('#cbo_ref_type').val();
				if(ref_type!=163)
	            setFilterGrid('tbl_list',-1,tableFilters1);
	            //setFilterGrid('tbl_list',-1);
				//reset_hide_field();
	        });
	</script>
       
       <table width="100%" id="tbl_list_head" class="rpt_table" rules="all" border="1" align="center">
			<thead>
				<tr>
					<th width="25"> <input style="margin-left:4px;" type="checkbox" id="all_chk" onclick="check_all_data();"/>
                     &nbsp;
                     <?
					 //if($type==163)
					// {
					if($only_full=='true')
						{
							$unclose_id=0;
						}
						else $unclose_id=1;
					 //}
					 ?>
               		 <input style="margin-left:4px; width:20px;" type="hidden" id="unclose_id" name="unclose_id" value="<? echo $unclose_id;?>"/>
                    </th>
					<th width="30">SL</th>
					<th width="<? if($type==163) echo "80"; else echo "110";?>">
					<? 
					if($type==69 || $type==70 || $type==117){ echo 'Requisition No';}
					elseif($type==4){echo 'Receive Number';}
					elseif($type==104){ echo 'PI No';}
					elseif($type==105){ echo 'BTB LC No';}
					elseif($type==106){ echo 'Export LC No';}
					elseif($type==163){ echo 'Buyer';}
					else{ echo 'Purchase Order No';} 
					?>
                    </th>
					<th width="<? if($type==117 || $type==163){echo "100" ;} else {echo "65";}?>">
					<? 
					if($type==69 || $type==70 || $type==117){ echo 'Requisition Date';}
					elseif($type==4){echo 'Receive Date';}
					elseif($type==104){ echo 'PI Date';}
					elseif($type==105){ echo 'BTB LC Date';}
					elseif($type==106){ echo 'Export LC Date';}
					elseif($type==163){ echo 'Job Number';}
					else{ echo 'Purchase Order Date';} ?>
                    </th>
                    <?php
                    if($type == 163)
                    {
                    	?>
                        <th width="80">Internal Ref</th>
                        <?php
						
                    }
					?>
					<th width="<? if($type==117){echo "100" ;} else {echo "120";}?>">
					<? 
					if($type==105 ){ echo 'Supplier';}
					elseif($type==106 ){ echo 'Buyer';}
					else if($type==163){ echo "Po No";}
					elseif($type==117){echo 'Sample Name';}
					else{echo 'Source';} ?>
					</th>
					<th width="<? if($type==163){echo "100";}else{echo "110";} ?>">
					<?
					if($type==117){ echo "Item Name";} 
					elseif($type==163){ echo "Shipment Date";} 
					elseif($type==4 ){ echo 'Supplier';}
					else { echo "Age";} 
					?>
					</th>
                    <th width="<? if($type==163){echo "80";}else{echo "90";} ?>">
					<? 
					if($type==104 || $type==105){ echo 'Item Category';}
					elseif($type==106){ echo 'Tolerance %';}
					elseif($type==117){ echo 'Color';}
					elseif($type==163){ echo 'Po Qty';}
					else{ echo 'Pay Mode';} 
					?>
                    </th>

                    <?
                    if($type!=163)
                    {
                    	?>
						<th width="<? if($type==117){echo "100" ;} else {echo "80";}?>">
						<? 
						if($type==102 || $type==103){ echo 'PO Value';}
						elseif($type==104){ echo 'PI Value';}
						elseif($type==117){ echo 'Sample Req Qty';}
						elseif($type==105 || $type==106){ echo 'LC Value';}
						elseif($type==4){ echo 'Receive Qty';}
						else { echo 'PO Qty';} 
						?>
	                    </th>
	                    <?
	                    if($type==104)
						{ 
							echo '<th width="80">LC Value</th>';
							echo '<th width="80">Acceptance Value</th>';
						}
					    ?>
						<th width="<? if($type==117){echo "120" ;} else {echo "80";}?>">
						<? 
						if($type==102 || $type==103){ echo 'PI / Receive Value';}
						elseif($type==104 || $type==105){ echo 'Receive Value';}
						elseif($type==106){ echo 'Invoice Value';}
						elseif($type==117){ echo 'Delv Start Date';}
						elseif($type==4){ echo 'Item Category';}
						else { echo 'PI / Receive Qty';} 
						?>
	                    </th>
	                    <?
	                    if($type==105 || $type==106){ echo '<th width="65">LC Exp. Date</th>';}
						elseif($type==4){ echo '<th width="65">Loan Party</th>';}
						if($type==70 || $type==100 || $type==101 || $type==102 || $type==103 || $type==104 || $type==105){ $th_width='width="100"';} 
						elseif($type==4){ $th_width='width="80"';}
						
						if($type==105){ 
							echo '<th width="100">Receive Rtn Value</th>';
							echo '<th width="100">Actual Rcv Value</th>';
						}
						?>
						<th <? echo $th_width;?>>
						<?
                        if($type==102 || $type==103 || $type==104 || $type==105 || $type==106){ echo 'Balance Value';}
                        elseif($type==117){ echo 'Delv End Date';}
						elseif($type==4){ echo 'Receive Basis';}
						else { echo 'Balance Qty';} 
						?>
						</th>
	                   	 <?
	                    if($type==106){ echo '<th width="100">Value %</th>';}
					}
					else
					{
						?>
						<th width="60">Ship Qty</th>
						<th width="110">Ship Bal. Qty</th>
						<th width="110">Shipping Status</th>
						<th>Team Leader</th>
						<?
					}
				   	?>
				</tr>
			</thead>
            </table>

            <div style="overflow:scroll; height:350px; width:101.5%;">
            <table id="tbl_list" align="center" class="rpt_table" rules="all" border="1" style="width:100%;" >
            <tbody>
            	<input style="width:400px;" type="hidden" id="total_id" name="total_id"/>
				<?
				
				$i=1;
				if($type==4) //Dyes and Chemicals Receive
				{
					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.receive_date between ".$txt_date_from." and ".$txt_date_to."";


					$rec_sql = "select a.id, a.recv_number, a.receive_date, a.receive_basis, a.receive_purpose, a.item_category, a.buyer_id, a.lc_no, a.loan_party , a.supplier_id,a.pay_mode, a.source, sum(b.cons_quantity) as rcv_qnty
					from inv_receive_master a, inv_transaction b
					where a.id=b.mst_id  and a.item_category in(5,6,7,22,23) and a.entry_form=4 and a.company_id = $company and a.ref_closing_status = 0 and b.transaction_type=1 group by a.id, a.recv_number, a.receive_date, a.receive_basis, a.receive_purpose, a.item_category, a.buyer_id, a.lc_no, a.loan_party,a.supplier_id, a.pay_mode, a.source order by a.id";
					//echo $rec_sql;
					$result = sql_select($rec_sql);
				}
				if($type==69) //Purchase Requisition *PENDING
				{
					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.lc_date between ".$txt_date_from." and ".$txt_date_to."";
					echo '<br><b>Incompleted<b>';
				}
				if($type==70) //Yarn Purchase Requisition
				{
					$job_sql=sql_select("select a.id, b.po_quantity as po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company");
					$job_qnty_arr=array();
					foreach($job_sql as $row)
					{
						$job_qnty_arr[$row[csf("id")]]+=$row[csf("po_quantity")];
					}
					//echo $job_qnty_arr[2762].kayum;
					unset($job_sql);
					
					if($db_type==0) $select_job=" group_concat(b.job_id) as job_id"; else  $select_job=" rtrim(xmlagg(xmlelement(e,b.job_id,',').extract('//text()') order by b.job_id).GetClobVal(),',') AS job_id ";
					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.requisition_date between ".$txt_date_from." and ".$txt_date_to."";

					$result= sql_select("select a.id,a.requ_no as requ_and_wo_no,a.requisition_date as requ_and_wo_date,a.source,a.pay_mode,SUM(b.quantity) as total_quantity,b.mst_id,  $select_job from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b where a.item_category_id=1 and a.company_id=$company and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.ref_closing_status=0 $wo_date_cond group by a.id,a.requ_no,a.requisition_date,a.source,a.pay_mode,b.mst_id");
				}
				if($type==100) //Yarn Purchase Order
				{
					$job_sql=sql_select("select a.id, b.po_quantity as po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company");
					$job_qnty_arr=array();
					foreach($job_sql as $row)
					{
						$job_qnty_arr[$row[csf("id")]]+=$row[csf("po_quantity")];
					}
					//echo $job_qnty_arr[2762].kayum;
					unset($job_sql);
					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.wo_date between ".$txt_date_from." and ".$txt_date_to."";
					$pi_qty_arr=return_library_array( "select b.work_order_id, sum(b.quantity) as qty from com_pi_master_details a,com_pi_item_details b where a.id=b.pi_id and a.importer_id=$company and a.item_category_id=1 and a.pi_basis_id= and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=11 group by b.work_order_id ", "work_order_id", "qty");
					$receive_qty_arr=return_library_array( "select a.pi_wo_batch_no,sum(a.order_qnty) as qty from inv_transaction a,inv_receive_master b
						where a.mst_id=b.id and a.company_id=$company and  a.item_category=1 and a.transaction_type=1 and b.item_category=1
						and b.entry_form=1 and b.receive_basis=2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.pi_wo_batch_no ", "pi_wo_batch_no", "qty");
					//print_r($receive_qty_arr);

					 /*$result_aprvl_necty_date_arr= sql_select(" select a.setup_date,b.page_id,b.approval_need from approval_setup_mst a, approval_setup_dtls b  where a.id=b.mst_id and a.company_id=$company and b.page_id=15 and b.approval_need=1 and a.is_deleted=0 and a.status_active=1");
					 $aprvl_necty_date="";
					 foreach($result_aprvl_necty_date_arr as $row)
					 {
						$aprvl_necty_date.="'".$row[csf('setup_date')]."'".',';
					 }
  					$aprvl_necty_date=chop($aprvl_necty_date,",");*/

				  	if($db_type==0)
					{
						$approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'),'yyyy-mm-dd')."' and company_id='$company')) and page_id=15 and status_active=1 and is_deleted=0";
					}
					else
					{
						$approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'), "", "",1)."' and company_id='$company')) and page_id=15 and status_active=1 and is_deleted=0";
					}
					$approval_status=sql_select($approval_status);
					if($approval_status[0][csf('approval_need')]==1)
					{
						$approval_status="1";
					}
					else
					{
						$approval_status="0,1";
					}

					$result_wo_date_arr= sql_select("select a.id,a.wo_number as requ_and_wo_no,a.wo_date as requ_and_wo_date  from wo_non_order_info_mst a,wo_non_order_info_dtls b where b.item_category_id=1 and a.company_name=$company and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.ref_closing_status=0 $wo_date_cond and a.is_approved in ($approval_status) group by a.id,a.wo_number,a.wo_date");

					$wo_approval_by_necty="";
					 foreach($result_wo_date_arr as $row)
					 {
						$wo_approval_by_necty.= $row[csf('id')].",";
					 }
					 $wo_approval_by_necty=chop($wo_approval_by_necty,",");

					if($wo_approval_by_necty!="")
					{
						$result= sql_select("select a.id,a.wo_number as requ_and_wo_no,a.wo_date as requ_and_wo_date,a.source,a.pay_mode,SUM(b.supplier_order_quantity) as total_quantity,b.mst_id from wo_non_order_info_mst a,wo_non_order_info_dtls b where b.item_category_id=1 and a.company_name=$company and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.ref_closing_status=0 $wo_date_cond and a.is_approved in ($approval_status) and a.id in($wo_approval_by_necty) group by a.id,a.wo_number,a.wo_date,a.source,a.pay_mode,b.mst_id");
					}
					else
					{
						//$result= sql_select("select a.id,a.wo_number as requ_and_wo_no,a.wo_date as requ_and_wo_date,a.source,a.pay_mode,SUM(b.supplier_order_quantity) as total_quantity,b.mst_id from wo_non_order_info_mst a,wo_non_order_info_dtls b where b.item_category_id=1 and a.company_name=$company and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.ref_closing_status=0 $wo_date_cond group by a.id,a.wo_number,a.wo_date,a.source,a.pay_mode,b.mst_id");

					}

				}
				if($type==101) //Dyes And Chemical Purchase Order
				{
					$job_sql=sql_select("select a.id, b.po_quantity as po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company");
					$job_qnty_arr=array();
					foreach($job_sql as $row)
					{
						$job_qnty_arr[$row[csf("id")]]+=$row[csf("po_quantity")];
					}
					//echo $job_qnty_arr[2762].kayum;
					unset($job_sql);
					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.wo_date between ".$txt_date_from." and ".$txt_date_to."";
					$pi_qty_arr=return_library_array( "select b.work_order_id, sum(b.quantity) as qty from com_pi_master_details a,com_pi_item_details b where a.id=b.pi_id and a.importer_id=$company and a.item_category_id in(5,6,7,23) and a.pi_basis_id=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.work_order_id ", "work_order_id", "qty");
					$receive_qty_arr=return_library_array( "select a.pi_wo_batch_no,sum(a.order_qnty) as qty from inv_transaction a,inv_receive_master b
						where a.mst_id=b.id and a.company_id=$company and  a.item_category in(5,6,7,23) and a.transaction_type=1 and b.item_category in(5,6,7,23)
						and b.entry_form=4 and b.receive_basis=2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.pi_wo_batch_no ", "pi_wo_batch_no", "qty");
					//print_r($receive_qty_arr);

					$result= sql_select("select a.id,a.wo_number as requ_and_wo_no,a.wo_date as requ_and_wo_date,a.source,a.pay_mode,SUM(b.supplier_order_quantity) as total_quantity,b.mst_id from wo_non_order_info_mst a,wo_non_order_info_dtls b where a.item_category in(5,6,7,23) and a.company_name=$company and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.ref_closing_status=0 $wo_date_cond group by a.id,a.wo_number,a.wo_date,a.source,a.pay_mode,b.mst_id");
				}
				if($type==102) //Stationary Purchase Order
				{
					$job_sql=sql_select("select a.id, b.po_quantity as po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company");
					$job_qnty_arr=array();
					foreach($job_sql as $row)
					{
						$job_qnty_arr[$row[csf("id")]]+=$row[csf("po_quantity")];
					}
					//echo $job_qnty_arr[2762].kayum;
					unset($job_sql);
					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.wo_date between ".$txt_date_from." and ".$txt_date_to."";
					$pi_qty_arr=return_library_array( "select b.work_order_id, sum(b.amount) as qty from com_pi_master_details a,com_pi_item_details b where a.id=b.pi_id and a.importer_id=$company and a.item_category_id in(4,11) and a.pi_basis_id=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.work_order_id ", "work_order_id", "qty");
					$receive_qty_arr=return_library_array( "select a.pi_wo_batch_no,sum(a.order_amount) as qty from inv_transaction a,inv_receive_master b
					where a.mst_id=b.id and a.company_id=$company and  a.item_category in(4,11) and a.transaction_type=1 and b.item_category in(4,11)
					and b.entry_form=20 and b.receive_basis=2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.pi_wo_batch_no ", "pi_wo_batch_no", "qty");
					//print_r($receive_qty_arr);

					$result= sql_select("select a.id,a.wo_number as requ_and_wo_no,a.wo_date as requ_and_wo_date,a.source,a.pay_mode,SUM(b.amount) as total_quantity,b.mst_id from wo_non_order_info_mst a,wo_non_order_info_dtls b where a.item_category in(4,11) and a.company_name=$company and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.ref_closing_status=0 $wo_date_cond group by a.id,a.wo_number,a.wo_date,a.source,a.pay_mode,b.mst_id");
				}
				if($type==103) // Others Purchase Order
				{
					$job_sql=sql_select("select a.id, b.po_quantity as po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company");
					$job_qnty_arr=array();
					foreach($job_sql as $row)
					{
						$job_qnty_arr[$row[csf("id")]]+=$row[csf("po_quantity")];
					}
					//echo $job_qnty_arr[2762].kayum;
					unset($job_sql);
					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.wo_date between ".$txt_date_from." and ".$txt_date_to."";
					$pi_qty_arr=return_library_array( "select b.work_order_id, sum(b.amount) as qty from com_pi_master_details a,com_pi_item_details b where a.id=b.pi_id and a.importer_id=$company and a.item_category_id in(8,9,10,15,16,17,18,19,20,21,22,32) and a.pi_basis_id = 1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.work_order_id ", "work_order_id", "qty");
					$receive_qty_arr=return_library_array( "select a.pi_wo_batch_no,sum(a.order_amount) as qty from inv_transaction a,inv_receive_master b
					where a.mst_id=b.id and a.company_id=$company and  a.item_category in(8,9,10,15,16,17,18,19,20,21,22,32) and a.transaction_type=1 and b.item_category in(8,9,10,15,16,17,18,19,20,21,22,32) and b.entry_form=20 and b.receive_basis=2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.pi_wo_batch_no ", "pi_wo_batch_no", "qty");
					//print_r($receive_qty_arr);

					$result= sql_select("select a.id,a.wo_number as requ_and_wo_no,a.wo_date as requ_and_wo_date,a.source,a.pay_mode,SUM(b.amount) as total_quantity,b.mst_id from wo_non_order_info_mst a,wo_non_order_info_dtls b where a.item_category in(8,9,10,15,16,17,18,19,20,21,22,32) and a.company_name=$company and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.ref_closing_status=0 $wo_date_cond group by a.id,a.wo_number,a.wo_date,a.source,a.pay_mode,b.mst_id");
				}
				if($type==104) // Pro Forma Invoice *SYSTEM ID TREAT AS ID
				{
					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.pi_date between ".$txt_date_from." and ".$txt_date_to."";
					//$pi_qty_arr=return_library_array( "select b.work_order_id, sum(b.amount) as qty from com_pi_master_details a,com_pi_item_details b where a.id=b.pi_id and a.importer_id=$company and a.item_category_id in(8,9,10,15,16,17,18,19,20,21,22,32) and a.pi_basis_id = 1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.work_order_id ", "work_order_id", "qty");
					$lc_value_arr=return_library_array("select a.pi_id, sum(b.net_total_amount) as total_lc_value from com_btb_lc_pi a, com_pi_master_details b 
					where a.pi_id=b.id and b.importer_id=$company and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 
					group by a.pi_id", "pi_id", "total_lc_value");
					
					$accep_value_arr=return_library_array("select a.pi_id, sum(a.current_acceptance_value) as accpe_value from com_import_invoice_dtls a, com_pi_master_details b 
					where a.pi_id=b.id and b.importer_id=$company and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 
					group by a.pi_id", "pi_id", "accpe_value");

					$receive_qty_arr=return_library_array( "select a.pi_wo_batch_no, sum(a.order_amount) as qty from inv_transaction a, inv_receive_master b
					where a.mst_id=b.id and a.pi_wo_batch_no=b.booking_id and a.company_id=$company and a.item_category in(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,28,30,31,32,33,34,35,36,37,38) and b.item_category in(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,28,30,31,32,33,34,35,36,37,38) 
					and b.receive_basis=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.pi_wo_batch_no", "pi_wo_batch_no", "qty");
					//print_r($receive_qty_arr);

					//$result= sql_select("select a.id,a.wo_number as requ_and_wo_no,a.wo_date as requ_and_wo_date,a.source,a.pay_mode,SUM(b.amount) as total_quantity,b.mst_id from wo_non_order_info_mst a,wo_non_order_info_dtls b where a.item_category in(8,9,10,15,16,17,18,19,20,21,22,32) and a.company_name=$company and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.ref_closing_status=0 group by a.id,a.wo_number,a.wo_date,a.source,a.pay_mode,b.mst_id");
					$result= sql_select("select a.id, a.pi_date as requ_and_wo_date, a.source, a.item_category_id, a.pi_number as requ_and_wo_no, SUM(b.amount) as total_quantity 
					from com_pi_master_details a,com_pi_item_details b 
					where a.id=b.pi_id and a.item_category_id in(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,28,30,31,32,33,34,35,36,37,38) and a.importer_id=$company and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.ref_closing_status=0 $wo_date_cond 
					group by a.id,a.pi_date,a.source,a.item_category_id,a.pi_number");

				}
				if($type==105) // BTB/Margin LC
				{
					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.lc_date between ".$txt_date_from." and ".$txt_date_to."";
					$lib_supplier_arr=return_library_array( "select id,supplier_name from lib_supplier where is_deleted=0 and status_active=1", "id", "supplier_name");
					//$pi_qty_arr=return_library_array( "select b.work_order_id, sum(b.amount) as qty from com_pi_master_details a,com_pi_item_details b where a.id=b.pi_id and a.importer_id=$company and a.item_category_id in(8,9,10,15,16,17,18,19,20,21,22,32) and a.pi_basis_id = 1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.work_order_id ", "work_order_id", "qty");
					//$lc_value_arr=return_library_array("select a.pi_id,sum(b.lc_value) as total_lc_value from com_btb_lc_pi a,com_btb_lc_master_details b where a.com_btb_lc_master_details_id=b.id and b.importer_id=$company and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.pi_id", "pi_id", "total_lc_value");
					$rtn_sql="select a.booking_id, a.exchange_rate, c.id as trans_id, c.cons_amount as rcv_amount 
					from inv_receive_master a, inv_issue_master b, inv_transaction c
					where a.id=b.received_id and b.id=c.mst_id and a.company_id=$company and c.item_category in(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,28,30,31,32,33,110) and a.receive_basis=1 and c.transaction_type=3 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1";
					$rtn_result=sql_select($rtn_sql);
					foreach($rtn_result as $row)
					{
						if($trans_check[$row[csf("trans_id")]]=="")
						{
							$trans_check[$row[csf("trans_id")]]=$row[csf("trans_id")];
							$receive_rtn_arr[$row[csf("booking_id")]]+=$row[csf("rcv_amount")]/$row[csf("exchange_rate")];
						}
					}
					
					//print_r($receive_qty_arr);
					$receive_qty_arr=return_library_array( "select a.pi_wo_batch_no,sum(a.order_amount) as qty from inv_transaction a, inv_receive_master b
					where a.mst_id=b.id and a.pi_wo_batch_no=b.booking_id and a.company_id=$company and a.item_category in(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,28,30,31,32,33,110) and b.item_category in(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,28,30,31,32,33,110) and b.receive_basis=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.pi_wo_batch_no", "pi_wo_batch_no", "qty");
					$btb_sql = "select a.id, a.btb_system_id as requ_and_wo_no_btb, a.lc_date as requ_and_wo_date, a.supplier_id, a.item_category_id, SUM(a.lc_value) as total_quantity, a.lc_number as requ_and_wo_no, a.pi_id as multi_pi_id, a.lc_expiry_date, b.com_btb_lc_master_details_id 
					from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c 
					where a.id=b.com_btb_lc_master_details_id and b.pi_id = c.pi_id and c.item_category_id in(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,28,30,31,32,33,110) and a.importer_id=$company and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.ref_closing_status=0 $wo_date_cond 
					group by a.id, a.btb_system_id, a.lc_date, a.supplier_id, a.item_category_id, a.lc_number, a.pi_id, a.lc_expiry_date, b.com_btb_lc_master_details_id";
					//echo $btb_sql;
				 	$result= sql_select($btb_sql);
				}
				if($type==106) // Export LC Entry
				{
					/*if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.lc_date between ".$txt_date_from." and ".$txt_date_to."";
					$lib_buyer_arr=return_library_array( "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1", "id", "buyer_name");
					$invoice_value_arr=return_library_array( "select lc_sc_id,sum(invoice_value) as invoice_value from com_export_invoice_ship_mst where is_deleted=0 and status_active=1 and benificiary_id=$company  group by lc_sc_id","lc_sc_id", "invoice_value");
*/
				 //	$result= sql_select("select id,lc_date as requ_and_wo_date,export_lc_system_id,a.buyer_name,tolerance,SUM(lc_value) as total_quantity,export_lc_no as requ_and_wo_no ,expiry_date as lc_expiry_date from com_export_lc a where export_item_category in(1,2,3,4,10,11,20,21,22,23,24,30,31,35,36,37,40,45,46,47,48,49,50,51,55,60,65,66) and beneficiary_name=$company and is_deleted=0 and status_active=1 and ref_closing_status=$unclose_id $wo_date_cond group by id,lc_date,export_lc_system_id,buyer_name,tolerance,export_lc_no,expiry_date");
					//echo "select id,lc_date as requ_and_wo_date,export_lc_system_id,a.buyer_name,tolerance,SUM(lc_value) as total_quantity,export_lc_no as requ_and_wo_no ,expiry_date as lc_expiry_date from com_export_lc a where export_item_category in(1,2,3,4,10,11,20,21,22,23,24,30,31,35,36,37,40,45,46,47,48,49,50,51,55,60,65,66) and beneficiary_name=$company and is_deleted=0 and status_active=1 and ref_closing_status=$unclose_id $wo_date_cond group by id,lc_date,export_lc_system_id,buyer_name,tolerance,export_lc_no,expiry_date";
				}
				if($type==107) // Sales Contract Entry *PENDING
				{
					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.lc_date between ".$txt_date_from." and ".$txt_date_to."";
					if($db_type==0) $select_job=" group_concat(b.job_id) as job_id"; else  $select_job=" rtrim(xmlagg(xmlelement(e,b.job_id,',').extract('//text()') order by b.job_id).GetClobVal(),',') AS job_id ";
					echo '<br><b>Incompleted<b>';
					//$result= sql_select("select a.id,a.wo_number as requ_and_wo_no,a.wo_date as requ_and_wo_date,a.source,SUM(b.supplier_order_quantity) as total_quantity,b.mst_id,$select_job from wo_non_order_info_mst a,wo_non_order_info_dtls b where a.item_category in(4,11) and a.company_name=$company and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and a.ref_closing_status=0 group by a.id,a.wo_number,a.wo_date,a.source,b.mst_id");
				}
				if($type==117) // Sample Requisition
				{
					if($db_type==0)
					{
						$is_complete_prod=" and b.is_complete_prod=0";
					}
					else
					{
						$is_complete_prod=" and b.is_complete_prod is null";
					}
					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.requisition_date between ".$txt_date_from." and ".$txt_date_to."";
					$result=sql_select("select a.id as req_id,a.requisition_number  as requ_and_wo_no,b.id,a.requisition_date as requ_and_wo_date ,b.sample_name, b.gmts_item_id, b.smv, b.sample_color, b.sample_prod_qty as total_quantity, b.submission_qty, b.delv_start_date, b.delv_end_date, b.sample_charge, b.sample_curency from sample_development_mst a, sample_development_dtls b where  a.id=b.sample_mst_id  and a.entry_form_id=117 and b.entry_form_id=117 and a.company_id=$company and  a.is_deleted=0  and a.status_active=1 and b.status_active=1 and b.is_deleted=0  $is_complete_prod $wo_date_cond  group by a.id  ,b.id,a.requisition_date   ,b.sample_name, b.gmts_item_id, b.smv, b.sample_color, b.sample_prod_qty, b.submission_qty, b.delv_start_date, b.delv_end_date, b.sample_charge, b.sample_curency,a.requisition_number order by b.id asc");
				}
				if($type==163) // Order Entry
				{
					$lib_buyer_arr=return_library_array( "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1", "id", "buyer_name");
					$order_qnty=return_library_array( "SELECT a.id, a.po_quantity*b.total_set_qnty as qnty  from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.is_deleted=0 and a.status_active in(1,2,3) and b.is_deleted=0 and b.status_active=1  ", "id", "qnty");
					$ex_fac_qnty=return_library_array( "SELECT po_break_down_id,sum(ex_factory_qnty) as qnty from pro_ex_factory_mst where is_deleted=0 and status_active =1 group by po_break_down_id", "po_break_down_id", "qnty");

					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.shipment_date between ".$txt_date_from." and ".$txt_date_to."";
					$shipment_cond="";
					if($only_full=='true') $shipment_cond .=" and  a.shiping_status=3";
					else $shipment_cond .=" and  a.shiping_status NOT IN (3) "; 

					$result=sql_select("SELECT b.team_leader, a.id, a.job_no_mst, a.po_number as requ_and_wo_no, a.shiping_status, a.shipment_date as requ_and_wo_date, b.buyer_name, a.grouping from wo_po_details_master b, wo_po_break_down a where a.job_no_mst=b.job_no  and a.is_deleted=0 and a.status_active=1 $wo_date_cond and b.company_name=$company $shipment_cond group by b.team_leader, a.id, a.job_no_mst, a.po_number, a.shiping_status, a.shipment_date, b.buyer_name, a.grouping order by a.id asc");
				//	echo "SELECT b.team_leader, a.id, a.job_no_mst, a.po_number as requ_and_wo_no, a.shiping_status, a.shipment_date as requ_and_wo_date, b.buyer_name, a.grouping from wo_po_details_master b, wo_po_break_down a where a.job_no_mst=b.job_no  and a.is_deleted=0 and a.status_active=1 $wo_date_cond and b.company_name=$company $shipment_cond group by b.team_leader, a.id, a.job_no_mst, a.po_number, a.shiping_status, a.shipment_date, b.buyer_name, a.grouping order by a.id asc";
				}
				
				if(empty($result))
				{
					echo get_empty_data_msg();
					die;
				}

				$k=1;
				foreach($result as $row)
				{
					if($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					if($db_type==2 && $type==70) $row[csf('job_id')] = $row[csf('job_id')]->load();
					$all_job_id=array_unique(explode(",",$row[csf("job_id")]));
					$job_qnty=0;
					foreach($all_job_id as $job_id)
					{
						$job_qnty+=$job_qnty_arr[$job_id];
					}
					//for type=105
					if($type==105){
						$all_single_pi=array_unique(explode(",",$row[csf("multi_pi_id")]));
						//print_r( $all_single_pi);
						//echo "<br/>";
						//$single_pi_arrr=array();
						$pi_rec_qty=0;$pi_rcv_rtn_amt=0;
						foreach($all_single_pi as $show_single_pi)
						{
							$pi_rec_qty+=$receive_qty_arr[$show_single_pi];
							$pi_rcv_rtn_amt+=$receive_rtn_arr[$show_single_pi];
						}
						//print_r($single_pi_arrr);
						$requ_and_wo_no=$row[csf("requ_and_wo_no")];
					}
					elseif($type==106)
					{
						//$requ_and_wo_no=$row[csf("export_lc_system_id")];
					}
					elseif($type==4)
					{
						$requ_and_wo_no=$row[csf("recv_number")];
					}
					else
					{
						$requ_and_wo_no=$row[csf("requ_and_wo_no")];
					}

					?>
					<tr id=""  bgcolor="<? echo $bgcolor; ?>">
						<td style="word-break:break-all" width="25" align="center">
							<input type="checkbox" id="chk_id_<? echo $k;  ?>" onclick="js_set_value(<? echo $k;  ?>)" style="margin-left:3px;" value="<? echo $row[csf('id')].'**'.$requ_and_wo_no;  ?>"/>
						</td>
						<td style="word-break:break-all" width="30"><? echo  $i++; ?></td>
						<td style="word-break:break-all" width="<? if($type==163) echo "80"; else echo "110";?>" title="<? if($type==4) {echo "rcv_id: ";}else {echo "lc_id: ";} echo $row[csf("id")]; ?>">
                        <p>
                        <? 
                        if($type==163) { echo $lib_buyer_arr[$row[csf("buyer_name")]];}
						elseif($type == 4){ echo $row[csf("recv_number")];}
						else { echo $requ_and_wo_no;} 
						?> 
                        &nbsp;</p>
						</td>
						<td style="word-break:break-all"  width=" <? if($type==117 || $type==163){ echo '100';} else { echo '65';}?> " align="center"><p>
						<? 
						if($type==163) { echo $row[csf("job_no_mst")];}
						elseif($type==4){ echo $row[csf("receive_date")];}
						else{ echo  change_date_format($row[csf("requ_and_wo_date")]);} 
						?>
						&nbsp;</p></td>
                        <?php
                        if($type == 163)
                        {
                        	?>
                            <td style="word-break:break-all;" width="80"><?php echo $row[csf("grouping")]; ?></td>
                            <?php
                        }
                        ?>
                        <td style="word-break:break-all" width="<? if($type==117 ){echo "100" ;} else {echo "120";}?>"><p>
						<?
						if($type==105){ echo  $lib_supplier_arr[$row[csf("supplier_id")]]; }
						elseif($type==106) { echo $lib_buyer_arr[$row[csf("buyer_name")]];}
						elseif($type==163){ echo $row[csf("requ_and_wo_no")];}
						elseif($type==117){ echo $sample_name_library[$row[csf("sample_name")]];}
						else{ echo  $source[$row[csf("source")]]; }
						?>
                        &nbsp;</p></td>
						<td style="word-break:break-all" width="<? if($type==163){echo "100";}else{echo "110";}?>"><p>
						<?
						if($type==117){ echo $item_arrs[$row[csf("gmts_item_id")]];}
						elseif($type==163){ echo change_date_format($row[csf("requ_and_wo_date")]);}
						elseif($type==4){ echo  $lib_supplier_arr[$row[csf("supplier_id")]];}
						else 
						{
							$birth_date=change_date_format($row[csf('requ_and_wo_date')]);
							$age = my_old($birth_date);
							if($age[year]>0 )
							{
								printf("%d years, %d months, %d days\n", $age[year], $age[mnt], $age[day]);
							}
							else
							{
								printf("%d months, %d days\n", $age[mnt], $age[day]);
							}
						}
						?>
						&nbsp;</p></td>
                        <td style="word-break:break-all" width="<? if($type==163){echo "80";}else{echo "90";} ?>" align="center"><p>
						<?
						if($type==104 || $type==105){ echo $item_category[$row[csf("item_category_id")]];}
						elseif($type==106){ echo $row[csf("tolerance")]; }
						elseif($type==117){ echo  $color_library[$row[csf("sample_color")]];}
						elseif($type==163){ echo $po_qty= $order_qnty[$row[csf("id")]];}
						else { echo $pay_mode[$row[csf("pay_mode")]];}
						?>
						&nbsp;</p></td>
                        <?
						if($type!=163)
						{
							
							?>
							<td  width="<? if($type==117){echo "100" ;} else {echo "80";}?>" align="right">
							<?
							if($type == 4){ echo number_format($row[csf("rcv_qnty")],2);}
							else{ echo  number_format($row[csf("total_quantity")],2);}
							?>
							</td>
	                        <?
							if($type==104)
							{
								echo '<td width="80" align="right">';
								echo number_format($lc_value_arr[$row[csf("id")]],2);
								?>
								</td>
                                <td width="80" align="right"><? echo number_format($accep_value_arr[$row[csf("id")]],2);?></td>
								<?
							}
							?>
	                        <td width="<? if($type==117){echo "120" ;} else {echo "80";}?>" align="<? if($type==117){echo "center";} else {echo "right";} ?>">
							<?
							if($type==70){
								echo  number_format($job_qnty,2);
							}
							if($type==100 || $type==101 || $type==102 || $type==103)
							{
								if($row[csf("pay_mode")]==2)
								{
									echo number_format($pi_qty_arr[$row[csf("id")]],2);
								}
								else
								{
									echo number_format($receive_qty_arr[$row[csf("id")]],2);
								}
							}
							if(($type==104))
							{
								echo number_format($receive_qty_arr[$row[csf("id")]],2);
							}
							if(($type==117))
							{
								echo change_date_format($row[csf("delv_start_date")]);
							}
							if(($type==105))
							{
								//$resultt = array_intersect($single_pi_arrr, $receive_qty_arr);
								//print_r( $resultt);
							   //$singlePi[$row[csf(628)]][$row[csf("multi_pi_id")]]= $singlePi;
							   // $data[] = $row[csf(628)]][$row[110,122,123] ==
								/*if(in_array($receive_qty_arr[0],$single_pi_arrr))
								{*/
								//$arrLanth=count($single_pi_arrr);
								echo number_format($pi_rec_qty,2);
								//$RecValueTotal="";
								//$increment_index=0;
								//for($g=0;$g <= $arrLanth;$g++){
									//$RecValueTotal+= $single_pi_arrr=$receive_qty_arr[$single_pi_arrr[0]];
									//echo $single_pi_arRr=$receive_qty_arr[$single_pi_arrr];
									//echo $increment_index++;
									//echo $single_pi_arrr=$receive_qty_arr[$single_pi_arrr[2]];
									//echo $RecValueTotal;
									//}

								/*}
								print_r( $receive_qty_arr);*/
								//echo number_format($receive_qty_arr[$row[csf("multi_pi_id")]],2);
							  //print_r(  $receive_qty_arr);
							}
							if(($type==106))
							{
								echo number_format($invoice_value_arr[$row[csf("id")]],2);
								$invoce_valuee=$invoice_value_arr[$row[csf("id")]];
							}
							if($type == 4){
								echo $item_category[$row[csf("item_category")]];
							}
							?>
	                        </td>
							<? if($type==105 || $type==106)
							{
                                //------ if expire date is gone td color red
                                $now = time(); // or your date as well
                                $your_date = strtotime($row[csf("lc_expiry_date")]);
                                if($now>=$your_date)
                                {
                                    echo '<td width="65" align="center" style="background-color:red;">';
                                }else{echo '<td width="65" align="center" >';}
								echo change_date_format($row[csf("lc_expiry_date")]);
								?>
								</td>
								<?
                            }
                            if($type==4)
							{
                                echo '<td width="65 align="center">';
                                echo $lib_supplier_arr[$row[csf("loan_party")]];
								?>
								</td>
								<?
                             }
                             if($type==70 || $type==100 || $type==101 || $type==102 || $type==103 || $type==104 || $type==105)
                             {
                                  $th_width='width="100"';
                             }elseif($type == 4){
                                  $th_width = 'width="80"';
                             } 
							 if($type==105){
								 ?>
                                 <td width="100" align="right"><? echo number_format($pi_rcv_rtn_amt,2); ?></td>
                                 <td width="100" align="right"><? $ac_rcv=$pi_rec_qty-$pi_rcv_rtn_amt; echo number_format($ac_rcv,2); ?></td>
                                 <? 
							}
							?>
							<td align="<? if($type==117){echo "center";} else {echo "right";} ?>" <? echo $th_width; ?>>
							<?
							if($type==70)
							{
								$balance_req=$row[csf("total_quantity")]-$job_qnty;
								echo  number_format($balance_req,2);
							}
							elseif($type==117)
							{
								echo change_date_format($row[csf("delv_end_date")]);
							}
							elseif($type==100 || $type==101 || $type==102 || $type==103)
							{
								$balance_pi=$row[csf("total_quantity")]-$pi_qty_arr[$row[csf("id")]];
								if($pay_mode[$row[csf("pay_mode")]]==2)
								{
									echo  number_format($balance_pi,2);
								}
								else
								{
									$balance_recv=$row[csf("total_quantity")]-$receive_qty_arr[$row[csf("id")]];
									echo  number_format($balance_recv,2);
								}
							}
							if($type==104)
							{
								 $balance_valuepi= $row[csf("total_quantity")]-$receive_qty_arr[$row[csf("id")]];
								 echo  number_format( $balance_valuepi,2);
							}
							if($type==105)
							{
								 $balance_valuepi= $row[csf("total_quantity")]-$ac_rcv;
								 echo  number_format( $balance_valuepi,2);
							}
							if($type==106)
							{
								 $balance_invoice_value= $row[csf("total_quantity")]-$invoce_valuee;
								 echo  number_format( $balance_invoice_value,2);
							}
							if($type==4)
							{
								 echo $receive_basis_arr[$row[csf("receive_basis")]];
							}
							 //echo  number_format($balance,2); ?>
						 	</td>
	                        <? if($type==106){
							echo '<td width="83" align="right">';
							$percent_value=($balance_invoice_value/$row[csf("total_quantity")])*100;
							echo number_format($percent_value,2);
							?>
							</td>
							<?
							}
						}
						else
						{
							?>
							<td style="word-wrap: break-word;word-break: break-all;" align="center" width="60">	<? echo $ex_qty=$ex_fac_qnty[$row[csf("id")]]; ?></td>
							<td  style="word-wrap: break-word;word-break: break-all;" align="center" width="110">	<? echo  $bal=$po_qty-$ex_qty; ?></td>
							<td style="word-wrap: break-word;word-break: break-all;"  align="center" width="110">	<? echo  $shipment_status[$row[csf("shiping_status")]]; ?></td>
							<td><? echo  $team_leader_arr[$row[csf("team_leader")]]; ?></td>
							<?
						}
						?>

					</tr>
					<?
					$k++;
				}

				?>
            </tbody>
		</table>
		<?
		if($type==163)
		{
			?>
			<table id="tbl_list" align="center" class="rpt_table" rules="all" border="1" style="width:100%;" >
				<tr>
					<td width="25"></td>
					<td width="30"></td>
					<td width="<? if($type==163) echo "80";else echo "110";?>"></td>
					<td width="100"></td>
                    <td width="80"></td>
					<td width="120"></td>
					<td width="100">Grand Total </td>
					<td align="center" id="po" width="80"></td>
					<td  align="center" id='ship' width="60"></td>
					<td  align="center" id="bal" width="110"></td>
					<td width="110"></td>
					<td  ></td>
				</tr>

			</table>
			<?
		}
		?>
		</div>
		<script type="text/javascript">
			var type='<? echo $type;?>';
			if(type==163)
			{
				var tableFilters1 =
				{
					col_10: "select",
					display_all_text:'Show All',

					col_operation: {
						id: ["po","ship","bal"],
						col: [7,8,9],
						operation: ["sum","sum","sum"],
						write_method: ["innerHTML","innerHTML","innerHTML"]
					}
				}
				setFilterGrid("tbl_list",-1,tableFilters1);
			}
		</script>
		<?
}

if($action=="show_details_pi")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company = str_replace("'","",$cbo_company_name);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$only_full=trim(str_replace("'","",$only_full));
	$check_only_full=trim(str_replace("'","",$check_only_full));
	//$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");
	//$lib_supplier_arr=return_library_array( "select id,supplier_name from lib_supplier where is_deleted=0 and status_active=1", "id", "supplier_name");
 	?>
 	<script>
	 	//check all
		/*function check_all_data()
		{
		var tbl_row_count = document.getElementById( 'tbl_list').rows.length;

		tbl_row_count = tbl_row_count - 1;
		alert(tbl_row_count);
			for( var i = 1; i <= tbl_row_count; i++)  {


			}
		}*/

		//this function for only ID
		var selected_id = new Array;
		function js_set_value(str)
		{
			
			if( jQuery.inArray( $('#chk_id_' + str).val(), selected_id ) == -1 )
			{
				selected_id.push($('#chk_id_' + str).val());
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#chk_id_' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + '***';
			}
			id = id.substr( 0, id.length - 1 );
			$('#total_id').val( id );
		}



		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value(i);
			}

			if($('#tbl_list_head thead tr #all_chk').is(':checked'))
			{
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = true;
				});
			}
			else
			{
				//var selected_id = new Array;
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = false;
				});
				//$('#total_id').val('');
			}
		}
		var ref_type=$('#cbo_ref_type').val();
		var tableFilters1 ="";

			$(document).ready(function() {
				var ref_type=$('#cbo_ref_type').val();
				if(ref_type!=163)
	            setFilterGrid('tbl_list',-1,tableFilters1);
	            //setFilterGrid('tbl_list',-1);
				//reset_hide_field();
	        });
	</script>
    <?
	$lc_value_arr=return_library_array("select a.pi_id, sum(b.net_total_amount) as total_lc_value from com_btb_lc_pi a, com_pi_master_details b 
	where a.pi_id=b.id and b.importer_id=$company and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 
	group by a.pi_id", "pi_id", "total_lc_value");
	
	$accep_value_arr=return_library_array("select a.pi_id, sum(a.current_acceptance_value) as accpe_value from com_import_invoice_dtls a, com_pi_master_details b 
	where a.pi_id=b.id and b.importer_id=$company and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 
	group by a.pi_id", "pi_id", "accpe_value");

	$receive_qty_arr=return_library_array( "select b.booking_id, sum(a.order_amount) as qty 
	from inv_transaction a, inv_receive_master b
	where a.mst_id=b.id and a.transaction_type=1 and b.receive_basis=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.company_id=$company 
	and a.item_category in(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,28,30,31,32,33,110) 
	and b.item_category in(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,28,30,31,32,33,110) 
	group by b.booking_id", "booking_id", "qty");
	
	$wo_date_cond="";
	if( $date_from!="" && $date_to!="" ) $wo_date_cond= " and a.pi_date between ".$txt_date_from." and ".$txt_date_to."";
	$result= sql_select("select a.id, a.pi_date as requ_and_wo_date, a.source, a.item_category_id, a.pi_number as requ_and_wo_no, SUM(b.amount) as total_quantity 
	from com_pi_master_details a,com_pi_item_details b 
	where a.id=b.pi_id and a.item_category_id in(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,28,30,31,32,33,34,35,36,37,38) and a.importer_id=$company and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.ref_closing_status=$only_full $wo_date_cond 
	group by a.id,a.pi_date,a.source,a.item_category_id,a.pi_number");
	if($check_only_full=='true')
	{
		$closing_status=0;
	}
	else $closing_status=1;
	?>
    <div style="width:1100px">
    <input style="width:400px;" type="hidden" id="total_id" name="total_id"/>
    <table width="1100" id="tbl_list_head" class="rpt_table" rules="all" border="1" align="center">
        <thead>
            <tr>
                <th width="30"><input style="margin-left:4px;" type="checkbox" id="all_chk" onclick="check_all_data();"/>
                <input style="margin-left:4px; width:20px;" type="hidden" id="unclose_id" name="unclose_id" value="<? echo $closing_status;?>"/>
                </th>
                <th width="40">SL</th>
                <th width="120">PI No</th>
                <th width="70">PI Date</th>
                <th width="100">Source</th>
                <th width="110">Age</th>
                <th width="90">Pay Mode</th>
                <th width="100">PI Value</th>
                <th width="100">LC Value</th>
                <th width="100">Acceptance Value</th>
                <th width="100">Receive Value</th>
                <th>Balance Value</th>
            </tr>
        </thead>
    </table>
    <div style="overflow:scroll; height:350px; width:1100px;">
    <table id="tbl_list" align="center" class="rpt_table" rules="all" border="1" width="1080">
    	<tbody>
        	<?
			
			$i=1;$k=1;
			foreach($result as $row)
			{
				if($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor;?>" >
					<td style="word-break:break-all" width="30" align="center">
						<input type="checkbox" id="chk_id_<? echo $k;  ?>" onclick="js_set_value(<? echo $k;  ?>)" style="margin-left:3px;" value="<? echo $row[csf('id')].'**'.$row[csf("requ_and_wo_no")];  ?>"/>
					</td>
					<td style="word-break:break-all" width="40" align="center"><? echo  $i++; ?></td>
					<td style="word-break:break-all" width="120" title="<?=  "PI id :".$row[csf("id")];?>"><p><? echo $row[csf("requ_and_wo_no")]; ?>&nbsp;</p></td>
					<td style="word-break:break-all"  width="70" align="center"><p><? echo  change_date_format($row[csf("requ_and_wo_date")]);?>&nbsp;</p></td>
					<td style="word-break:break-all" width="100" align="center" title="<?= $row[csf("source")];?>"><p><? echo  $source[$row[csf("source")]];?>&nbsp;</p></td>
					<td style="word-break:break-all" width="110"><p><?
					$birth_date=change_date_format($row[csf('requ_and_wo_date')]);
					$age = my_old($birth_date);
					if($age[year]>0 )
					{
						printf("%d years, %d months, %d days\n", $age[year], $age[mnt], $age[day]);
					}
					else
					{
						printf("%d months, %d days\n", $age[mnt], $age[day]);
					}
					?>
					&nbsp;</p></td>
					<td style="word-break:break-all" width="90" align="center"><p><? echo $pay_mode[2];?>&nbsp;</p></td>
					<td width="100" align="right"><? echo  number_format($row[csf("total_quantity")],2); ?></td>
                    <td width="100" align="right"><? echo number_format($lc_value_arr[$row[csf("id")]],2);?></td>
                    <td width="100" align="right"><? echo number_format($accep_value_arr[$row[csf("id")]],2);?></td>
					<td width="100" align="right"><? echo number_format($receive_qty_arr[$row[csf("id")]],2);?></td>
                    <td align="right"><? $balance_valuepi= $row[csf("total_quantity")]-$receive_qty_arr[$row[csf("id")]]; echo number_format( $balance_valuepi,2); ?></td>
                </tr>
				<?
				$k++;
			}
            ?>
        </tbody>
    </table>
    </div>
    <?
}

if($action=="show_details_btb")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company = str_replace("'","",$cbo_company_name);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$only_full=trim(str_replace("'","",$only_full));
	$check_only_full=trim(str_replace("'","",$check_only_full));
	//$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");
	$lib_supplier_arr=return_library_array( "select id,supplier_name from lib_supplier where is_deleted=0 and status_active=1", "id", "supplier_name");
 	?>
 	<script>
	 	//check all
		/*function check_all_data()
		{
		var tbl_row_count = document.getElementById( 'tbl_list').rows.length;

		tbl_row_count = tbl_row_count - 1;
		alert(tbl_row_count);
			for( var i = 1; i <= tbl_row_count; i++)  {


			}
		}*/

		//this function for only ID
		var selected_id = new Array;
		function js_set_value(str)
		{
			
			if( jQuery.inArray( $('#chk_id_' + str).val(), selected_id ) == -1 )
			{
				selected_id.push($('#chk_id_' + str).val());
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#chk_id_' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + '***';
			}
			id = id.substr( 0, id.length - 1 );
			$('#total_id').val( id );
		}



		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value(i);
			}

			if($('#tbl_list_head thead tr #all_chk').is(':checked'))
			{
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = true;
				});
			}
			else
			{
				//var selected_id = new Array;
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = false;
				});
				//$('#total_id').val('');
			}
		}
		var ref_type=$('#cbo_ref_type').val();
		var tableFilters1 ="";

			$(document).ready(function() {
				var ref_type=$('#cbo_ref_type').val();
				if(ref_type!=163)
	            setFilterGrid('tbl_list',-1,tableFilters1);
	            //setFilterGrid('tbl_list',-1);
				//reset_hide_field();
	        });
	</script>
    <?
	$rtn_sql="select a.booking_id, a.exchange_rate, c.id as trans_id, c.cons_amount as rcv_amount 
	from inv_receive_master a, inv_issue_master b, inv_transaction c
	where a.id=b.received_id and b.id=c.mst_id and a.company_id=$company and c.item_category in(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,28,30,31,32,33,110) and a.receive_basis=1 and c.transaction_type=3 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1";
	$rtn_result=sql_select($rtn_sql);
	foreach($rtn_result as $row)
	{
		if($trans_check[$row[csf("trans_id")]]=="")
		{
			$trans_check[$row[csf("trans_id")]]=$row[csf("trans_id")];
			$receive_rtn_arr[$row[csf("booking_id")]]+=$row[csf("rcv_amount")]/$row[csf("exchange_rate")];
		}
	}

	$receive_qty_arr=return_library_array( "select b.booking_id, sum(a.order_amount) as qty 
	from inv_transaction a, inv_receive_master b
	where a.mst_id=b.id and a.transaction_type=1 and b.receive_basis=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.company_id=$company 
	and a.item_category in(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,28,30,31,32,33,110) 
	and b.item_category in(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,28,30,31,32,33,110) 
	group by b.booking_id", "booking_id", "qty");
	$wo_date_cond="";
	if( $date_from!="" && $date_to!="" ) $wo_date_cond= " and a.lc_date between ".$txt_date_from." and ".$txt_date_to."";
	$btb_sql = "select a.id, a.btb_system_id, a.lc_date, a.supplier_id, a.lc_value, a.lc_number, a.lc_expiry_date, c.item_category_id, c.id as pi_id
	from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c 
	where a.id=b.com_btb_lc_master_details_id and b.pi_id = c.pi_id and c.item_category_id in(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,28,30,31,32,33,110) and a.importer_id=$company and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.ref_closing_status=$only_full $wo_date_cond";
	//echo $btb_sql;
	$result= sql_select($btb_sql);
	$btb_data=array();
	foreach($result as $row)
	{
		$btb_data[$row[csf("id")]]["id"]=$row[csf("id")];
		$btb_data[$row[csf("id")]]["btb_system_id"]=$row[csf("btb_system_id")];
		$btb_data[$row[csf("id")]]["lc_date"]=$row[csf("lc_date")];
		$btb_data[$row[csf("id")]]["supplier_id"]=$row[csf("supplier_id")];
		$btb_data[$row[csf("id")]]["lc_value"]=$row[csf("lc_value")];
		$btb_data[$row[csf("id")]]["lc_number"]=$row[csf("lc_number")];
		$btb_data[$row[csf("id")]]["lc_expiry_date"]=$row[csf("lc_expiry_date")];
		if($pi_check[$row[csf("id")]][$row[csf("pi_id")]]=="")
		{
			$pi_check[$row[csf("id")]][$row[csf("pi_id")]]=$row[csf("pi_id")];
			$btb_data[$row[csf("id")]]["pi_id"].=$row[csf("pi_id")].",";
		}
		if($btb_cat_check[$row[csf("id")]][$row[csf("item_category_id")]]=="")
		{
			$btb_cat_check[$row[csf("id")]][$row[csf("item_category_id")]]=$row[csf("item_category_id")];
			$btb_data[$row[csf("id")]]["item_category_id"].=$row[csf("item_category_id")].",";
		}
	}
	
		if($check_only_full=='true')
		{
			$closing_status=0;
		}
		else $closing_status=1;
	?>
    <div style="width:1150px">
    <input style="width:400px;" type="hidden" id="total_id" name="total_id"/>
    <table width="1150" id="tbl_list_head" class="rpt_table" rules="all" border="1" align="center">
        <thead>
            <tr>
                <th width="30"><input style="margin-left:4px;" type="checkbox" id="all_chk" onclick="check_all_data();"/>
                <input style="margin-left:4px; width:20px;" type="hidden" id="unclose_id" name="unclose_id" value="<? echo $closing_status;?>"/>
                </th>
                <th width="40">SL</th>
                <th width="120">BTB LC No</th>
                <th width="70">BTB LC Date</th>
                <th width="70">LC Exp. Date</th>
                <th width="110">Supplier</th>
                <th width="110">Age</th>
                <th width="110">Item Category</th>
                <th width="90">LC Value </th>
                <th width="90">Receive Value</th>
                <th width="90">Receive Rtn Value</th>
                <th width="90">Actual Rcv Value</th>
                <th>Balance Value</th>
            </tr>
        </thead>
    </table>
    <div style="overflow:scroll; height:350px; width:1150px;">
    <table id="tbl_list" align="center" class="rpt_table" rules="all" border="1" width="1130">
    	<tbody>
        	<?
			$i=1;$k=1;
			foreach($btb_data as $btb_id=>$val)
			{
				$item_cat_arr=explode(",",chop($val["item_category_id"],","));
				$all_cat=$rcv_val=$rcv_rtn_val=$rcv_balance=$balance_val="";
				foreach($item_cat_arr as $cat_id)
				{
					$all_cat.=$item_category[$cat_id].",";
				}
				$pi_id_arr=explode(",",chop($val["pi_id"],","));
				foreach($pi_id_arr as $pi_id)
				{
					$rcv_val+=$receive_qty_arr[$pi_id];
					$rcv_rtn_val+=$receive_rtn_arr[$pi_id];
				}
				$rcv_balance=$rcv_val-$rcv_rtn_val;
				$balance_val=$val["lc_value"]-$rcv_balance;
				
				if($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor;?>" >
					<td style="word-break:break-all" width="30" align="center">
						<input type="checkbox" id="chk_id_<? echo $k;  ?>" onclick="js_set_value(<? echo $k;  ?>)" style="margin-left:3px;" value="<? echo $btb_id.'**'.$val["lc_number"];  ?>"/>
					</td>
					<td style="word-break:break-all" width="40" align="center"><? echo  $i++; ?></td>
					<td style="word-break:break-all" width="120" title="<?=  "PI id :".$btb_id;?>"><p><? echo $val["lc_number"]; ?>&nbsp;</p></td>
					<td style="word-break:break-all"  width="70" align="center"><p><? echo  change_date_format($val["lc_date"]);?>&nbsp;</p></td>
                    <td style="word-break:break-all"  width="70" align="center"><p><? echo  change_date_format($val["lc_expiry_date"]);?>&nbsp;</p></td>
					<td style="word-break:break-all" width="110" align="center" title="<?= $row[csf("source")];?>"><p><? echo  $lib_supplier_arr[$val["supplier_id"]];?>&nbsp;</p></td>
					<td style="word-break:break-all" width="110"><p><?
					$birth_date=change_date_format($val["lc_date"]);
					$age = my_old($birth_date);
					if($age[year]>0 )
					{
						printf("%d years, %d months, %d days\n", $age[year], $age[mnt], $age[day]);
					}
					else
					{
						printf("%d months, %d days\n", $age[mnt], $age[day]);
					}
					?>
					&nbsp;</p></td>
					<td style="word-break:break-all" width="110" align="center"><p><? echo chop($all_cat,",");?>&nbsp;</p></td>
					<td width="90" align="right"><? echo number_format($val["lc_value"],2); ?></td>
                    <td width="90" align="right" title="<?= chop($val["pi_id"],",");?>"><? echo number_format($rcv_val,2);?></td>
                    <td width="90" align="right"><? echo number_format($rcv_rtn_val,2);?></td>
					<td width="90" align="right"><? echo number_format($rcv_balance,2);?></td>
                    <td align="right"><? echo number_format($balance_val,2); ?></td>
                </tr>
				<?
				$k++;
			}
            ?>
        </tbody>
    </table>
    </div>
    <?
}
if($action=="show_details_export_lc_closing")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company = str_replace("'","",$cbo_company_name);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$only_full=trim(str_replace("'","",$only_full));
	$check_only_full=trim(str_replace("'","",$check_only_full));
	//$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");
	$lib_supplier_arr=return_library_array( "select id,supplier_name from lib_supplier where is_deleted=0 and status_active=1", "id", "supplier_name");
 	?>
 	<script>
	 	//check all
		/*function check_all_data()
		{
		var tbl_row_count = document.getElementById( 'tbl_list').rows.length;

		tbl_row_count = tbl_row_count - 1;
		alert(tbl_row_count);
			for( var i = 1; i <= tbl_row_count; i++)  {


			}
		}*/

		//this function for only ID
		var selected_id = new Array;
		function js_set_value(str)
		{
			
			if( jQuery.inArray( $('#chk_id_' + str).val(), selected_id ) == -1 )
			{
				selected_id.push($('#chk_id_' + str).val());
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#chk_id_' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + '***';
			}
			id = id.substr( 0, id.length - 1 );
			$('#total_id').val( id );
		}



		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value(i);
			}

			if($('#tbl_list_head thead tr #all_chk').is(':checked'))
			{
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = true;
				});
			}
			else
			{
				//var selected_id = new Array;
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = false;
				});
				//$('#total_id').val('');
			}
		}
		var ref_type=$('#cbo_ref_type').val();
		var tableFilters1 ="";

			$(document).ready(function() {
				var ref_type=$('#cbo_ref_type').val();
				if(ref_type!=163)
	            setFilterGrid('tbl_list',-1,tableFilters1);
	            //setFilterGrid('tbl_list',-1);
				//reset_hide_field();
	        });
	</script>
    <?
					if($check_only_full=='true')
					{
						$closing_status=0;
					}
					else $closing_status=1;
		
					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.lc_date between ".$txt_date_from." and ".$txt_date_to."";
					$lib_buyer_arr=return_library_array( "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1", "id", "buyer_name");
					$invoice_value_arr=return_library_array( "select lc_sc_id,sum(invoice_value) as invoice_value from com_export_invoice_ship_mst where is_deleted=0 and status_active=1 and benificiary_id=$company  group by lc_sc_id","lc_sc_id", "invoice_value");

				 	$result= sql_select("select a.id,lc_date as requ_and_wo_date,a.export_lc_system_id,a.buyer_name,a.tolerance,SUM(lc_value) as lc_value,a.export_lc_no as requ_and_wo_no ,a.expiry_date as lc_expiry_date from com_export_lc a where a.export_item_category in(1,2,3,4,10,11,20,21,22,23,24,30,31,35,36,37,40,45,46,47,48,49,50,51,55,60,65,66) and a.beneficiary_name=$company and a.is_deleted=0 and a.status_active=1 and a.ref_closing_status=$only_full $wo_date_cond group by a.id,a.lc_date,a.export_lc_system_id,a.buyer_name,a.tolerance,a.export_lc_no,a.expiry_date");
					
		
	?>
    <div style="width:1050px">
    <input style="width:400px;" type="hidden" id="total_id" name="total_id"/>
    <table width="1050" id="tbl_list_head" class="rpt_table" rules="all" border="1" align="center">
        <thead>
            <tr>
                <th width="30"><input style="margin-left:4px;" type="checkbox" id="all_chk" onclick="check_all_data();"/>
                <input style="margin-left:4px; width:20px;" type="hidden" id="unclose_id" name="unclose_id" value="<? echo $closing_status;?>"/>
                </th>
                <th width="40">SL</th>
                <th width="120">Export LC No</th>
                <th width="70">Export LC Date</th>
                <th width="70">Buyer</th>
                <th width="110">Age</th>
                <th width="110">Tolerance % </th>
                <th width="110">LC Value</th>
                <th width="90">Invoice Value</th>
                <th width="90">LC Exp. Date</th>
                <th width="90">Balance Value </th>
                <th width="">Value %</th>
               
            </tr>
        </thead>
    </table>
    <div style="overflow:scroll; height:350px; width:1050px;">
    <table id="tbl_list" align="center" class="rpt_table" rules="all" border="1" width="1030">
    	<tbody>
        	<?
			$i=1;
				$k=1;
				foreach($result as $row)
				{
					if($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$all_job_id=array_unique(explode(",",$row[csf("job_id")]));
					$job_qnty=0;
					foreach($all_job_id as $job_id)
					{
						$job_qnty+=$job_qnty_arr[$job_id];
					}
				
					
				
				if($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor;?>" >
					<td style="word-break:break-all" width="30" align="center">
						<input type="checkbox" id="chk_id_<? echo $k;  ?>" onclick="js_set_value(<? echo $k;  ?>)" style="margin-left:3px;" value="<? echo $row[csf("id")].'**'.$row[csf("export_lc_system_id")];  ?>"/>
					</td>
					<td style="word-break:break-all" width="40" align="center"><? echo  $i++; ?></td>
					<td style="word-break:break-all" width="120" title="<? ?>"><p><? echo $row[csf("export_lc_system_id")]; ?>&nbsp;</p></td>
					<td style="word-break:break-all"  width="70" align="center"><p><? echo  change_date_format($row[csf("requ_and_wo_date")]);?>&nbsp;</p></td>
                    <td style="word-break:break-all"  width="70" align="center"><p><?  echo $lib_buyer_arr[$row[csf("buyer_name")]];?>&nbsp;</p></td>
					<td style="word-break:break-all" width="110" align="center"><p><? 
					
					$birth_date=change_date_format($row[csf('requ_and_wo_date')]);
				$age = my_old($birth_date);
				if($age[year]>0 )
				{
					printf("%d years, %d months, %d days\n", $age[year], $age[mnt], $age[day]);
				}
				else
				{
					printf("%d months, %d days\n", $age[mnt], $age[day]);
				};?>&nbsp;</p></td>
					<td style="word-break:break-all" width="110"><p><?
					
					echo $row[csf("tolerance")];
					?>
					&nbsp;</p></td>
					<td style="word-break:break-all" width="110" align="center"><p><? echo number_format($row[csf("lc_value")],2);
$invoce_valuee=$invoice_value_arr[$row[csf("id")]];;?>&nbsp;</p></td>
					<td width="90" align="right"><? echo  number_format($invoice_value_arr[$row[csf("id")]],2); ?></td>
                    <td width="90" align="right"><? echo change_date_format($row[csf("lc_expiry_date")]);?></td> 
                    <td width="90" align="right"><? echo number_format($row[csf("lc_value")]-$invoice_value_arr[$row[csf("id")]],2);?></td>
					
                    <td align="right"><? $balance_invoice_value=$row[csf("lc_value")]-$invoice_value_arr[$row[csf("id")]];
						$percent_value=($balance_invoice_value/$row[csf("lc_value")])*100;
							echo number_format($percent_value,2);  ?></td>
                </tr>
				<?
				$k++;
			}
            ?>
        </tbody>
    </table>
    </div>
    <?
}
if($action=="show_details_knit_qc_sweater")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company = str_replace("'","",$cbo_company_name);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$only_full=trim(str_replace("'","",$only_full));
	$check_only_full=trim(str_replace("'","",$check_only_full));
	//$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");
	//$lib_supplier_arr=return_library_array( "select id,supplier_name from lib_supplier where is_deleted=0 and status_active=1", "id", "supplier_name");
 	?>
 	<script>
	 	//check all
		/*function check_all_data()
		{
		var tbl_row_count = document.getElementById( 'tbl_list').rows.length;

		tbl_row_count = tbl_row_count - 1;
		alert(tbl_row_count);
			for( var i = 1; i <= tbl_row_count; i++)  {


			}
		}*/

		//this function for only ID
		var selected_id = new Array;
		function js_set_value(str)
		{
			
			if( jQuery.inArray( $('#chk_id_' + str).val(), selected_id ) == -1 )
			{
				selected_id.push($('#chk_id_' + str).val());
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#chk_id_' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + '***';
			}
			id = id.substr( 0, id.length - 1 );
			$('#total_id').val( id );
		}



		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value(i);
			}

			if($('#tbl_list_head thead tr #all_chk').is(':checked'))
			{
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = true;
				});
			}
			else
			{
				//var selected_id = new Array;
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = false;
				});
				//$('#total_id').val('');
			}
		}
		var ref_type=$('#cbo_ref_type').val();
		var tableFilters1 ="";

			$(document).ready(function() {
				var ref_type=$('#cbo_ref_type').val();
				if(ref_type!=370)
	            setFilterGrid('tbl_list',-1,tableFilters1);
	            //setFilterGrid('tbl_list',-1);
				//reset_hide_field();
	        });
	</script>
    <?

					//$wo_date_cond="";
					//if( $date_from!="" && $date_to!="" ) $wo_date_cond= " and a.lc_date between ".$txt_date_from." and ".$txt_date_to."";
					$lib_buyer_arr=return_library_array( "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1", "id", "buyer_name");
					$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");
					
					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.pub_shipment_date between ".$txt_date_from." and ".$txt_date_to."";
					$shipment_cond="";
					if($only_full==1) $shipment_cond .=" and  a.shiping_status=3";
					else $shipment_cond .=" and  a.shiping_status NOT IN (3) "; 
					$sql_qc=sql_select("select a.production_quantity,a.po_break_down_id from pro_garments_production_mst a where a.garments_nature=100 and a.production_type=52 and a.status_active=1");
					foreach($sql_qc as $row)
					{
						$po_qc_qty_arr[$row[csf("po_break_down_id")]]+=$row[csf("production_quantity")];
					}

				
				$result=sql_select("select b.team_leader, a.id as po_id,b.style_ref_no,b.order_uom,a.job_no_mst,a.po_number,a.po_quantity,a.shiping_status,a.pub_shipment_date ,b.buyer_name  from wo_po_details_master b , wo_po_break_down a where a.job_no_mst=b.job_no  and a.is_deleted=0 and a.status_active=1 and b.garments_nature=100 $wo_date_cond and b.company_name=$company $shipment_cond  order by a.id asc");
				
				
				//echo "select b.team_leader, a.id as po_id,b.style_ref_no,b.order_uom,a.job_no_mst,a.po_number,a.po_quantity,a.shiping_status,a.pub_shipment_date ,b.buyer_name  from wo_po_details_master b , wo_po_break_down a where a.job_no_mst=b.job_no  and a.is_deleted=0 and a.status_active=1 and b.garments_nature=100 $wo_date_cond and b.company_name=$company $shipment_cond  order by a.id asc";
				//$result=sql_select("select b.team_leader, a.id as po_id,b.style_ref_no,b.order_uom,a.job_no_mst,a.po_number,a.po_quantity,a.shiping_status,a.pub_shipment_date ,b.buyer_name  from wo_po_details_master b , wo_po_break_down a,pro_garments_production_mst c where a.job_no_mst=b.job_no  and c.po_break_down_id=a.id and a.is_deleted=0 and a.status_active=1 and b.garments_nature=100 and  c.garments_nature=100 and c.production_type=52 $wo_date_cond and b.company_name=$company and c.ref_closing_status=$only_full   order by a.id asc");
				//echo "select b.team_leader, a.id as po_id,b.style_ref_no,b.order_uom,a.job_no_mst,a.po_number,a.po_quantity,a.shiping_status,a.pub_shipment_date ,b.buyer_name  from wo_po_details_master b , wo_po_break_down a,pro_garments_production_mst c where a.job_no_mst=b.job_no  and c.po_break_down_id=a.id and a.is_deleted=0 and a.status_active=1 and b.garments_nature=100 and  c.garments_nature=100 and c.production_type=52 $wo_date_cond and b.company_name=$company and c.ref_closing_status=$only_full order by a.id asc";
		if($check_only_full=='true')
		{
			$closing_status=0;
		}
		else $closing_status=1;
	?>
    <div style="width:1100px">
    <input style="width:400px;" type="hidden" id="total_id" name="total_id"/>
    <table width="1100" id="tbl_list_head" class="rpt_table" rules="all" border="1" align="center">
        <thead>
            <tr>
                <th width="30"><input style="margin-left:4px;" type="checkbox" id="all_chk" onclick="check_all_data();"/>
                &nbsp;
                <input style="margin-left:4px; width:20px;" type="hidden" id="unclose_id" name="unclose_id" value="<? echo $closing_status;?>"/>
                
                </th>
                <th width="40">SL</th>
                <th width="120">Buyer</th>
                <th width="100">Job Number</th>
                <th width="100">Style</th>
                <th width="100">Po No</th>
                <th width="100">Image</th>
                <th width="70">Pub. Ship. Date</th>
                <th width="90">Po Qty </th>
                <th width="90">Knitting Qty</th>
                <th width="90">UOM</th>
                <th width="90">Knit. Bal. Qty</th>
                <th>Team Leader</th>
            </tr>
        </thead>
    </table>
    <div style="overflow:scroll; height:350px; width:1120px;">
    <table id="tbl_list" align="center" class="rpt_table" rules="all" border="1" width="1100">
    	<tbody>
        	<?
			$imge_arr=return_library_array( "select master_tble_id,image_location from  common_photo_library where file_type=1 and form_name='knit_order_entry' ",'master_tble_id','image_location');
			$i=1;$k=1;
			foreach($result as $val)
			{
				if($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				?>
				<tr bgcolor="<? echo $bgcolor;?>" >
					<td style="word-break:break-all" width="30" align="center">
						<input type="checkbox" id="chk_id_<? echo $k;  ?>" onclick="js_set_value(<? echo $k;  ?>)" style="margin-left:3px;" value="<? echo $val[csf('po_id')].'**'.$val[csf("po_number")]; //change_date_format ?>"/>
					</td>
					<td style="word-break:break-all" width="40" align="center"><? echo  $i++; ?></td>
					<td style="word-break:break-all" width="120" title=""><p><? echo $lib_buyer_arr[$val[csf("buyer_name")]]; ?>&nbsp;</p></td>
					<td style="word-break:break-all"  width="100" align="center"><p><? echo  $val[csf("job_no_mst")];?>&nbsp;</p></td>
                    <td style="word-break:break-all"  width="100" align="center"><p><? echo  $val[csf("style_ref_no")];?>&nbsp;</p></td>
					<td style="word-break:break-all" width="100" align="center" title="<?= $val[csf("po_number")];?>"><p><? echo  $val[csf("po_number")];?>&nbsp;</p></td>
					<td style="word-break:break-all" width="100"><p><?
					 if($imge_arr[$val[csf("job_no_mst")]]!="")
					{
					?>
					<img  src='../../<? echo $imge_arr[$val[csf("job_no_mst")]]; ?>' height='50' width='98' />
					<?
					}
					else "&nbsp;";
					?>
					&nbsp;</p></td>
					<td style="word-break:break-all" width="70" align="center"><p><? echo change_date_format($val[csf("pub_shipment_date")]);?>&nbsp;</p></td>
					<td width="90" align="right"><? echo number_format($val[csf("po_quantity")],2); ?></td>
                    <td width="90" align="right" title=""><? echo number_format($po_qc_qty_arr[$val[csf("po_id")]],2);?></td>
                    <td width="90" align="center"><? echo $unit_of_measurement[$val[csf("order_uom")]];?></td>
					<td width="90" align="right"><? echo number_format($val[csf("po_quantity")]-$po_qc_qty_arr[$val[csf("po_id")]],2);?></td>
                    <td><? echo  $team_leader_arr[$val[csf("team_leader")]]; ?></td>
                </tr>
				<?
				$k++;
			}
            ?>
        </tbody>
    </table>
    </div>
    <?
}
if($action=="show_details_knit_closing")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company = str_replace("'","",$cbo_company_name);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$only_full=trim(str_replace("'","",$only_full));
	$check_only_full=trim(str_replace("'","",$check_only_full));
	//$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");
	//$lib_supplier_arr=return_library_array( "select id,supplier_name from lib_supplier where is_deleted=0 and status_active=1", "id", "supplier_name");
 	?>
 	<script>
	 	//check all
		/*function check_all_data()
		{
		var tbl_row_count = document.getElementById( 'tbl_list').rows.length;

		tbl_row_count = tbl_row_count - 1;
		alert(tbl_row_count);
			for( var i = 1; i <= tbl_row_count; i++)  {


			}
		}*/

		//this function for only ID
		var selected_id = new Array;
		function js_set_value(str)
		{
			
			if( jQuery.inArray( $('#chk_id_' + str).val(), selected_id ) == -1 )
			{
				selected_id.push($('#chk_id_' + str).val());
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#chk_id_' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + '***';
			}
			id = id.substr( 0, id.length - 1 );
			$('#total_id').val( id );
		}



		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value(i);
			}

			if($('#tbl_list_head thead tr #all_chk').is(':checked'))
			{
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = true;
				});
			}
			else
			{
				//var selected_id = new Array;
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = false;
				});
				//$('#total_id').val('');
			}
		}
		var ref_type=$('#cbo_ref_type').val();
		var tableFilters1 ="";

			$(document).ready(function() {
				var ref_type=$('#cbo_ref_type').val();
				if(ref_type!=370)
	            setFilterGrid('tbl_list',-1,tableFilters1);
	            //setFilterGrid('tbl_list',-1);
				//reset_hide_field();
	        });
	</script>
    <?

					$wo_date_cond="";
					if( $date_from!="" && $date_to!="" ) $wo_date_cond= " and a.receive_date between ".$txt_date_from." and ".$txt_date_to."";
					$lib_buyer_arr=return_library_array( "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1", "id", "buyer_name");
					//$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");
					
					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.receive_date between ".$txt_date_from." and ".$txt_date_to."";
				$result_prog=sql_select("select a.id as mst_id,a.recv_number,a.receive_date,c.program_date,sum(b.grey_receive_qnty) as grey_receive_qnty,sum(b.reject_fabric_receive) as reject_fabric_receive,b.order_id,sum(c.program_qnty) as program_qnty,c.status,c.id as prog_no,a.buyer_id  from inv_receive_master a , pro_grey_prod_entry_dtls b,ppl_planning_info_entry_dtls c where a.id=b.mst_id and c.id=a.booking_id   and a.is_deleted=0 and a.status_active=1 and a.entry_form=2 and a.receive_basis=2 $wo_date_cond and a.company_id=$company and a.ref_closing_status=$only_full group by  a.id,a.recv_number,c.id,a.buyer_id,a.receive_date,c.program_date,b.order_id,c.status,c.id   order by c.id asc");
				
				$prog_no="";
				foreach($result_prog as $row)
				{
				$prog_dataArr[$row[csf('prog_no')]]['grey_receive_qnty']+=$row[csf('grey_receive_qnty')];
				$prog_dataArr[$row[csf('prog_no')]]['reject_fabric_receive']+=$row[csf('reject_fabric_receive')];
				$prog_dataArr[$row[csf('prog_no')]]['program_date']=$row[csf('program_date')];
				$prog_dataArr[$row[csf('prog_no')]]['order_id']=$row[csf('order_id')];
				$prog_dataArr[$row[csf('prog_no')]]['program_qnty']+=$row[csf('program_qnty')];
				$prog_dataArr[$row[csf('prog_no')]]['status']=$row[csf('status')];
				$prog_dataArr[$row[csf('prog_no')]]['buyer_id']=$row[csf('buyer_id')];
				$prog_dataArr[$row[csf('prog_no')]]['recv_number']=$row[csf('recv_number')];
				$prog_dataArr[$row[csf('prog_no')]]['mst_id'].=$row[csf('mst_id')].',';
				if($prog_no=="") $prog_no=$row[csf('prog_no')];else $prog_no.=",".$row[csf('prog_no')];
				}
				
				$poIds=chop($prog_no,','); 
				$po_ids=count(array_unique(explode(",",$prog_no)));
				if($db_type==2 && $po_ids>1000)
				{
				$po_cond_for_in=" and (";
				$poIdsArr=array_chunk(explode(",",$poIds),999);
				foreach($poIdsArr as $ids)
				{
				$ids=implode(",",$ids);
				$po_cond_for_in.=" b.id in($ids) or"; 
				}
				$po_cond_for_in=chop($po_cond_for_in,'or ');
				$po_cond_for_in.=")";
				}
				else
				{
					$poIds=implode(",",(array_unique(explode(",",$poIds))));
				$po_cond_for_in=" and b.id in($poIds)";
				}
				if($db_type==0) $year_field="YEAR(d.insert_date) as year"; 
				else if($db_type==2) $year_field="to_char(d.insert_date,'YYYY') as year";
	
				$po_result = sql_select("select c.po_number,c.id as po_id,b.id as prog_no,d.style_ref_no,d.job_no,c.file_no,c.grouping as ref_no,$year_field from  ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b, wo_po_break_down c,wo_po_details_master d where  b.id=a.dtls_id and c.id=a.po_id and d.job_no=c.job_no_mst and b.status_active =1 and c.status_active =1 and a.status_active =1 $po_cond_for_in");
				//echo "select c.po_number,c.id as po_id,b.id as prog_no,d.style_ref_no,d.job_no,c.file_no,c.grouping as ref_no,$year_field from  ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b, wo_po_break_down c,wo_po_details_master d where  b.id=a.dtls_id and c.id=a.po_id and d.job_no=c.job_no_mst and b.status_active =1 and c.status_active =1 and a.status_active =1 $po_cond_for_in";
				
				foreach($po_result as $row)
				{
					$po_arr[$row[csf('prog_no')]]['po_number'].=$row[csf('po_number')].',';
					$po_arr[$row[csf('prog_no')]]['job_no'].=$row[csf('job_no')].',';
					$po_arr[$row[csf('prog_no')]]['style_ref_no'].=$row[csf('style_ref_no')].',';
					$po_arr[$row[csf('prog_no')]]['file_no'].=$row[csf('file_no')].',';
					$po_arr[$row[csf('prog_no')]]['ref_no'].=$row[csf('ref_no')].',';
					$po_arr[$row[csf('prog_no')]]['year'].=$row[csf('year')].',';
				}
			$req_sql = "select a.booking_no,c.knit_id,c.prod_id,c.requisition_no, c.yarn_qnty
			from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b,ppl_yarn_requisition_entry c
			where a.id=b.mst_id and b.id=c.knit_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_cond_for_in";

			$req_result = sql_select($req_sql);

			$req_no="";
			foreach($req_result as $row)
			{
				if($req_no=="") $req_no=$row[csf('requisition_no')];else $req_no.=",".$row[csf('requisition_no')];
			}
				$ReqIds=chop($req_no,','); 
				$req_ids=count(array_unique(explode(",",$ReqIds)));
				if($db_type==2 && $req_ids>1000)
				{
				$req_cond_for_in=" and (";
				//$req_cond_for_in2=" and (";
				$ReqIdsArr=array_chunk(explode(",",$ReqIds),999);
				foreach($ReqIdsArr as $ids)
				{
				$ids=implode(",",$ids);
				$req_cond_for_in.=" b.requisition_no in($ids) or"; 
				//$req_cond_for_in2.=" a.booking_id in($ids) or"; 
				}
				$req_cond_for_in=chop($req_cond_for_in,'or ');
				$req_cond_for_in.=")";
				//$req_cond_for_in2=chop($req_cond_for_in2,'or ');
				//$req_cond_for_in2.=")";
				}
				else
				{
					$ReqIds=implode(",",(array_unique(explode(",",$ReqIds))));
				$req_cond_for_in=" and b.requisition_no in($ReqIds)";
				//$req_cond_for_in2=" and a.booking_id in($poIds)";
				}
				
			$yarn_issue="select a.id as issue_id,c.knit_id as prog_no,b.requisition_no,b.cons_quantity,b.cons_reject_qnty from inv_issue_master a,inv_transaction b,ppl_yarn_requisition_entry c where a.id=b.mst_id and b.requisition_no=c.requisition_no  and a.entry_form=3 and b.transaction_type=2 $req_cond_for_in ";
				
			$yarn_result = sql_select($yarn_issue);
			$issue_id="";
			foreach($yarn_result as $row)
			{
					if($issue_id=="") $issue_id=$row[csf('issue_id')];else $issue_id.=",".$row[csf('issue_id')];
					$yarn_qty_arr[$row[csf('prog_no')]]['yarn_issue']+=$row[csf('cons_quantity')];
					$yarn_qty_arr[$row[csf('prog_no')]]['yarn_issue_rej']+=$row[csf('cons_reject_qnty')];
			}
			$IssueIds=chop($issue_id,','); 
				$issue_ids=count(array_unique(explode(",",$IssueIds)));
				if($db_type==2 && $issue_ids>1000)
				{
				$issue_cond_for_in=" and (";
				//$req_cond_for_in2=" and (";
				$IssueIdsArr=array_chunk(explode(",",$IssueIds),999);
				foreach($IssueIdsArr as $ids)
				{
				$ids=implode(",",$ids);
				$issue_cond_for_in.=" b.requisition_no in($ids) or"; 
				//$req_cond_for_in2.=" a.booking_id in($ids) or"; 
				}
				$issue_cond_for_in=chop($issue_cond_for_in,'or ');
				$issue_cond_for_in.=")";
				//$req_cond_for_in2=chop($req_cond_for_in2,'or ');
				//$req_cond_for_in2.=")";
				}
				else
				{
					$IssueIds=implode(",",(array_unique(explode(",",$IssueIds))));
				$issue_cond_for_in=" and a.issue_id in($IssueIds)";
				//$req_cond_for_in2=" and a.booking_id in($poIds)";
				}
				
					
			$yarn_issue_ret="select a.issue_id,c.knit_id as prog_no,b.requisition_no,b.cons_quantity,b.cons_reject_qnty from inv_receive_master a,inv_transaction b,ppl_yarn_requisition_entry c where a.id=b.mst_id and a.booking_id=c.requisition_no  and a.entry_form=9 and b.transaction_type=4 and b.item_category=1 $issue_cond_for_in ";
			$yarn_ret_result = sql_select($yarn_issue_ret);
			foreach($yarn_ret_result as $row)
			{
				//if($req_no=="") $req_no=$row[csf('requisition_no')];else $req_no.=",".$row[csf('requisition_no')];
					$yarn_qty_arr[$row[csf('prog_no')]]['yarn_issue_ret']+=$row[csf('cons_quantity')];
					$yarn_qty_arr[$row[csf('prog_no')]]['yarn_issue_rej_ret']+=$row[csf('cons_reject_qnty')];
			}
			
			
		if($check_only_full=='true')
		{
			$closing_status=0;
		}
		else $closing_status=1;
	?>
    <div style="width:1540px">
    <input style="width:400px;" type="hidden" id="total_id" name="total_id"/>
    <table width="1540" id="tbl_list_head" class="rpt_table" rules="all" border="1" align="center">
        <thead>
            <tr>
                <th width="30"><input style="margin-left:4px;" type="checkbox" id="all_chk" onclick="check_all_data();"/>
                &nbsp;
                <input style="margin-left:4px; width:20px;" type="hidden" id="unclose_id" name="unclose_id" value="<? echo $closing_status;?>"/>
                
                </th>
                <th width="20">SL</th>
                <th width="100">Program No</th>
                <th width="100">Program Date</th>
                <th width="100">Buyer</th>
                <th width="100">Job Year</th>
                <th width="100">Job No</th>
                <th width="100">Style Ref</th>
                <th width="90">Order No</th>
                <th width="90">File No</th>
                <th width="90">Int. Ref.</th>
                <th width="90">Prog. Qty.</th>
                <th width="90">Yarn Issue Qnty</th>
                <th width="90">Issue Return Qnty</th>
                <th width="90">Knitting Qnty</th>
                <th width="90">Reject Fabric Qnty</th>
                <th width="90">Knitting Balance</th>
                <th width="">Knitting Status</th>
               
            </tr>
        </thead>
    </table>
    <div style="overflow:scroll; height:350px; width:1540px;">
    <table id="tbl_list" align="center" class="rpt_table" rules="all" border="1" width="1520">
    	<tbody>
        	<?
			
			$i=1;$k=1;
			foreach($prog_dataArr as $progNo=>$val)
			{
				if($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
					$po_number=rtrim($po_arr[$progNo]['po_number'],',');
					$po_number=implode(',',array_unique(explode(',',$po_number)));
					$style_ref_no=rtrim($po_arr[$progNo]['style_ref_no'],',');
					$style_ref_no=implode(',',array_unique(explode(',',$style_ref_no)));
					$file_no=rtrim($po_arr[$progNo]['file_no'],',');
					$file_no=implode(',',array_unique(explode(',',$file_no)));
					$ref_no=rtrim($po_arr[$progNo]['ref_no'],',');
					$ref_no=implode(',',array_unique(explode(',',$ref_no)));
					$job_year=rtrim($po_arr[$progNo]['year'],',');
					$job_year=implode(',',array_unique(explode(',',$job_year)));
					$job_no=rtrim($po_arr[$progNo]['job_no'],',');
					$job_no=implode(',',array_unique(explode(',',$job_no)));
					
					$mst_ids=rtrim($val[("mst_id")],',');
					$mst_ids=implode(',',array_unique(explode(',',$mst_ids)));
					
					//$yarn_qty_ret=$yarn_qty_arr[$progNo]['yarn_issue_ret'];
					//echo $yarn_qty_ret.'dd';
					
				?>
				<tr bgcolor="<? echo $bgcolor;?>" >
					<td style="word-break:break-all" width="30" align="center">
						<input type="checkbox" id="chk_id_<? echo $k;  ?>" onclick="js_set_value(<? echo $k;  ?>)" style="margin-left:3px;" value="<? echo $progNo.'**'.$mst_ids; //change_date_format ?>"/>
					</td>
					<td style="word-break:break-all" width="20" align="center"><? echo  $i++; ?></td>
					<td style="word-break:break-all" width="100" title="<? echo $val[("recv_number")];?>"><p><? echo $progNo;?>&nbsp;</p></td>
					<td style="word-break:break-all"  width="100" align="center"><p><? echo  change_date_format($val[("program_date")]);?>&nbsp;</p></td>
                    <td style="word-break:break-all"  width="100" align="center"><p><? echo  $lib_buyer_arr[$val[("buyer_id")]]; ;?>&nbsp;</p></td>
					<td style="word-break:break-all" width="100" align="center" title=""><p><? echo  $job_year;?>&nbsp;</p></td>
					<td style="word-break:break-all" width="100"><div style="word-break:break-all"><? echo $job_no;?>	</div></td>
					<td style="word-break:break-all" width="100" align="center"><div style="word-break:break-all"><? echo $style_ref_no;?></div></td>
					<td width="90" align="center"><div style="word-break:break-all"><? echo $po_number; ?></div></td>
                    <td width="90" align="center" title=""><? echo $file_no;?></td>
                    <td width="90" align="center"><? echo $ref_no;?></td>
					<td width="90" align="right"><? echo number_format($val[("program_qnty")],2);?></td>
                    <td width="90" align="right"><? echo number_format($yarn_qty_arr[$progNo]['yarn_issue'],2);?></td>
                    <td width="90" align="right"><? echo number_format($yarn_qty_arr[$progNo]['yarn_issue_ret'],2);?></td>
                    <td width="90" align="right" title="Knitting Qty"><? echo number_format($val[("grey_receive_qnty")],2);?></td>
                    <td width="90" align="right"  title="Knitting Reject Qty"><? echo number_format($val[("reject_fabric_receive")],2);?></td>
                    <td width="90" align="right"><? echo number_format($val[("program_qnty")]-($val[("grey_receive_qnty")]+$val[("reject_fabric_receive")]),2);?></td>
                    <td><? echo  $knitting_program_status[$val[("status")]]; ?></td>
                </tr>
				<?
				$k++;
			}
            ?>
        </tbody>
    </table>
    </div>
    <?
}
if($action=="show_details_order_closing")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company = str_replace("'","",$cbo_company_name);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$only_full=trim(str_replace("'","",$only_full));
	$check_only_full=trim(str_replace("'","",$check_only_full));
	//$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");
	//$lib_supplier_arr=return_library_array( "select id,supplier_name from lib_supplier where is_deleted=0 and status_active=1", "id", "supplier_name");
 	?>
 	<script>
	 	//check all
		/*function check_all_data()
		{
		var tbl_row_count = document.getElementById( 'tbl_list').rows.length;

		tbl_row_count = tbl_row_count - 1;
		alert(tbl_row_count);
			for( var i = 1; i <= tbl_row_count; i++)  {


			}
		}*/

		//this function for only ID
		var selected_id = new Array;
		function js_set_value(str)
		{
			
			if( jQuery.inArray( $('#chk_id_' + str).val(), selected_id ) == -1 )
			{
				selected_id.push($('#chk_id_' + str).val());
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#chk_id_' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + '***';
			}
			id = id.substr( 0, id.length - 1 );
			$('#total_id').val( id );
		}



		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value(i);
			}

			if($('#tbl_list_head thead tr #all_chk').is(':checked'))
			{
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = true;
				});
			}
			else
			{
				//var selected_id = new Array;
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = false;
				});
				//$('#total_id').val('');
			}
		}
		var ref_type=$('#cbo_ref_type').val();
		var tableFilters1 ="";

			$(document).ready(function() {
				var ref_type=$('#cbo_ref_type').val();
				if(ref_type!=370)
	            setFilterGrid('tbl_list',-1,tableFilters1);
	            //setFilterGrid('tbl_list',-1);
				//reset_hide_field();
	        });
	</script>
    <?

					$wo_date_cond="";
					if( $date_from!="" && $date_to!="" ) $wo_date_cond= " and a.receive_date between ".$txt_date_from." and ".$txt_date_to."";
					$lib_buyer_arr=return_library_array( "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1", "id", "buyer_name");
					//$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");
					
					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.receive_date between ".$txt_date_from." and ".$txt_date_to."";
				$lib_buyer_arr=return_library_array( "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1", "id", "buyer_name");
					$order_qnty=return_library_array( "SELECT a.id, a.po_quantity*b.total_set_qnty as qnty  from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.is_deleted=0 and a.status_active in(1,2,3) and b.is_deleted=0 and b.status_active=1  ", "id", "qnty");
					$ex_fac_qnty=return_library_array( "SELECT po_break_down_id,sum(ex_factory_qnty) as qnty from pro_ex_factory_mst where is_deleted=0 and status_active =1 group by po_break_down_id", "po_break_down_id", "qnty");

					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.shipment_date between ".$txt_date_from." and ".$txt_date_to."";
					$shipment_cond="";
					if($only_full=='true') $shipment_cond .=" and  a.shiping_status=3";
					else $shipment_cond .=" and  a.shiping_status NOT IN (3) "; 

					$result=sql_select("SELECT b.team_leader, a.id,a.po_quantity*b.total_set_qnty as qnty a.job_no_mst, a.po_number as po_number, a.shiping_status, a.shipment_date as requ_and_wo_date, b.buyer_name, a.grouping from wo_po_details_master b, wo_po_break_down a where a.job_no_mst=b.job_no  and a.is_deleted=0 and a.status_active=1 $wo_date_cond and b.company_name=$company $shipment_cond group by b.team_leader, a.id, a.job_no_mst, a.po_number, a.shiping_status, a.shipment_date, b.buyer_name, a.grouping order by a.id asc");
					//echo "SELECT b.team_leader, a.id, a.job_no_mst, a.po_number as requ_and_wo_no, a.shiping_status, a.shipment_date as requ_and_wo_date, b.buyer_name, a.grouping from wo_po_details_master b, wo_po_break_down a where a.job_no_mst=b.job_no  and a.is_deleted=0 and a.status_active=1 $wo_date_cond and b.company_name=$company $shipment_cond group by b.team_leader, a.id, a.job_no_mst, a.po_number, a.shiping_status, a.shipment_date, b.buyer_name, a.grouping order by a.id asc";
				$po_id="";
				foreach($result_prog as $row)
				{
				$po_dataArr[$row[csf('id')]]['po_qnty']+=$row[csf('qnty')];
				$po_dataArr[$row[csf('id')]]['buyer_id']=$row[csf('buyer_name')];
				$po_dataArr[$row[csf('id')]]['order_id']=$row[csf('id')];
				$po_dataArr[$row[csf('id')]]['job_no_mst']=$row[csf('job_no_mst')];
				$po_dataArr[$row[csf('id')]]['ref_no']=$row[csf('grouping')];
				$po_dataArr[$row[csf('id')]]['ship_date']=$row[csf('requ_and_wo_date')];
				$po_dataArr[$row[csf('id')]]['po_number']=$row[csf('po_number')];
				$po_dataArr[$row[csf('id')]]['shiping_status']=$row[csf('shiping_status')];
				$po_dataArr[$row[csf('id')]]['team_leader']=$row[csf('team_leader')];
				if($po_id=="") $po_id=$row[csf('id')];else $po_id.=",".$row[csf('id')];
				}
				
				$poIds=chop($prog_no,','); 
				$po_ids=count(array_unique(explode(",",$prog_no)));
				if($db_type==2 && $po_ids>1000)
				{
				$po_cond_for_in=" and (";
				$poIdsArr=array_chunk(explode(",",$poIds),999);
				foreach($poIdsArr as $ids)
				{
				$ids=implode(",",$ids);
				$po_cond_for_in.=" b.id in($ids) or"; 
				}
				$po_cond_for_in=chop($po_cond_for_in,'or ');
				$po_cond_for_in.=")";
				}
				else
				{
				$po_cond_for_in=" and b.id in($poIds)";
				}
				if($db_type==0) $year_field="YEAR(d.insert_date) as year"; 
				else if($db_type==2) $year_field="to_char(d.insert_date,'YYYY') as year";
	/*
				$po_result = sql_select("select c.po_number,c.id as po_id,b.id as prog_no,d.style_ref_no,d.job_no,c.file_no,c.grouping as ref_no,$year_field from  ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b, wo_po_break_down c,wo_po_details_master d where  b.id=a.dtls_id and c.id=a.po_id and d.job_no=c.job_no_mst and b.status_active =1 and c.status_active =1 and a.status_active =1 $po_cond_for_in");
				
				foreach($po_result as $row)
				{
					$po_arr[$row[csf('prog_no')]]['po_number'].=$row[csf('po_number')].',';
					$po_arr[$row[csf('prog_no')]]['job_no'].=$row[csf('job_no')].',';
					$po_arr[$row[csf('prog_no')]]['style_ref_no'].=$row[csf('style_ref_no')].',';
					$po_arr[$row[csf('prog_no')]]['file_no'].=$row[csf('file_no')].',';
					$po_arr[$row[csf('prog_no')]]['ref_no'].=$row[csf('ref_no')].',';
					$po_arr[$row[csf('prog_no')]]['year'].=$row[csf('year')].',';
				}
			*/

			

		if($check_only_full=='true')
		{
			$closing_status=0;
		}
		else $closing_status=1;
	?>
    <div style="width:1540px">
    <input style="width:400px;" type="hidden" id="total_id" name="total_id"/>
    <table width="1540" id="tbl_list_head" class="rpt_table" rules="all" border="1" align="center">
        <thead>
            <tr>
                <th width="30"><input style="margin-left:4px;" type="checkbox" id="all_chk" onclick="check_all_data();"/>
                &nbsp;
                <input style="margin-left:4px; width:20px;" type="hidden" id="unclose_id" name="unclose_id" value="<? echo $closing_status;?>"/>
                
                </th>
                <th width="20">SL</th>
                <th width="100">Buyer</th>
                <th width="100"> Job No </th>
                <th width="100">Int. Ref no</th>
                <th width="100">PO No </th>
                <th width="100"> Shipment Date </th>
                <th width="100"> Po Qty </th>
                <th width="90">Ship Qty</th>
                <th width="90">Ship Bal. Qty</th>
                <th width="90">Shipping Status</th>
                <th width="">Team Leader</th>
               
               
            </tr>
        </thead>
    </table>
    <div style="overflow:scroll; height:350px; width:1540px;">
    <table id="tbl_list" align="center" class="rpt_table" rules="all" border="1" width="1520">
    	<tbody>
        	<?
			
			$i=1;$k=1;
			foreach($po_dataArr as $poId=>$val)
			{
				if($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
					$po_number=rtrim($po_arr[$progNo]['po_number'],',');
					$po_number=implode(',',array_unique(explode(',',$po_number)));
					$style_ref_no=rtrim($po_arr[$progNo]['style_ref_no'],',');
					$style_ref_no=implode(',',array_unique(explode(',',$style_ref_no)));
					$file_no=rtrim($po_arr[$progNo]['file_no'],',');
					$file_no=implode(',',array_unique(explode(',',$file_no)));
					$ref_no=rtrim($po_arr[$progNo]['ref_no'],',');
					$ref_no=implode(',',array_unique(explode(',',$ref_no)));
					$job_year=rtrim($po_arr[$progNo]['year'],',');
					$job_year=implode(',',array_unique(explode(',',$job_year)));
					$job_no=rtrim($po_arr[$progNo]['job_no'],',');
					$job_no=implode(',',array_unique(explode(',',$job_no)));
					
					
					
				?>
				<tr bgcolor="<? echo $bgcolor;?>" >
					<td style="word-break:break-all" width="30" align="center">
						<input type="checkbox" id="chk_id_<? echo $k;  ?>" onclick="js_set_value(<? echo $k;  ?>)" style="margin-left:3px;" value="<? echo $poId.'**'.$val[("po_number")]; //change_date_format ?>"/>
					</td>
					<td style="word-break:break-all" width="20" align="center"><? echo  $i++; ?></td>
					<td style="word-break:break-all" width="100" title="<? echo $val[("recv_number")];?>"><p><? echo $lib_buyer_arr[$val[("buyer_id")]];?>&nbsp;</p></td>
					<td style="word-break:break-all"  width="100" align="center"><p><? echo  $val[("job_no_mst")];?>&nbsp;</p></td>
                    <td style="word-break:break-all"  width="100" align="center"><p><? echo  $val[("ref_no")]; ;?>&nbsp;</p></td>
					<td style="word-break:break-all" width="100" align="center" title=""><p><? echo  $val[("po_number")];?>&nbsp;</p></td>
					<td style="word-break:break-all" width="100"><p><? echo change_date_format($val[("program_date")]);?>
					&nbsp;</p></td>
					<td style="word-break:break-all" width="100" align="center"><p><? echo change_date_format($val[("program_date")]);//$style_ref_no;?>&nbsp;</p></td>
					<td width="90" align="right"><? echo $po_number; ?></td>
                    <td width="90" align="right" title=""><? echo $file_no;?></td>
                    <td width="90" align="center"><? echo $ref_no;?></td>
				
                   
                    <td><? echo  $knitting_program_status[$val[("status")]]; ?></td>
                </tr>
				<?
				$k++;
			}
            ?>
        </tbody>
    </table>
    </div>
    <?
}


?>
