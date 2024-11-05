<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:../../../login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );     	 
	exit();
}

//--------------------------------------------------------------------------------------------------------------------
if($action=="batchnumbershow")
{
	echo load_html_head_contents("batch Info", "../../../", 1, 1,'','','',1);
	extract($_REQUEST);
	//echo $type;
	?>
	<script type="text/javascript">
        function js_set_value(id)
        { 
            document.getElementById('selected_id').value=id;
            parent.emailwindow.hide();
        }
    </script>
	<input type="hidden" id="selected_id" name="selected_id" /> 
	<?
	if ($company_name==0) $com_con=""; else $com_con="and a.company_id='$company_name'";

	 $sql="select a.batch_no,c.file_no,c.grouping,c.job_no_mst,a.company_id from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c where a.id=b.mst_id $com_con and b.po_id=c.id and  a.is_deleted=0 and b.is_deleted=0 group by a.batch_no,c.file_no,c.grouping,c.job_no_mst,a.company_id";	

	 echo create_list_view("list_view", "Job No,Batch No.", "100,100","420","350",0, $sql, "js_set_value", "job_no_mst,batch_no", "", 1, "0,0,0,0,0", $arr , "job_no_mst,batch_no", "",'setFilterGrid("list_view",-1);','0') ;
	exit();
}

if($action=="fileNref")
{
	echo load_html_head_contents("File Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $cbo_buyer_name;
	?>
	<script type="text/javascript">
        function js_set_value(id)
        { 
            document.getElementById('selected_id').value=id;
            parent.emailwindow.hide();
        }
		
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		
    </script>
	<input type="hidden" id="selected_id" name="selected_id" /> 
	<?
	//and a.buyer_name='$cbo_buyer_name'
	if($type==1) $po_cond="file_no";
	if($type==2) $po_cond="grouping";
	if($db_type==0) $year_field="SUBSTRING_INDEX(a.insert_date, '-', 1) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";

	if ($cbo_buyer_name==0) $buyer_con=""; else $buyer_con="and a.buyer_name='$cbo_buyer_name'";
	if ($txt_file_no==0) $file_con=""; else $file_con="and b.file_no='$txt_file_no'";
	
	if ($txt_inter_ref==0) $inter_ref_con=""; else $inter_ref_con="and b.grouping='$txt_inter_ref'";
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	
	if($cbo_job_year!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_job_year"; else $year_cond="";	
	//if ($company_name==0) $com_con=""; else $com_con="and a.company_name='$company_name'";
	   $sql="select a.job_no,a.job_no_prefix_num as job_prefix,b.file_no,b.grouping,a.style_ref_no,a.buyer_name,a.company_name,$year_field from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst  $buyer_con $file_con $inter_ref_con $year_cond and a.company_name='$company_name' and a.is_deleted=0 and b.is_deleted=0 order by a.job_no Asc";	

	$arr=array(5=>$buyer_arr);

	echo  create_list_view("list_view", "Job No,Year,File No.,Internal Ref. No,Style Ref. No,Buyer Name", "100,80,80,100,100","650","350",0, $sql, "js_set_value", "job_prefix,$po_cond,style_ref_no", "", 1, "0,0,0,0,0,buyer_name", $arr , "job_prefix,year,file_no,grouping,style_ref_no,buyer_name", "",'setFilterGrid("list_view",-1);','0') ;
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_batch_no=str_replace("'","",$txt_batch_no);
	$txt_inter_ref=str_replace("'","",$txt_inter_ref);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_styleref_no=str_replace("'","",$txt_styleref_no);
	$cbo_job_year=str_replace("'","",$cbo_job_year);
	$type=str_replace("'","",$type);
	//$txt_season="%".trim(str_replace("'","",$txt_season))."%";
	//if($txt_batch_no!="")
	$company_arr=return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_arr=return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$color_library=return_library_array("select id, color_name from lib_color", "id", "color_name");
  	
  	if($db_type==0)
	{
		$year_cond=" and YEAR(e.insert_date)=$cbo_job_year";
	}
	else if($db_type==2)
	{
		$year_cond=" and to_char(e.insert_date,'YYYY')=$cbo_job_year";
	}
	else
	{
		$year_cond="";
	}

	//FAL-17-00138
	if($txt_file_no!="") $file_cond=" and e.file_no='$txt_file_no'";else $file_cond="";
	if($txt_file_no!="") $file_cond2=" and c.file_no='$txt_file_no'";else $file_cond2="";
	if($txt_batch_no!="") $batch_cond=" and a.batch_no='$txt_batch_no'";else $batch_cond="";
	if($txt_inter_ref!="") $ref_cond=" and e.grouping='$txt_inter_ref'";else $ref_cond="";
	if($txt_inter_ref!="") $inter_ref_con=" and c.grouping='$txt_inter_ref'";else $inter_ref_con="";
	
	if($db_type==2)
	{
		$group_con="LISTAGG(cast(b.po_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.po_id) as po_id";
	}
	else
	{
		$group_con="group_concat(distinct(b.po_id)) as po_id";
	}
	if($cbo_buyer_name>0) $buyer_cond=" and d.buyer_name=$cbo_buyer_name"; else  $buyer_cond="";
	
	if ($type==1) // Show Start
	{
		$sql_res="SELECT a.batch_no,a.id,d.company_name,d.buyer_name,d.style_ref_no,e.file_no,e.grouping,a.color_id,$group_con,sum(b.batch_qnty) as batch_qnty,e.job_no_mst 
		from pro_batch_create_mst a,pro_batch_create_dtls b , wo_po_details_master d, wo_po_break_down e 
		where b.po_id=e.id and d.job_no=e.job_no_mst and a.id=b.mst_id and a.entry_form=0  and a.status_active=1 and b.status_active=1  and d.company_name=$cbo_company_name and a.batch_against != 2 $buyer_cond  $batch_cond $file_cond $ref_cond $year_cond
		group by a.batch_no,a.id,d.buyer_name,a.color_id,d.company_name,e.file_no,e.grouping,e.job_no_mst,d.style_ref_no";
		
		
		$header=sql_select($sql_res);
		$batchreport = array(); $batchIdArray = array(); $poIdArray = array();
		$all_po_id="";
		foreach($header as $row)
		{
			if ($sub_group_arr[$row[csf('color_id')]]=='')
			{
				$i=0;
				$sub_group_arr[$row[csf('color_id')]]=$row[csf('color_id')];
			}
			$batchreport[$row[csf('color_id')]][$row[csf('id')]]['color_id']=$row[csf('color_id')];
			$batchreport[$row[csf('color_id')]][$row[csf('id')]]['po_id']=$row[csf('po_id')];
			$batchreport[$row[csf('color_id')]][$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			$batchreport[$row[csf('color_id')]][$row[csf('id')]]['job_no_mst']=$row[csf('job_no_mst')];
			$batchreport[$row[csf('color_id')]][$row[csf('id')]]['batch_qnty']=$row[csf('batch_qnty')];
			$batchreport[$row[csf('color_id')]][$row[csf('id')]]['production_date']=$row[csf('production_date')];
			$batchreport[$row[csf('color_id')]][$row[csf('id')]]['company_name']=$row[csf('company_name')];
			$batchreport[$row[csf('color_id')]][$row[csf('id')]]['id']=$row[csf('id')];
			$batchreport[$row[csf('color_id')]][$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
			$batchreport[$row[csf('color_id')]][$row[csf('id')]]['grouping']=$row[csf('grouping')];
			$batchreport[$row[csf('color_id')]][$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$batchreport[$row[csf('color_id')]][$row[csf('id')]]['file_no']=$row[csf('file_no')];
			$batchIdArray[$row[csf('id')]] = $row[csf('id')];
			$poIdArray[$row[csf('po_id')]] = $row[csf('po_id')];
			
			$ref_no="'".$row[csf('grouping')]."'";
			$RefNoArray[$ref_no]=$ref_no;
			
				if($all_po_id=="") $all_po_id=$row[csf('po_id')]; else $all_po_id.=",".$row[csf('po_id')]; //echo $all_po_id;
			$i++;
		}
		//print_r($poIdArray);

		if(count($batchreport)==0)
		{
			?>
			<div style="font-weight: bold;color: red;font-size: 20px;text-align: center;">Dana not found! Please try again.</div>
			<?
			die();
		}
		//===============================================================
		if($txt_inter_ref!="")
		{
			$RefNos_cond="";
		}
		else  
		{ 
			$RefNos= implode(",", $RefNoArray);
		 	if( $RefNos!="")  $RefNos_cond="and c.grouping in($RefNos)";else $RefNos_cond="";
		}
		
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2) and ENTRY_FORM=50");
		oci_commit($con);
		
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 50, 1, $poIdArray, $empty_arr);//Po ID
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 50, 2, $batchIdArray, $empty_arr);//Batch ID
		disconnect($con);
		
		//================================= booking =======================
		$sql_book=sql_select("SELECT b.po_break_down_id as po_id,b.fabric_color_id ,b.fin_fab_qnty as fin_fab_qnty from wo_booking_mst a, wo_booking_dtls b, gbl_temp_engine d where a.booking_no=b.booking_no and a.booking_type=1 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=50 and d.ref_from=1");
		$booking_fin_qty=array();
		foreach($sql_book as $row)
		{
			$booking_grey_qty[$row[csf("po_id")]][$row[csf("fabric_color_id")]]['qty']+=$row[csf("fin_fab_qnty")];
		}
		unset($sql_book);
		// =================================== fin =========================
		$sql_fin_qty=sql_select("select a.batch_id, a.color_id, sum(a.receive_qnty) as qty, max(b.receive_date) as receive_date, a.remarks from pro_finish_fabric_rcv_dtls a, inv_receive_master b, gbl_temp_engine d where b.id=a.mst_id and b.entry_form in(68,7) and b.item_category=2 and a.status_active=1 and a.status_active=1  and a.batch_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=50 and d.ref_from=2 group by a.batch_id,a.color_id,a.remarks");
		
		$finishing_qty=array();$finishing_date=array();$finishing_remarks=array();
		foreach($sql_fin_qty as $row)
		{
			$finishing_qty[$row[csf("batch_id")]][$row[csf("color_id")]]['fin_qty']+=$row[csf("qty")];
			//$finishing_date[$row[csf("batch_id")]][$row[csf("color_id")]]['fin_date']=$row[csf("receive_date")];
			$finishing_remarks[$row[csf("batch_id")]][$row[csf("color_id")]]['remarks']=$row[csf("remarks")];
		}
		unset($sql_fin_qty);
		// =================================== issue =======================================
		$data_array_issue=sql_select("SELECT b.pi_wo_batch_no as batch_id,c.color_id, b.cons_quantity as issue_qnty 
		from inv_issue_master a, inv_transaction b, pro_batch_create_mst c, gbl_temp_engine d 
		where a.id=b.mst_id and b.pi_wo_batch_no=c.id and a.item_category=2 and a.entry_form in (71,18) and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.pi_wo_batch_no=d.ref_val and d.user_id = ".$user_id." and d.entry_form=50 and d.ref_from=2"); 
		$issue_array=array();
		foreach($data_array_issue as $row)
		{
			$issue_array[$row[csf("batch_id")]][$row[csf("color_id")]]['issue_qty']+=$row[csf("issue_qnty")];
		}
		unset($data_array_issue);
		
		// ============================== finish fab ============================== 
		$data_array_finsing_qty=sql_select("select  a.entry_form,b.batch_id,c.color_id,sum(b.receive_qnty) as receive_qnty,max(a.receive_date) as receive_date from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c, gbl_temp_engine d where a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.id=b.mst_id  and b.batch_id=c.id and  a.entry_form in(66,37,7) and a.company_id=$company_name and b.batch_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=50 and d.ref_from=2 group by a.entry_form, b.batch_id,c.color_id");
		
		$finishing_rec_qty_array=array();
		foreach($data_array_finsing_qty as $row)
		{
			if($row[csf("entry_form")]==7) //Knit Fin Recv page
			{
				$finishing_rec_qty_array[$row[csf("batch_id")]][$row[csf("color_id")]]['finishing_qty']=$row[csf("receive_qnty")];
			}
			else
			{
				$finishing_rec_qty_array2[$row[csf("batch_id")]][$row[csf("color_id")]]['finishing_qty']=$row[csf("receive_qnty")];
			}
			$finishing_date[$row[csf("batch_id")]][$row[csf("color_id")]]['fin_date']=$row[csf("receive_date")];
		}
		unset($data_array_finsing_qty);
		
		// ========================================= grey fab ==================================
		$grey_fabric_qnty=sql_select("select a.job_no, a.fabric_color_id, (a.grey_fab_qnty) as grey_fab_qnty, (a.fin_fab_qnty) as fin_fab_qnty, a.is_short from wo_booking_dtls a, gbl_temp_engine d
		where a.booking_type=1 and a.status_active=1 and a.is_deleted=0 and a.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=50 and d.ref_from=1");
		
		$grey_fabric_qnty_array=array();
		foreach($grey_fabric_qnty as $row)
		{
			$grey_fabric_qnty_array[$row[csf("job_no")]][$row[csf("fabric_color_id")]][$row[csf("is_short")]]['grey_fab_qnty']+=$row[csf("grey_fab_qnty")];
			$grey_fabric_qnty_array[$row[csf("job_no")]][$row[csf("fabric_color_id")]][$row[csf("is_short")]]['fin_fab_qnty']+=$row[csf("fin_fab_qnty")];
		}
		unset($grey_fabric_qnty);
		
		// =============================== del. to store ============================
		$sql_del_store=sql_select("select b.batch_id,a.color_id,(b.current_delivery) as delivery from pro_grey_prod_delivery_dtls b, pro_batch_create_mst a, gbl_temp_engine d where b.batch_id=a.id and  b.status_active=1 and b.is_deleted=0 and b.batch_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=50 and d.ref_from=2 ");
		$delivery=array();
		foreach($sql_del_store as $row)
		{
			$delivery[$row[csf("batch_id")]][$row[csf("color_id")]]['del_qty']+=$row[csf("delivery")];
		}
		unset($sql_del_store);
		// =============================== dyeing ===========================
		$sql_dyeing=sql_select("select b.id as batch_id,b.color_id,a.process_end_date from pro_fab_subprocess a, pro_batch_create_mst b, gbl_temp_engine d where b.id=a.batch_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=35 and b.entry_form=0 and a.load_unload_id=2 and b.id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=50 and d.ref_from=2 group by a.process_end_date,b.id,b.color_id");
		$dyeing_prod_arr=array();
		foreach($sql_dyeing as $row)
		{
			$dyeing_prod_arr[$row[csf("batch_id")]][$row[csf("color_id")]]['unload_date']=$row[csf("process_end_date")];
		}
		unset($sql_dyeing);	
		
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2) and ENTRY_FORM=50");
		oci_commit($con);
		disconnect($con);
		
		//-------------------------------------------
		$chk_sub_group_arr=array();
		foreach($batchreport as $color=>$batchdet)
		{
			foreach ( $batchdet as $row)
			{
				$issue_qty=$issue_array[$row["id"]][$color]['issue_qty'];
				$sub_total_finish_issue_qty_arr[$color]['finishQty_qty']+=$issue_qty;
			  
				$po_ids=array_unique(explode(",",$row["po_id"]));
				//$color_qty=0;
				foreach($po_ids as $po_id)
				{ 
				//	$color_qty+=$booking_grey_qty[$po_id][$color]['qty'];
				}		
				if (!in_array($color,$chk_sub_group_arr) )
				{
					//$color_qty=$color_qty;
					$chk_sub_group_arr[]=$color;   
				}
				//else $color_qty=0;
				//$sub_total_color_qty_arr[$color]['colorQty_qty']+=$color_qty;	  
			}
		}
		//------------------------------------------------
		//var_dump($sub_total_color_qty_arr );die;  
		
		ob_start();
		?>
		<script type="text/javascript">
			$(document).ready(function(e) {
			    setFilterGrid('tbl_list_search',-1);
				
			})
		</script>
		<style type="text/css">
			/*#td_idss{border:none !important;}
			#td_color_idsss{border:none !important;}*/
			#change_size{font-size:12px;}
		</style>
		<fieldset style="width:1615px" >
		    <table cellpadding="0" align="center" cellspacing="0" width="915">
				<tr>
				   <td  width="100%" colspan="24" class="form_caption"><? echo "<div style='color:red'> Ext. Batch not allowed.</div>".$report_title; ?></td>
				</tr>
			</table>
	    	<table width="915" align="center">
	            <tr>
		            <td id="change_size"><strong>Buyer Name:   </strong>
	                <?php
					$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
					$all_buyer='';
					foreach($batchreport as  $color=>$batchdet)
					{
						$mn=0; 
	                    foreach ( $batchdet as $row)
	                    { 
						  	if($all_buyer=='' ) $all_buyer= $row['buyer_name']; else $all_buyer.=",".$row['buyer_name'];
						}
					}
				   	$all_buyer_ids='';
				   	$buyer_ids=array_unique(explode(",",$all_buyer));
				   	foreach($buyer_ids as $bid)
				   	{
					 	if($all_buyer_ids=='' ) $all_buyer_ids=$buyer_arr[$bid]; else $all_buyer_ids.=",".$buyer_arr[$bid];  
				   	}
					echo $all_buyer_ids;//implode(",",array_unique(explode(",",$buyer_arr[$all_buyer])));
					?>
	                </td>
		            <td width="100"></td>
		            <td id="change_size"><strong>File No:</strong>
	                <?php
					$all_file_no='';
					foreach($batchreport as $color=>$batchdet)
					{
						$mn=0; 
						foreach ( $batchdet as $row)
						{ 
							if($all_file_no=='') $all_file_no= $row['file_no']; else $all_file_no.=",".$row['file_no'];
						}
					}
					echo implode(",",array_unique(explode(",",$all_file_no)));
					?> 
	                </td>
	                <td width="100"></td>
	                <td id="change_size"><strong>Internal Ref:   </strong>
					<?php
					$all_in_ref='';
					foreach($batchreport as $color=>$batchdet)
					{
						$mn=0; 
						foreach ( $batchdet as $row)
						{ 
							if($all_in_ref=='' ) $all_in_ref= $row['grouping']; else $all_in_ref.=",".$row['grouping'];
						}
					}
					echo implode(",",array_unique(explode(",",$all_in_ref)));
					?> 
	                </td>
	                <td width="100"></td>
		        </tr>
	            <tr>
		            <td><strong>Job No:</strong><?php
						$all_job='';
						foreach($batchreport as $color=>$batchdet)
						{
							$mn=0; 
		                    foreach ( $batchdet as $row)
		                    { 
							  	if($all_job=='' ) $all_job= $row['job_no_mst']; else $all_job.=",".$row['job_no_mst'];
							}
						}
						echo implode(",",array_unique(explode(",",$all_job)));
					?>
					</td>
		            <td width="100"></td>
		            <td id="change_size"><strong>Style Ref. No:</strong>
	                <?php
						$all_style_ref='';
						foreach($batchreport as $color=>$batchdet)
						{
							$mn=0; 
		                    foreach ( $batchdet as $row)
		                    { 
							  	if($all_style_ref=='' ) $all_style_ref= $row['style_ref_no']; else $all_style_ref.=",".$row['style_ref_no'];
							}
						}
						echo implode(",",array_unique(explode(",",$all_style_ref)));
					?>
	                </td>
	                <td width="100"></td>
		        </tr>
	        </table>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" width="1595"  >
				<thead>
					<tr>
	                	<th width="35">SL.</th>
						<th width="100">Color Name</th>
	                    <th width="100">Grey Req. As Per Booking</th>
						<th width="100">Batch No</th>
	                    <th width="80">Batch Qty.</th>
						<th width="120">Dye. Production Date</th>
	                    <th width="80">Finish Req. As per Booking</th>
	                    <th width="100">Finishing Qty</th>
	                    <th width="100">Finish Date</th>
						<th width="100">Process loss percentage</th>
	                    <th width="100">Finish Fab. Received</th>
	                    <th width="100">Finish Fab. Issue to cutting</th>
	                   	<th width="100">Yet to Issue</th>
	                    <th width="100">Finish Fab. In Hand</th>
	                    <th>Remarks</th>
	                </tr>
				</thead>
	        </table> 
	        <div style="width:1615px; overflow-y:scroll;  max-height:400px;" id="scroll_body">
	            <table id="tbl_list_search" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" width="1595">
					<? 
  					$colorchk_arr=array();$sub_group_arr=array();$check_sub_group_arr=array();
					$f=0;$total_batch_qty=$tot_finishingQty=0;
					$total_fin_grey_qty=0;
					$total_color_qty=0;
					$total_fin_qty=0;
					$total_finishinhand=0;
					$total_finish_fab_issue_qty=0;
					$total_store=0;
					$i=1;$k=1;
					//$batchreport=sql_select($sql_res);
					foreach($batchreport as $color=>$batchdet)
					{
						$mn=0; 
	                    foreach ( $batchdet as $row)
	                    {
							//  print_r($batchdet);
							$mn++; 
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							$po_ids=array_unique(explode(",",$row[("po_id")]));
							
							$fin_qty=$finishing_rec_qty_array[$row[("id")]][$row[("color_id")]]['finishing_qty'];//$finishing_qty[$row[("id")]][$row[("color_id")]]['fin_qty'];
							$del=$delivery[$row[("id")]][$row[("color_id")]]['del_qty'];
							$dyeing_date=$dyeing_prod_arr[$row[("id")]][$row[("color_id")]]['unload_date'];
							$fin_date=$finishing_date[$row[("id")]][$row[("color_id")]]['fin_date'];
							$fin_remarks=$finishing_remarks[$row[("id")]][$row[("color_id")]]['remarks'];
							$issue_qty=$issue_array[$row[("id")]][$row[("color_id")]]['issue_qty'];
							
							// $fab_greyQty=$grey_fabric_qnty_array[$row[("job_no_mst")]][$row[("color_id")]]['grey_fab_qnty'];
							// $color_qty=$grey_fabric_qnty_array[$row[("job_no_mst")]][$row[("color_id")]]['fin_fab_qnty'];

							$fab_greyMainQty=$grey_fabric_qnty_array[$row[("job_no_mst")]][$row[("color_id")]][2]['grey_fab_qnty'];
							$fab_greyShortQty=$grey_fabric_qnty_array[$row[("job_no_mst")]][$row[("color_id")]][1]['grey_fab_qnty'];

							$fab_greyQty=$fab_greyMainQty+$fab_greyShortQty;

							$color_Mainqty=$grey_fabric_qnty_array[$row[("job_no_mst")]][$row[("color_id")]][2]['fin_fab_qnty'];
							$color_Shortqty=$grey_fabric_qnty_array[$row[("job_no_mst")]][$row[("color_id")]][1]['fin_fab_qnty'];
							
							$color_qty=$color_Mainqty+$color_Shortqty;

							$issue_qty_calculation=0;	
							$issue_qty_calculation+=$issue_qty;
							$finishingQty=$finishing_rec_qty_array2[$row[("id")]][$row[("color_id")]]['finishing_qty'];
							
							//echo $finishingQty;		//echo $color_qty.'aa';
							if (!in_array($row[('color_id')],$sub_group_arr) )
							{
								//$sub_total_finish_issue_qty_arr[$row[("color_id")]]['finishQty_qty']+=$issue_qty;
								//$sub_total_color_qty_arr[$row[("color_id")]]['colorQty_qty']+=$color_qty;
								if($k!=1)
								{ 	
									?>
	                                <tr class="tbl_bottom">
										<td colspan="2"><strong>Sub. Total : </strong></td>
										<td align="right"><? echo number_format($sub_total_fin_grey_qty,2); ?></td>
										<td></td>
										<td align="right" width="80"><? echo number_format($sub_total_batch_qty,2); ?></td>
										<td></td>
										<td align="right"><? echo number_format($sub_total_color_fin_qty,2); ?></td> 
										<td align="right" width="80"><? echo number_format($sub_total_fin_qty,2); ?></td>
										<td width="100"><? //echo number_format($btq,2); ?></td>
										<td><? $sub_process_loss_per=(($sub_total_batch_qty-$sub_total_fin_qty)/$sub_total_batch_qty)*100;
										if($sub_total_fin_qty>0) echo number_format($sub_process_loss_per,2);
										else echo "";?></td>
										<td></td>
										<td align="right"><? echo number_format($sub_total_finish_fab_issue_qty,2); ?></td>
										<td></td>
									
										<td align="right" width="100"><? echo number_format($sub_total_finishInhand,2); ?></td>
										<td ></td>
	                                </tr>                                
									<?
									unset($sub_total_batch_qty);
									unset($sub_total_fin_grey_qty);
									unset($sub_total_color_fin_qty);
									unset($sub_total_fin_qty);
									unset($sub_total_store);
									unset($sub_total_finishInhand); 
									unset($sub_total_finish_fab_issue_qty);
								}
								?>
								
								<?
								$sub_group_arr[]=$row[('color_id')];            
								$k++;
							}
	                       	?>
	                        
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
							
								<? 
								if ($mn==1) // ( !in_array($row[('color_id')],$colorchk_arr) ) 
		            			{ 
		            				$f++;
		                			?>
		    						<td width="35" rowspan="<? echo count($batchdet); ?>" align="left" id="td_id" bgcolor="#E9F3FF" valign="middle"><? echo $f; ?></td>
		                            <td width="100" rowspan="<? echo count($batchdet); ?>" bgcolor="#E9F3FF"  id="td_color_id" valign="middle"><div style="word-break:break-all"><? echo $color_library[$row["color_id"]]; ?></div></td>
		                            <td width="100" rowspan="<? echo count($batchdet); ?>" bgcolor="#E9F3FF"  id="td_color_id" valign="middle" align="right" title="<? echo 'Main Qnty='.number_format($fab_greyMainQty,2).' & Short Qnty='.number_format($fab_greyShortQty,2);?>"><p><? echo number_format($fab_greyQty,2); ?></p></td>
		    						<?  	//$colorchk_arr[]=$row[('color_id')];
							  	
									$total_fin_grey_qty+=$fab_greyQty;
									$sub_total_fin_grey_qty+=$fab_greyQty;
								}
								?>   	
		                            <td width="100"><div style="word-break:break-all"><? echo $row[("batch_no")];?></div></td>
		                            <td width="80" align="right"><p><? echo number_format($row[("batch_qnty")],2); ?></p></a></p></td>
		                            <td width="120" align="center"><p><? echo change_date_format($dyeing_date); ?></p></td>
		                    	<? 
		                    	if ($mn==1) 
		            			{ 
									$sub_total_color_fin_qty+=$color_qty;$total_color_qty+=$color_qty;
		                			?>
		                    		<td width="80" align="right" rowspan="<? echo count($batchdet); ?>" title="<? echo $row[("po_id")].' and Main Qnty='.number_format($color_Mainqty,2).' & Short Qnty='.number_format($color_Shortqty,2);?>"><p><? echo number_format($color_qty,2)?></p></td>
		    						<?  	
							  	}
								?>   	
		                            
	                            <td width="100" align="right"><p><? echo number_format($fin_qty,2);?></p></td>
	                            <td align="center" width="100" id="td_color_idd" valign="middle"><div style="word-break:break-all"><? echo change_date_format($fin_date); ?></div></td>
	                            <td width="100" align="right" title="(Batch Qty-Finish Qty/Batch Qty)*100"><p>
	  								<?
									$bat_qty=$row[("batch_qnty")]; 
									if($fin_qty!='')
									{
										// $batch_fin=(($bat_qty/$fin_qty)*100)-100;
										 $batch_fin=(($bat_qty-$fin_qty)/$bat_qty)*100;
										 echo $result=number_format((float)$batch_fin, 2, '.', '');
									}
									else
									{ 
										echo " ";
									}
									?></p>
	                     		</td>
	                            <td align="right" width="100"   id="td_color_idd" valign="middle"><div style="word-break:break-all"><?  echo number_format($finishingQty,2); ?></div></td>
	                            <td align="right" width="100"   id="td_color_iddd" valign="middle"><div style="word-break:break-all"><?  echo number_format($issue_qty,2); ?></div></td>

		                     	<? 
		                     	if ($mn==1) 
		            			{
		                			?>
		                            <td align="center" width="100" rowspan="<? echo count($batchdet); ?>" bgcolor="#E9F3FF"  id="td_color_idd" valign="middle" title="Finish Req. As per Booking-Finish Fab. Issue to cutting">
		                            <div style="word-break:break-all"><? echo number_format($sub_total_color_fin_qty-$sub_total_finish_issue_qty_arr[$row[("color_id")]]['finishQty_qty'],2); 
									//echo $sub_total_finish_issue_qty_arr[$row["color_id"]]['finishQty_qty'];?></div></td>
		    						<?  	
							  	}
								?>
	                            <td  align="right"  width="100"><? $finishInhand=$finishingQty-$issue_qty;//$fin_qty-$del; 
								echo number_format($finishInhand,2); ?></td>
	                            <td   align="left"><p><? echo $fin_remarks; ?></p></td>
							</tr>

			 				<?
                            $i++;
							$sub_total_batch_qty+=$row[("batch_qnty")];
						
							//$sub_total_fin_grey_qty+=$fab_greyQty;
							$sub_total_color_qty+=$color_qty;
							$sub_total_fin_qty+=$fin_qty;
							
							$sub_total_store+=$del;
							$sub_total_finishInhand+=$finishInhand;
							$sub_total_finish_fab_issue_qty+=$issue_qty;
							//$cal_yetqty=($sub_total_color_qty+=$color_qty)-($sub_total_finish_fab_issue_qty+=$issue_qty);
							$tot_avg_process_loss+=$result;
							$tot_finishingQty+=$finishingQty;
							$total_batch_qty+=$row[("batch_qnty")];
							//$total_fin_grey_qty+=$fab_greyQty;
							
							$total_fin_qty+=$fin_qty; 
							$total_store+=$del;
							$total_finishinhand+=$finishInhand;
							$total_finish_fab_issue_qty+=$issue_qty;
	                    }
							
					}
					if (!in_array($row[('color_id')],$sub_group_arr) )
					{
						if($k!=1)
						{ 	
						?>
                            <tr class="tbl_bottom">
                               
                                <td colspan="2"><strong>Sub. Total : </strong></td>
								<td align="right"><? echo number_format($sub_total_fin_grey_qty,2); ?></td>
								<td></td>
								<td align="right" width="80"><? echo number_format($sub_total_batch_qty,2); ?></td>
								<td></td>
								<td align="right"><? echo number_format($sub_total_color_fin_qty,2); ?></td> 
								<td align="right" width="80"><? echo number_format($sub_total_fin_qty,2); ?></td>
								<td width="100"><? //echo number_format($btq,2); $batch_fin=(($bat_qty-$fin_qty)/$bat_qty)*100; ?></td>
								<td><? $sub_process_loss_per=(($sub_total_batch_qty-$sub_total_fin_qty)/$sub_total_batch_qty)*100;
								if($sub_total_fin_qty>0) echo number_format($sub_process_loss_per,2);
								else echo "";
								  ?></td>
								<td></td>
								<td align="right"><? echo number_format($sub_total_finish_fab_issue_qty,2); ?></td>
								<td></td>
								
								<td align="right" width="100"><? echo number_format($sub_total_finishInhand,2); ?></td>
								<td ></td>
                            </tr>                                
							<?
							unset($sub_total_batch_qty);
							unset($sub_total_fin_grey_qty);
							unset($sub_total_color_fin_qty);
							unset($sub_total_fin_qty);
							unset($sub_total_store);
							unset($sub_total_finishInhand);
							unset($sub_total_finish_fab_issue_qty);
							
						}
						?>
						
						<?
						$sub_group_arr[]=$row[('color_id')];            
						$k++;
					}
					?>
                    <tr class="tbl_bottom">
                        <td colspan="2"><strong>Sub. Total : </strong></td>
                        <td align="right"><? echo number_format($sub_total_fin_grey_qty,2); ?></td>
                        <td></td>
                        <td align="right" width="80"><? echo number_format($sub_total_batch_qty,2); ?></td>
                        <td></td>
                        <td align="right"><? echo number_format($sub_total_color_fin_qty,2); ?></td> 
                        <td align="right" width="80"><? echo number_format($sub_total_fin_qty,2); ?></td>
                        <td width="100"><? //echo number_format($btq,2); ?></td>
                        <td><? $sub_process_loss_per=(($sub_total_batch_qty-$sub_total_fin_qty)/$sub_total_batch_qty)*100;
								if($sub_total_fin_qty>0) echo number_format($sub_process_loss_per,2);
								else echo "";
								 ?></td>
                        <td></td>
                        <td align="right"><? echo number_format($sub_total_finish_fab_issue_qty,2); ?></td>
                        <td></td>
                        
                        <td  align="right" width="100"><? echo number_format($sub_total_finishInhand,2); ?></td>
                        <td ></td>
                    </tr>   

					<tr class="tbl_bottom">                    
						<td colspan="2"><strong>Grand Total : </strong></td>
						<td align="right"><? echo number_format($total_fin_grey_qty,2); ?></td>
						<td></td>
						<td align="right"><? echo number_format($total_batch_qty,2)?> </td> 
						<td></td>
                        <td align="right"><? echo number_format($total_color_qty,2)?> </td> 
                        <td align="right"><? echo number_format($total_fin_qty,2);?></td>
						<td></td>
						<td align="right"><? //$tot_processloss_avg=(($total_batch_qty-$total_fin_qty)/$total_batch_qty)*100;
							//echo number_format($tot_processloss_avg,2);?></td>
						<td align="right"><? echo number_format($tot_finishingQty,2);?></td>
						<td align="right"><? echo number_format($total_finish_fab_issue_qty,2);?></td>
						<td></td>
                     
						<td align="right"><? echo number_format($total_finishinhand,2);?></td>
                        <td></td>
			        </tr>
				</table> 
			</div>
		</fieldset>
		<?
		
		foreach (glob("$user_id*.xls") as $filename) 
		{
			if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc,ob_get_contents());
		$filename=$user_id."_".$name.".xls";
		echo "$total_data####$filename";
		
		disconnect($con);
		exit();
	} // Show End

	if ($type==2) // Show 2 Start
	{
		$constructtion_arr=array();
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
		$data_array=sql_select($sql_deter);
		foreach( $data_array as $row )
		{
			$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		}
		unset($data_array);
	 	$sql_res="SELECT a.batch_no,a.id,d.company_name,d.buyer_name,d.style_ref_no,e.file_no,e.grouping,a.color_id,b.po_id,sum(b.batch_qnty) as batch_qnty,e.job_no_mst, b.item_description, f.detarmination_id
	 	from pro_batch_create_mst a,pro_batch_create_dtls b , wo_po_details_master d,wo_po_break_down e, product_details_master f 
	 	where b.po_id=e.id and d.job_no=e.job_no_mst and a.id=b.mst_id and b.prod_id=f.id and a.entry_form=0  and a.status_active=1 and b.status_active=1  and d.company_name=$cbo_company_name and a.batch_against != 2 $buyer_cond  $batch_cond $file_cond $ref_cond";
		// echo $sql_res;
		// and A.BATCH_NO='B400' 
		$header=sql_select($sql_res);
		$batchreport = array();
		$batchIdArray = array();
		$poIdArray = array();
		$contru_arr = array();
		$all_po_id="";
		foreach($header as $row)
		{
			if ($sub_group_arr[$row[csf('color_id')]]=='')
			{
				$i=0;
				$sub_group_arr[$row[csf('color_id')]]=$row[csf('color_id')];
			}
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]]['color_id']=$row[csf('color_id')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]]['po_id'].=$row[csf('po_id')].',';
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]]['job_no_mst']=$row[csf('job_no_mst')];
			// $batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]]['batch_qnty']=$row[csf('batch_qnty')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]]['production_date']=$row[csf('production_date')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]]['company_name']=$row[csf('company_name')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]]['id']=$row[csf('id')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]]['grouping']=$row[csf('grouping')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]]['file_no']=$row[csf('file_no')];
			$batchIdArray[$row[csf('id')]] = $row[csf('id')];
			$poIdArray[$row[csf('po_id')]] = $row[csf('po_id')];
			$ref_no="'".$row[csf('grouping')]."'";
			$RefNoArray[$ref_no]=$ref_no;

			// $constructionArray[$row[csf('item_description')]] = $row[csf('item_description')];
			$construction_name=$constructtion_arr[$row[csf('detarmination_id')]];
			$contru_arr[$constructtion_arr[$row[csf('detarmination_id')]]]=$construction_name;
			$batch_qty_arr[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]][$construction_name]+=$row[csf('batch_qnty')];
			
			if($all_po_id=="") $all_po_id=$row[csf('po_id')]; else $all_po_id.=",".$row[csf('po_id')]; //echo $all_po_id;
			$i++;
		}		
		// echo "<pre>"; print_r($contru_arr);die;

		/*$contru_arr=array();
		foreach ($constructionArray as $key => $value) 
		{
			$contru=explode(",", $value);
			$contru_arr[$contru[0]]=$contru[0];
		}
		$count_constr=count($contru_arr);*/
		$count_constr=count($contru_arr);

		if(count($batchreport)==0)
		{
			?>
			<div style="font-weight: bold;color: red;font-size: 20px;text-align: center;">Dana not found! Please try again.</div>
			<?
			die();
		}
		//===============================================================
		if($txt_inter_ref!="")
		{
			$RefNos_cond="";
		}
		else  
		{ 
			$RefNos= implode(",", $RefNoArray);
		 	if( $RefNos!="")  $RefNos_cond="and c.grouping in($RefNos)";else $RefNos_cond="";
		}
		
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (3,4) and ENTRY_FORM=50");
		oci_commit($con);
		
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 50, 3, $poIdArray, $empty_arr);//Po ID
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 50, 4, $batchIdArray, $empty_arr);//Batch ID
		disconnect($con);

		// ====================== Req. Qty As Per Booking Color Wise ========================
		$grey_fabric_qnty=sql_select("SELECT a.job_no, a.fabric_color_id, d.construction, (a.grey_fab_qnty) as grey_fab_qnty,(a.fin_fab_qnty) as fin_fab_qnty
		from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls d, gbl_temp_engine e
		where a.pre_cost_fabric_cost_dtls_id=d.id and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 and a.po_break_down_id=e.ref_val and e.user_id = ".$user_id." and e.entry_form=50 and e.ref_from=3");
		
		$grey_fabric_qnty_array=array();
		foreach($grey_fabric_qnty as $row)
		{
			$grey_fabric_qnty_array[$row[csf("job_no")]][$row[csf("fabric_color_id")]][$row[csf("construction")]]['grey_fab_qnty']+=$row[csf("grey_fab_qnty")];
			$grey_fabric_qnty_array[$row[csf("job_no")]][$row[csf("fabric_color_id")]][$row[csf("construction")]]['fin_fab_qnty']+=$row[csf("fin_fab_qnty")];
		}
		unset($grey_fabric_qnty);

		// ==================== QC Pass Qty, Received qty and Issue Return qty ==================
		$data_array_finsing_qty=sql_select("SELECT a.entry_form, c.color_id, b.batch_id, b.fabric_description_id, sum(b.receive_qnty) as receive_qnty
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c, gbl_temp_engine e
		where a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.id=b.mst_id  and b.batch_id=c.id and  a.entry_form in(37,7,52) and a.company_id=$company_name and b.batch_id=e.ref_val and e.user_id = ".$user_id." and e.entry_form=50 and e.ref_from=4 group by a.entry_form,c.color_id, b.batch_id, b.fabric_description_id");

		$qc_pass_qnty_array=array();$finishing_rec_qty_array=array();
		foreach($data_array_finsing_qty as $row)
		{
			if($row[csf("entry_form")]==7) // finish fabric production entry page
			{
				$qc_pass_qnty_array[$row[csf("color_id")]][$row[csf("batch_id")]][$constructtion_arr[$row[csf("fabric_description_id")]]]+=$row[csf("receive_qnty")];
			}
			else if($row[csf("entry_form")]==37) // Knit Finish Fabric Receive By Garments
			{
				$finishing_rec_qty_array[$row[csf("color_id")]][$row[csf("batch_id")]][$constructtion_arr[$row[csf("fabric_description_id")]]]+=$row[csf("receive_qnty")];
			}
			else // 52 Knit Finish Fabric Issue Return
			{
				$finish_issue_rtn_qty_array[$row[csf("color_id")]][$row[csf("batch_id")]][$constructtion_arr[$row[csf("fabric_description_id")]]]+=$row[csf("receive_qnty")];
			}
		}
		unset($data_array_finsing_qty);
		// echo "<pre>";print_r($finish_issue_rtn_qty_array);die;

		// =============================== del. to store ============================
		$sql_del_store=sql_select("SELECT b.batch_id, a.color_id, b.determination_id, (b.current_delivery) as delivery from pro_grey_prod_delivery_dtls b, pro_batch_create_mst a, gbl_temp_engine e where b.batch_id=a.id and  b.status_active=1 and b.is_deleted=0 and b.entry_form=54 and b.batch_id=e.ref_val and e.user_id = ".$user_id." and e.entry_form=50 and e.ref_from=4 group by b.batch_id,a.color_id, b.determination_id");
		$delivery=array();
		foreach($sql_del_store as $row)
		{
			$delivery[$row[csf("color_id")]][$row[csf("batch_id")]][$constructtion_arr[$row[csf("determination_id")]]]+=$row[csf("delivery")];
		}
		unset($sql_del_store);
		// echo "<pre>";print_r($delivery);die;

		// =================================== issue =======================================
		$data_array_issue=sql_select("SELECT c.color_id, c.id as batch_id, d.detarmination_id, (b.cons_quantity) as issue_qnty
		 from inv_issue_master a, inv_transaction b, pro_batch_create_mst c, product_details_master d, gbl_temp_engine e
		where a.id=b.mst_id and b.pi_wo_batch_no=c.id  and b.prod_id=d.id and a.item_category=2 and a.entry_form in (71,18) and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.id=e.ref_val and e.user_id = ".$user_id." and e.entry_form=50 and e.ref_from=4");//group by c.color_id, c.id, d.detarmination_id
		$issue_array=array();
		foreach($data_array_issue as $row)
		{
			$issue_array[$row[csf("color_id")]][$row[csf("batch_id")]][$constructtion_arr[$row[csf("detarmination_id")]]]+=$row[csf("issue_qnty")];
		}
		unset($data_array_issue);
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (3,4) and ENTRY_FORM=50");
		oci_commit($con);
		disconnect($con);
		// echo "<pre>";print_r($issue_array);die;

		//$table_width=2430;
		// $table_width=1030;
		$table_width=($count_constr*930)+100;
		ob_start();
		?>
		
		<style type="text/css">
			/*#td_idss{border:none !important;}
			#td_color_idsss{border:none !important;}*/
			#change_size{font-size:12px;}
		</style>
		<fieldset style="width:<? echo $table_width;?>px" >
		    <table cellpadding="0" align="center" cellspacing="0" width="<? echo $table_width-20; ?>">
				<tr>
				   <td  width="100%" colspan="24" class="form_caption"><? echo $report_title; ?></td>
				   <!-- "<div style='color:red'> Ext. Batch not allowed.</div>". -->
				</tr>
			</table>
			<table width="915" align="center">
	            <tr>
		            <td id="change_size"><strong>Buyer Name:</strong>
	                <?php
					$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
					$all_buyer='';
					foreach($batchreport as $job_no=>$job_no_arr)
					{ 
	                    foreach ($job_no_arr as $color_id=>$colorId_arr)
	                    {
	                    	foreach ($colorId_arr as $batch_no=>$row)
	                    	{ 
						  		if($all_buyer=='' ) $all_buyer= $row['buyer_name']; else $all_buyer.=",".$row['buyer_name'];
						  	}
						}
					}
				   	$all_buyer_ids='';
				   	$buyer_ids=array_unique(explode(",",$all_buyer));
				   	foreach($buyer_ids as $bid)
				   	{
					 	if($all_buyer_ids=='' ) $all_buyer_ids=$buyer_arr[$bid]; else $all_buyer_ids.=",".$buyer_arr[$bid];  
				   	}
					echo $all_buyer_ids;//implode(",",array_unique(explode(",",$buyer_arr[$all_buyer])));
					?>
	                </td>
		            <td width="100"></td>
		            <td id="change_size"><strong>Style Ref. No:</strong>
	                <?php
						$all_style_ref='';
						foreach($batchreport as $job_no=>$job_no_arr)
						{ 
		                    foreach ($job_no_arr as $color_id=>$colorId_arr)
		                    {
		                    	foreach ($colorId_arr as $batch_no=>$row)
		                    	{
							  		if($all_style_ref=='' ) $all_style_ref= $row['style_ref_no']; else $all_style_ref.=",".$row['style_ref_no'];
							  	}
							}
						}
						echo implode(",",array_unique(explode(",",$all_style_ref)));
					?>
	                </td>
	                <td width="100"></td>
	                <td id="change_size"><strong>Internal Ref:</strong>
					<?php
					$all_in_ref='';
					foreach($batchreport as $job_no=>$job_no_arr)
					{ 
	                    foreach ($job_no_arr as $color_id=>$colorId_arr)
	                    {
	                    	foreach ($colorId_arr as $batch_no=>$row)
	                    	{ 
								if($all_in_ref=='' ) $all_in_ref= $row['grouping']; else $all_in_ref.=",".$row['grouping'];
							}
						}
					}
					echo implode(",",array_unique(explode(",",$all_in_ref)));
					?> 
	                </td>
	                <td width="100"></td>
	                <td><strong>Job No:</strong><?php
					$all_job='';
					foreach($batchreport as $job_no=>$job_no_arr)
					{ 
	                    foreach ($job_no_arr as $color_id=>$colorId_arr)
	                    {
	                    	foreach ($colorId_arr as $batch_no=>$row)
	                    	{
	                    		if($all_job=='' ) $all_job= $row['job_no_mst']; else $all_job.=",".$row['job_no_mst'];
	                    	}						  	
						}
					}
					echo implode(",",array_unique(explode(",",$all_job)));
					?></td>
					<td width="100"></td>
		        </tr>
	        </table>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" width="<? echo $table_width-20; ?>">
				<thead>
					<tr>
	                	<th rowspan="2" width="35">SL.</th>
						<th rowspan="2" width="100">Color Name</th>
						<th rowspan="2" width="100">Batch No</th>
						<th rowspan="2" width="100">Job No</th>
						<th rowspan="2" width="100">Internal Ref</th>

	                    <th title="<? echo $count_constr; ?>" colspan="<? echo $count_constr; ?>" width="<? echo $count_constr*80; ?>">Grey Req. & Batch Qty</th>
	                    
	                    <th colspan="<? echo $count_constr; ?>" width="<? echo $count_constr*80; ?>">Finish Req. & QC Pass Qty</th>

						<th colspan="<? echo $count_constr; ?>" width="<? echo $count_constr*80; ?>">Process Loss</th>

						<th colspan="<? echo $count_constr; ?>" width="<? echo $count_constr*80; ?>">Finish Req. & Delivery to Store</th>

						<th colspan="<? echo $count_constr; ?>" width="<? echo $count_constr*80; ?>">Finish Req. & Received</th>

						<th colspan="<? echo $count_constr; ?>" width="<? echo $count_constr*80; ?>">Finish Req. & Net. Issued qty</th>
						<th colspan="<? echo $count_constr; ?>" width="<? echo $count_constr*80; ?>">Stock In Hand</th>
	                </tr>
	                <tr>
	                	<?
	                	foreach ($contru_arr as $key => $contruction) 
	                	{
	                		?>
	                		<th width="80"><? echo $contruction; ?></th>
	                		<?
	                	}
	                	foreach ($contru_arr as $key => $contruction) 
	                	{
	                		?>
	                		<th width="80"><? echo $contruction; ?></th>
	                		<?
	                	}
	                	foreach ($contru_arr as $key => $contruction) 
	                	{
	                		?>
	                		<th width="80"><? echo $contruction; ?></th>
	                		<?
	                	}
	                	foreach ($contru_arr as $key => $contruction) 
	                	{
	                		?>
	                		<th width="80"><? echo $contruction; ?></th>
	                		<?
	                	}
	                	foreach ($contru_arr as $key => $contruction) 
	                	{
	                		?>
	                		<th width="80"><? echo $contruction; ?></th>
	                		<?
	                	}
	                	foreach ($contru_arr as $key => $contruction) 
	                	{
	                		?>
	                		<th width="80"><? echo $contruction; ?></th>
	                		<?
	                	}
	                	foreach ($contru_arr as $key => $contruction) 
	                	{
	                		?>
	                		<th width="80"><? echo $contruction; ?></th>
	                		<?
	                	}
	                	?>
	                </tr>
				</thead>
	        </table> 
	        <div style="width:<? echo $table_width;?>px; overflow-y:scroll;  max-height:400px;" id="scroll_body">
	            <table id="tbl_list_search" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" width="<? echo $table_width-20; ?>">
	            	
		            <?
					$i=1;
					foreach($batchreport as $job_no=>$job_no_arr)
					{						
						foreach ($job_no_arr as $color_id=>$colorId_arr)
	                    {
	                    	$fab_greyQty_color_bal_arr=array();$fab_finQty_color_bal_arr=array();

	                    	$color_total_batch_qty_arr=array();
	                    	$color_balance_batch_qty_arr=array();

	                    	$color_total_qc_pass_arr=array();
	                    	$color_balance_qc_pass_qty_arr=array();	                    	

	                    	$color_total_delivery_arr=array();
	                    	$color_balance_delivery_qty_arr=array();

	                    	$color_total_recv_arr=array();
	                    	$color_balance_recv_qty_arr=array();

	                    	$color_total_issue_arr=array();
	                    	$color_balance_issue_qty_arr=array();

	                    	$color_total_inHand_arr=array();
	                    	$color_balance_inHand_qty_arr=array();

	                    	$process_loss_total_batch_qty_arr=array();
	                    	$process_loss_total_qcPass_qty_arr=array();

	                    	$job_process_loss_total_batch_qty_arr=array();
	                    	$job_process_loss_total_qcPass_qty_arr=array();

							$job_bal_process_loss_total_batch_qty_arr=array();
							$job_bal_process_loss_total_qcPass_qty_arr=array();
	                    	?>
	                    	<tr bgcolor="#FFFFAA">
			            		<td colspan="5" align="right">Req. Qty As Per Booking Color Wise</td>
			                    <?
			                    foreach ($contru_arr as $key => $contruction)
			                	{// booking grey qty
			                		$fab_greyQty=$grey_fabric_qnty_array[$job_no][$color_id][$contruction]['grey_fab_qnty'];
			                		?>
			                		<td width="80" align="right"><? echo number_format($fab_greyQty,2,'.',''); ?></td>
			                		<?
			                		$fab_greyQty_color_bal_arr[$contruction]+=$fab_greyQty;
			                		$fab_greyQty_arr[$contruction]=$fab_greyQty;
			                	}
			                	foreach ($contru_arr as $key => $contruction)
			                	{// booking fin qty
			                		$fab_finQty=$grey_fabric_qnty_array[$job_no][$color_id][$contruction]['fin_fab_qnty'];			                		
			                		?>
			                		<td width="80" align="right"><? echo number_format($fab_finQty,2,'.',''); ?></td>
			                		<?
			                		$fab_finQty_arr[$contruction]=$fab_finQty;
			                		$fab_finQty_color_bal_arr[$contruction]+=$fab_finQty;
			                	}
			                	foreach ($contru_arr as $key => $contruction)
			                	{// booking process loss
			                		$fab_greyQty_process_loss=$fab_greyQty_arr[$contruction];
			                		$fab_finQty_process_loss=$fab_finQty_arr[$contruction];
			                		$book_process_loss=0;
			                		if ($fab_greyQty_process_loss) 
			                		{
			                			$book_process_loss=($fab_greyQty_process_loss-$fab_finQty_process_loss)/$fab_finQty_process_loss*100;
			                		}
			                		?>
			                		<td width="80" title="(<? echo $fab_greyQty_process_loss.'-'.$fab_finQty_process_loss.')/'.$fab_finQty_process_loss; ?>" align="right"><? echo number_format($book_process_loss,2,'.',''); ?>%</td>
			                		<?
			                	}
			                	foreach ($contru_arr as $key => $contruction)
			                	{// booking fin qty
			                		$fab_finQty=$grey_fabric_qnty_array[$job_no][$color_id][$contruction]['fin_fab_qnty'];
			                		?>
			                		<td width="80" align="right"><? echo number_format($fab_finQty,2,'.',''); ?></td>
			                		<?
			                	}
			                	foreach ($contru_arr as $key => $contruction)
			                	{// booking fin qty
			                		$fab_finQty=$grey_fabric_qnty_array[$job_no][$color_id][$contruction]['fin_fab_qnty'];
			                		?>
			                		<td width="80" align="right"><? echo number_format($fab_finQty,2,'.',''); ?></td>
			                		<?
			                	}
			                	foreach ($contru_arr as $key => $contruction)
			                	{// booking fin qty
			                		$fab_finQty=$grey_fabric_qnty_array[$job_no][$color_id][$contruction]['fin_fab_qnty'];
			                		?>
			                		<td width="80" align="right"><? echo number_format($fab_finQty,2,'.',''); ?></td>
			                		<?
			                	}
			                	foreach ($contru_arr as $key => $contruction)
			                	{//empty
			                		$fab_finQty=$grey_fabric_qnty_array[$job_no][$color_id][$contruction]['fin_fab_qnty'];
			                		?>
			                		<td width="80" align="right"></td>
			                		<?
			                	}
			                    ?>
			                </tr>
	                    	<?
		                    foreach ($colorId_arr as $batch_no=>$row)
		                    {
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$po_ids=array_unique(explode(",",$row[("po_id")]));
		                       	?>
		                        
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
		    						<td width="35" align="left" id="td_id" valign="middle"><? echo $i; ?></td>
		                            <td width="100" id="td_color_id" valign="middle"><div style="word-break:break-all"><? echo $color_library[$row["color_id"]]; ?></div></td>
		                            <td width="100" title="Batch ID:<? echo $row[("id")]; ?>"><div style="word-break:break-all"><? echo $row[("batch_no")];?></div></td>
		                            <td width="100"><p><? echo $job_no; ?></p></td>
		                            <td width="100"><p><? echo $row[("grouping")]; ?></p></td>
		                            
		                            <?
				                	foreach ($contru_arr as $key => $contruction)
				                	{//batch qty
				                		$batch_qnty=$batch_qty_arr[$job_no][$color_id][$row["id"]][$contruction];				                		
				                		?>
				                		<td width="80" align="right" title="<? echo $job_no.'==='.$color_id; ?>"><? echo number_format($batch_qnty,2,'.',''); ?></td>
				                		<?
				                		$batch_qnty_pro_loss_arr[$contruction]=$batch_qnty;
				                		$color_total_batch_qty_arr[$contruction]+=$batch_qnty;
				                	}
				                	foreach ($contru_arr as $key => $contruction)
				                	{//qc pass
				                		$qc_pass_qnty=$qc_pass_qnty_array[$color_id][$row["id"]][$contruction];
				                		?>
				                		<td width="80" align="right" title="<? echo $color_id.', '.$row["id"].', '.$contruction; ?>"><? echo number_format($qc_pass_qnty,2,'.',''); ?></td>
				                		<?
				                		$qc_pass_pro_loss_arr[$contruction]=$qc_pass_qnty;
				                		$color_total_qc_pass_arr[$contruction]+=$qc_pass_qnty;
				                	}
				                	foreach ($contru_arr as $key => $contruction)
				                	{//process loss
				                		$batch_qnty_pro_loss=$batch_qnty_pro_loss_arr[$contruction];
				                		$qc_pass_pro_loss=$qc_pass_pro_loss_arr[$contruction];
				                		if ($batch_qnty_pro_loss>0) 
				                		{
				                			$process_loss=($batch_qnty_pro_loss-$qc_pass_pro_loss)/$qc_pass_pro_loss*100;
				                		}
				                		else
				                		{
				                			$process_loss=0;
				                		}
				                		
				                		?>
				                		<td width="80" title="(<? echo $batch_qnty_pro_loss.'-'.$qc_pass_pro_loss.')/'.$qc_pass_pro_loss; ?>" align="right"><? echo number_format($process_loss,2,'.',''); ?>%</td>
				                		<?
				                	}
				                	foreach ($contru_arr as $key => $contruction)
				                	{//fin delivery to store
				                		$fin_delivery_qty=$delivery[$color_id][$row["id"]][$contruction];
				                		?>
				                		<td width="80" align="right"><? echo number_format($fin_delivery_qty,2,'.',''); ?></td>
				                		<?
				                		$color_total_delivery_arr[$contruction]+=$fin_delivery_qty;
				                	}
				                	foreach ($contru_arr as $key => $contruction)
				                	{//fin recv qty
				                		$fin_recv_qnty=$finishing_rec_qty_array[$color_id][$row["id"]][$contruction];
				                		$recv_qnty_for_inHand_arr[$contruction]=$fin_recv_qnty;
				                		?>
				                		<td width="80" align="right"><? echo number_format($fin_recv_qnty,2,'.',''); ?></td>
				                		<?
				                		$color_total_recv_arr[$contruction]+=$fin_recv_qnty;
				                	}
				                	foreach ($contru_arr as $key => $contruction)
				                	{// net issue qty
				                		$fin_issue_qnty=$issue_array[$color_id][$row["id"]][$contruction];
				                		$fin_issue_rtn_qnty=$finish_issue_rtn_qty_array[$color_id][$row["id"]][$contruction];
				                		$net_issued_qty=$fin_issue_qnty-$fin_issue_rtn_qnty;
				                		$issue_qty_for_inHand_arr[$contruction]=$net_issued_qty;
				                		?>
				                		<td width="80" align="right" title="fin return=<? echo $fin_issue_rtn_qnty;?>"><? echo number_format($net_issued_qty,2,'.',''); ?></td>
				                		<?
				                		$color_total_issue_arr[$contruction]+=$net_issued_qty;
				                	}
				                	foreach ($contru_arr as $key => $contruction)
				                	{ // Stock In Hand
				                		$recv_qty=$recv_qnty_for_inHand_arr[$contruction];
				                		$net_issue=$issue_qty_for_inHand_arr[$contruction];
				                		$stock_in_hand=$recv_qty-$net_issue;
				                		?>
				                		<td width="80" align="right"><? echo number_format($stock_in_hand,2,'.',''); ?></td>
				                		<?
				                		$color_total_inHand_arr[$contruction]+=$stock_in_hand;
				                	}
				                	?>
								</tr>
					 			<?
	                            $i++;
		                    } // color total below
		                    ?>
		                    <tr class="tbl_bottom">
		                        <td colspan="5"><strong>Color Total</strong></td>
		                        <?
		                        foreach ($contru_arr as $key => $contruction)
			                	{//color total Grey Req. & Batch Qty
			                		$color_total_batch_qty = $color_total_batch_qty_arr[$contruction];
			                		?>
			                		<td  align="right"><? echo number_format($color_total_batch_qty,2,'.',''); ?></td>
			                		<?
			                		$color_balance_batch_qty_arr[$contruction]+=$color_total_batch_qty;
			                		$job_total_batch_qty_arr[$contruction]+=$color_total_batch_qty;
			                		$process_loss_total_batch_qty_arr[$contruction]+=$color_total_batch_qty;
			                	}
			                	foreach ($contru_arr as $key => $contruction)
			                	{//color total Finish Req. & QC Pass Qty
			                		$color_total_qc_pass_qty = $color_total_qc_pass_arr[$contruction];
			                		?>
			                		<td  align="right"><? echo number_format($color_total_qc_pass_qty,2,'.',''); ?></td>
			                		<?
			                		$color_balance_qc_pass_qty_arr[$contruction]+=$color_total_qc_pass_qty;
			                		$job_total_qc_pass_qty_arr[$contruction]+=$color_total_qc_pass_qty;
			                		$process_loss_total_qc_pass_qty_arr[$contruction]+=$color_total_qc_pass_qty;
			                		$process_loss_total_qcPass_qty_arr[$contruction]+=$color_total_qc_pass_qty;
			                	}
			                	foreach ($contru_arr as $key => $contruction)
			                	{//color total Process Loss
			                		$process_loss_total_batch_qty=$process_loss_total_batch_qty_arr[$contruction];
			                		$process_loss_total_qcPass_qty=$process_loss_total_qcPass_qty_arr[$contruction];
			                		if ($process_loss_total_batch_qty>0) 
			                		{
			                			$process_loss_total=($process_loss_total_batch_qty-$process_loss_total_qcPass_qty)/$process_loss_total_qcPass_qty*100;
			                		}
			                		else
			                		{
			                			$process_loss_total=0;
			                		}
			                		
			                		?>
			                		<td  align="right"><? echo number_format($process_loss_total,2,'.',''); ?>%</td>
			                		<?
			                	}
			                	foreach ($contru_arr as $key => $contruction)
			                	{//color total Finish Req. & Delivery to Store
			                		$color_total_delivery_qty = $color_total_delivery_arr[$contruction];
			                		?>
			                		<td  align="right"><? echo number_format($color_total_delivery_qty,2,'.',''); ?></td>
			                		<?
			                		$color_balance_delivery_qty_arr[$contruction]+=$color_total_delivery_qty;
			                		$job_total_delivery_qty_arr[$contruction]+=$color_total_delivery_qty;
			                	}
			                	foreach ($contru_arr as $key => $contruction)
			                	{//color total Finish Req. & Received
			                		$color_total_recv_qty = $color_total_recv_arr[$contruction];
			                		?>
			                		<td  align="right"><? echo number_format($color_total_recv_qty,2,'.',''); ?></td>
			                		<?
			                		$color_balance_recv_qty_arr[$contruction]+=$color_total_recv_qty;
			                		$job_total_recv_qty_arr[$contruction]+=$color_total_recv_qty;
			                	}
			                	foreach ($contru_arr as $key => $contruction)
			                	{//color total Finish Req. & Net. Issued qty
			                		$color_total_issue_qty = $color_total_issue_arr[$contruction];
			                		?>
			                		<td  align="right"><? echo number_format($color_total_issue_qty,2,'.',''); ?></td>
			                		<?
			                		$color_balance_issue_qty_arr[$contruction]+=$color_total_issue_qty;
			                		$job_total_issue_qty_arr[$contruction]+=$color_total_issue_qty;
			                	}
			                	foreach ($contru_arr as $key => $contruction)
			                	{//color total Stock In Hand
			                		$color_total_inHand_qty = $color_total_inHand_arr[$contruction];
			                		?>
			                		<td  align="right"><? echo number_format($color_total_inHand_qty,2,'.',''); ?></td>
			                		<?
			                		$color_balance_inHand_qty_arr[$contruction]+=$color_total_inHand_qty;
			                		$job_total_inHand_qty_arr[$contruction]+=$color_total_inHand_qty;
			                	}
			                	?>
		                    </tr>

							<tr class="tbl_bottom">
								<td colspan="5"><strong>Color Total Balance</strong></td>
		                     	<?
		                        foreach ($contru_arr as $key => $contruction)
			                	{//color Balance Grey Req. & Batch Qty 
			                		$color_batch_qty=$color_balance_batch_qty_arr[$contruction];
			                		$color_fab_greyQty_qty=$fab_greyQty_color_bal_arr[$contruction];
			                		$color_total_balance_batch_qty=$color_fab_greyQty_qty-$color_batch_qty;
			                		?>
			                		<td  align="right"><? echo number_format($color_total_balance_batch_qty,2,'.',''); ?></td>
			                		<?
			                		$job_total_bal_batch_qty_arr[$contruction]+=$color_total_balance_batch_qty;
			                	}
			                	foreach ($contru_arr as $key => $contruction)
			                	{//color Balance Finish Req. & QC Pass Qty
			                		$color_qc_pass_qty=$color_balance_qc_pass_qty_arr[$contruction];
			                		$color_fab_finQty_qty=$fab_finQty_color_bal_arr[$contruction];
			                		$color_total_balance_qc_pass_qty=$color_fab_finQty_qty-$color_qc_pass_qty;
			                		?>
			                		<td  align="right"><? echo number_format($color_total_balance_qc_pass_qty,2,'.',''); ?></td>
			                		<?
			                		$job_total_bal_qc_pass_qty_arr[$contruction]+=$color_total_balance_qc_pass_qty;
			                	}
			                	foreach ($contru_arr as $key => $contruction)
			                	{//color Balance Process Loss
			                		?>
			                		<td  align="right"></td>
			                		<?
			                	}
			                	foreach ($contru_arr as $key => $contruction)
			                	{//color Balance Finish Req. & Delivery to Store
			                		$color_delivery_qty=$color_balance_delivery_qty_arr[$contruction];
			                		$color_fab_finQty_qty=$fab_finQty_color_bal_arr[$contruction];
			                		$color_total_balance_delivery_qty=$color_fab_finQty_qty-$color_delivery_qty;
			                		?>
			                		<td  align="right"><? echo number_format($color_total_balance_delivery_qty,2,'.',''); ?></td>
			                		<?
			                		$job_total_bal_delivery_qty_arr[$contruction]+=$color_total_balance_delivery_qty;
			                	}
			                	foreach ($contru_arr as $key => $contruction)
			                	{//color Balance Finish Req. & Received
			                		$color_recv_qty=$color_balance_recv_qty_arr[$contruction];
			                		$color_fab_finQty_qty=$fab_finQty_color_bal_arr[$contruction];
			                		$color_total_balance_recv_qty=$color_fab_finQty_qty-$color_recv_qty;
			                		?>
			                		<td  align="right"><? echo number_format($color_total_balance_recv_qty,2,'.',''); ?></td>
			                		<?
			                		$job_total_bal_recv_qty_arr[$contruction]+=$color_total_balance_recv_qty;
			                	}
			                	foreach ($contru_arr as $key => $contruction)
			                	{//color Balance Finish Req. & Net. Issued qty
			                		$color_issue_qty=$color_balance_issue_qty_arr[$contruction];
			                		$color_fab_finQty_qty=$fab_finQty_color_bal_arr[$contruction];
			                		$color_total_balance_issue_qty=$color_fab_finQty_qty-$color_issue_qty;
			                		?>
			                		<td  align="right"><? echo number_format($color_total_balance_issue_qty,2,'.',''); ?></td>
			                		<?
			                		$job_total_bal_issue_qty_arr[$contruction]+=$color_total_balance_issue_qty;
			                	}
			                	foreach ($contru_arr as $key => $contruction)
			                	{//color Balance Stock In Hand
			                		$color_inHand_qty=$color_balance_inHand_qty_arr[$contruction];
			                		$color_fab_finQty_qty=$fab_finQty_color_bal_arr[$contruction];
			                		$color_total_balance_inHand_qty=$color_fab_finQty_qty-$color_inHand_qty;
			                		?>
			                		<td  align="right"><? echo number_format($color_total_balance_inHand_qty,2,'.',''); ?></td>
			                		<?
			                		$job_total_bal_inHand_qty_arr[$contruction]+=$color_total_balance_inHand_qty;
			                	}
			                	?>
					        </tr>
		                    <?
		                }// job total below
		                ?>
				        <tr class="tbl_bottom">
	                        <td colspan="5"><strong>Job Total</strong></td>
	                        <?
	                        foreach ($contru_arr as $key => $contruction)
		                	{//Job Total Grey Req. & Batch Qty
		                		$job_total_batch_qty=$job_total_batch_qty_arr[$contruction];
		                		?>
		                		<td  align="right"><? echo number_format($job_total_batch_qty,2,'.',''); ?></td>
		                		<?
		                		$job_process_loss_total_batch_qty_arr[$contruction]+=$job_total_batch_qty;
		                	}
		                	foreach ($contru_arr as $key => $contruction)
		                	{//Job Total Finish Req. & QC Pass Qty
		                		$job_total_qc_pass_qty=$job_total_qc_pass_qty_arr[$contruction];
		                		?>
		                		<td  align="right"><? echo number_format($job_total_qc_pass_qty,2,'.',''); ?></td>
		                		<?
		                		$job_process_loss_total_qcPass_qty_arr[$contruction]+=$job_total_qc_pass_qty;
		                	}
		                	foreach ($contru_arr as $key => $contruction)
		                	{//Job Total Process Loss
		                		$job_process_loss_total_batch_qty=$job_process_loss_total_batch_qty_arr[$contruction];
		                		$job_process_loss_total_qcPass_qty=$job_process_loss_total_qcPass_qty_arr[$contruction];
		                		if ($job_process_loss_total_batch_qty>0) 
		                		{
		                			$job_process_loss_total=($job_process_loss_total_batch_qty-$job_process_loss_total_qcPass_qty)/$job_process_loss_total_qcPass_qty*100;
		                		}
		                		else
		                		{
		                			$job_process_loss_total=0;
		                		}
		                		
		                		?>
		                		<td  align="right"><? echo number_format($job_process_loss_total,2,'.',''); ?>%</td>
		                		<?
		                	}
		                	foreach ($contru_arr as $key => $contruction)
		                	{//Job Total Finish Req. & Delivery to Store
		                		$job_total_delivery_qty=$job_total_delivery_qty_arr[$contruction];
		                		?>
		                		<td  align="right"><? echo number_format($job_total_delivery_qty,2,'.',''); ?></td>
		                		<?
		                	}
		                	foreach ($contru_arr as $key => $contruction)
		                	{//Job Total Finish Req. & Received
		                		$job_total_recv_qty=$job_total_recv_qty_arr[$contruction];
		                		?>
		                		<td  align="right"><? echo number_format($job_total_recv_qty,2,'.',''); ?></td>
		                		<?
		                	}
		                	foreach ($contru_arr as $key => $contruction)
		                	{//Job Total Finish Req. & Net. Issued qty
		                		$job_total_issue_qty=$job_total_issue_qty_arr[$contruction];
		                		?>
		                		<td  align="right"><? echo number_format($job_total_issue_qty,2,'.',''); ?></td>
		                		<?
		                	}
		                	foreach ($contru_arr as $key => $contruction)
		                	{//Job Total Stock In Hand
		                		$job_total_inHand_qty=$job_total_inHand_qty_arr[$contruction];
		                		?>
		                		<td  align="right"><? echo number_format($job_total_inHand_qty,2,'.',''); ?></td>
		                		<?
		                	}
		                	?>
	                    </tr>
	                    <tr class="tbl_bottom">
	                        <td colspan="5"><strong>Job Total Balance</strong></td>
	                        <?
	                        foreach ($contru_arr as $key => $contruction)
		                	{//Job Balance Grey Req. & Batch Qty
		                		$job_balance_total_batch_qty=$job_total_bal_batch_qty_arr[$contruction];
		                		?>
		                		<td  align="right"><? echo number_format($job_balance_total_batch_qty,2,'.',''); ?></td>
		                		<?
		                		$job_bal_process_loss_total_batch_qty_arr[$contruction]+=$job_balance_total_batch_qty;
		                	}
		                	foreach ($contru_arr as $key => $contruction)
		                	{//Job Balance Finish Req. & QC Pass Qty
		                		$job_balance_total_qc_pass_qty=$job_total_bal_qc_pass_qty_arr[$contruction];
		                		?>
		                		<td  align="right"><? echo number_format($job_balance_total_qc_pass_qty,2,'.',''); ?></td>
		                		<?
		                		$job_bal_process_loss_total_qcPass_qty_arr[$contruction]+=$job_balance_total_qc_pass_qty;
		                	}
		                	foreach ($contru_arr as $key => $contruction)
		                	{//Job Balance Process Loss
		                		$job_bal_process_loss_total_batch_qty=$job_bal_process_loss_total_batch_qty_arr[$contruction];
		                		$job_bal_process_loss_total_qcPass_qty=$job_bal_process_loss_total_qcPass_qty_arr[$contruction];
		                		if ($job_bal_process_loss_total_batch_qty>0) 
		                		{
		                			$job_bal_process_loss_total=($job_bal_process_loss_total_batch_qty-$job_bal_process_loss_total_qcPass_qty)/$job_bal_process_loss_total_qcPass_qty*100;
		                		}
		                		else{
		                			$job_bal_process_loss_total=0;
		                		}
		                		
		                		?>
		                		<td  align="right" title="(<? echo $job_bal_process_loss_total_batch_qty.'-'.$job_bal_process_loss_total_qcPass_qty.')/'.$job_bal_process_loss_total_qcPass_qty; ?>"><? echo number_format($job_bal_process_loss_total,2,'.',''); ?>%</td>
		                		<?
		                	}
		                	foreach ($contru_arr as $key => $contruction)
		                	{//Job Balance Finish Req. & Delivery to Store
		                		$job_balance_total_delivery_qty=$job_total_bal_delivery_qty_arr[$contruction];
		                		?>
		                		<td  align="right"><? echo number_format($job_balance_total_delivery_qty,2,'.',''); ?></td>
		                		<?
		                	}
		                	foreach ($contru_arr as $key => $contruction)
		                	{//Job Balance Finish Req. & Received
		                		$job_balance_total_recv_qty=$job_total_bal_recv_qty_arr[$contruction];
		                		?>
		                		<td  align="right"><? echo number_format($job_balance_total_recv_qty,2,'.',''); ?></td>
		                		<?
		                	}
		                	foreach ($contru_arr as $key => $contruction)
		                	{//Job Balance Finish Req. & Net. Issued qty
		                		$job_balance_total_issue_qty=$job_total_bal_issue_qty_arr[$contruction];
		                		?>
		                		<td  align="right"><? echo number_format($job_balance_total_issue_qty,2,'.',''); ?></td>
		                		<?
		                	}
		                	foreach ($contru_arr as $key => $contruction)
		                	{//Job Balance Stock In Hand
		                		$job_balance_total_inHand_qty=$job_total_bal_inHand_qty_arr[$contruction];
		                		?>
		                		<td  align="right"><? echo number_format($job_balance_total_inHand_qty,2,'.',''); ?></td>
		                		<?
		                	}
		                	?>
	                    </tr>
		                <?
					}
					?>
				</table> 
			</div>
		</fieldset>
		<?
		
		foreach (glob("$user_id*.xls") as $filename) 
		{
			if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc,ob_get_contents());
		$filename=$user_id."_".$name.".xls";
		echo "$total_data####$filename";
		
		disconnect($con);
		exit();
	}  // Show 2 End
}
?>
