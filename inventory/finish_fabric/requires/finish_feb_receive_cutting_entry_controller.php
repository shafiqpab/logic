
<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
//print_r($color_arr);die;
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if($db_type==0) $year_cond="YEAR(insert_date)"; 
		else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
		else $year_cond="";//defined Later
		//$new_mrr_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'GRRB', date("Y",time()), 5, "select recv_number_prefix, recv_number_prefix_num from  inv_receive_mas_batchroll where company_id=$cbo_company_id and entry_form=62 and $year_cond=".date('Y',time())." order by id desc ", "recv_number_prefix","recv_number_prefix_num"));
		
		//$id=return_next_id( "id", "inv_receive_mas_batchroll", 1 ) ;
		
		$id = return_next_id_by_sequence("INV_RCV_MAS_BATC_PK_SEQ", "inv_receive_mas_batchroll", $con);
        //print_r($id); die;
		$new_mrr_number = explode("*", return_next_id_by_sequence("INV_RCV_MAS_BATC_PK_SEQ", "inv_receive_mas_batchroll",$con,1,$cbo_company_id,'AOP',62,date("Y",time()),13 ));
				 
		$field_array="id,recv_number_prefix,recv_number_prefix_num 	,recv_number,entry_form,receive_date,company_id,dyeing_source,dyeing_company,challan_no,batch_no,receive_basis,inserted_by,insert_date";
		$data_array="(".$id.",'".$new_mrr_number[1]."',".$new_mrr_number[2].",'".$new_mrr_number[0]."',62,".$txt_delivery_date.",".$cbo_company_id.",".$cbo_knitting_source.",".$cbo_knitting_company.",".$txt_challan_no.",'".str_replace($txt_batch_no)."',".$cbo_basis.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		$field_array_dtls="id, mst_id,roll_id,knitting_source,knitting_company,booking_no,receive_basis,prod_id,body_part_id,febric_description_id,gsm,width,
		roll_wgt,buyer_id,job_no, order_id,color_id, inserted_by, insert_date";
		$field_array_roll="id, mst_id, dtls_id, po_breakdown_id, entry_form,roll_id, roll_no,barcode_no, inserted_by, insert_date";
		//$dtls_id = return_next_id( "id", "pro_grey_batch_dtls", 1 );
		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		for($j=1;$j<=$tot_row;$j++)
		{ 	
		   $activeId="activeId_".$j;
		   if($$activeId==1)
		   {
			$rollId="rollId_".$j;
			$buyerId="buyerId_".$j;
			$bodyPart="bodyPart_".$j;
			$colorId="colorId_".$j;
			$deterId="deterId_".$j;
			$productId="productId_".$j;
			$orderId="orderId_".$j;
			$rollGsm="rollGsm_".$j;
			$knittingSource="knittingSource_".$j;
			$knittingComp="knittingComp_".$j;
			$fabricId="fabricId_".$j;
			$receiveBasis="receiveBasis_".$j;
			$job_no="job_no_".$j;
			$rollwgt="rollwgt_".$j;
			$rolldia="rolldia_".$j;
			$bookingNo="bookingNo_".$j;
			$barcodeNo="barcodeNo_".$j;
			$rollNo="rollNo_".$j;
			
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
			$dtls_id = return_next_id_by_sequence("PRO_GREY_BATCH_DTLS_PK_SEQ", "pro_grey_batch_dtls", $con);
			
			if($data_array_roll!="") $data_array_roll.= ",";
			$data_array_roll.="(".$id_roll.",".$id.",".$dtls_id.",'".$$orderId."',62,'".$$rollId."','".$$rollNo."','".$$barcodeNo."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$dtls_id.",".$id.",".$$rollId.",'".$$knittingSource."','".$$knittingComp."','".$$bookingNo."','".$$receiveBasis."',
			'".$$productId."','".$$bodyPart."','".$$deterId."','".$$rollGsm."','".$$rolldia."','".$$rollwgt."','".$$buyerId."','".$$job_no."',
			'".$$orderId."','".$$colorId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			//$id_roll=$id_roll+1;
			//$dtls_id = $dtls_id+1;
		   }
		}


		//echo "10**insert into inv_receive_mas_batchroll (".$field_array.") values ".$data_array;die;
		$rID=sql_insert("inv_receive_mas_batchroll",$field_array,$data_array,0);
		$rID2=sql_insert(" pro_roll_details",$field_array_roll,$data_array_roll,0);
		$rID3=sql_insert("pro_grey_batch_dtls",$field_array_dtls,$data_array_dtls,1);
		//echo "10**".$rID."&&".$rID2;die;

		if($db_type==0)
		{
			if($rID && $rID2 && $rID3)
			{
				mysql_query("COMMIT");  
				echo "0**".$id."**".$new_mrr_number[0];
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con);  
				echo "0**".$id."**".$new_mrr_number[0]."**".substr($barcodeNos,0,-1);
			}
			else
			{
				oci_rollback($con);
				echo "5**0**0";
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
		$field_array="receive_date*batch_no*updated_by*update_date";
		$data_array="".$txt_delivery_date."*'".str_replace($txt_batch_no)."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		//$dtls_id = return_next_id( "id", "pro_grey_batch_dtls", 1 );
		$field_array_dtls="updated_by*update_date*status_active*is_deleted";
		$field_array_roll="id, mst_id, dtls_id, po_breakdown_id, entry_form,roll_id, roll_no,barcode_no, inserted_by, insert_date";
		$field_array_insert="id,mst_id,roll_id,knitting_source,knitting_company,booking_no,receive_basis,prod_id,body_part_id,
		febric_description_id,gsm,width,
		roll_wgt,buyer_id,job_no, order_id,color_id, inserted_by, insert_date";
		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		//$dtls_id = return_next_id( "id", "pro_grey_batch_dtls", 1 );
		$barcodeNos='';
		for($j=1;$j<=$tot_row;$j++)
		{ 
		
		    $activeId="activeId_".$j;
			$updateDetailsId="updateDetailsId_".$j;
			if($$activeId==0 )
			{
				if($$updateDetailsId!="")
				{
				$updateDetailsId_arr[]=$$updateDetailsId;
				//$remove_detls_arr[$$updateDetailsId]=explode("*",("".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1"));
				}
			}
			
		   if($$activeId==1 && $$updateDetailsId==0)
			{
			$rollId="rollId_".$j;
			$buyerId="buyerId_".$j;
			$bodyPart="bodyPart_".$j;
			$colorId="colorId_".$j;
			$deterId="deterId_".$j;
			$productId="productId_".$j;
			$orderId="orderId_".$j;
			$rollGsm="rollGsm_".$j;
			$knittingSource="knittingSource_".$j;
			$knittingComp="knittingComp_".$j;
			$fabricId="fabricId_".$j;
			$receiveBasis="receiveBasis_".$j;
			$job_no="job_no_".$j;
			$rollwgt="rollwgt_".$j;
			$rolldia="rolldia_".$j;
			$bookingNo="bookingNo_".$j;
			$barcodeNo="barcodeNo_".$j;
			$rollNo="rollNo_".$j;
			
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
			$dtls_id = return_next_id_by_sequence("PRO_GREY_BATCH_DTLS_PK_SEQ", "pro_grey_batch_dtls", $con);
			
			if($data_array_roll!="") $data_array_roll.= ",";
			$data_array_roll.="(".$id_roll.",".$update_id.",".$dtls_id.",'".$$orderId."',62,'".$$rollId."','".$$rollNo."','".$$barcodeNo."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$dtls_id.",".$update_id.",".$$rollId.",'".$$knittingSource."','".$$knittingComp."','".$$bookingNo."','".$$receiveBasis."',
			'".$$productId."','".$$bodyPart."','".$$deterId."','".$$rollGsm."','".$$rolldia."','".$$rollwgt."','".$$buyerId."','".$$job_no."',
			'".$$orderId."','".$$colorId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			//$id_roll=$id_roll+1;
			//$dtls_id = $dtls_id+1;
			
			}
		}
		
		$flag=0;
		$rID=sql_update("inv_receive_mas_batchroll",$field_array,$data_array,"id",$update_id,0);
		if($rID) $flag=1; else $flag=50;
		$rID2=true; $rID3=true; $statusChange=true;
		if(count($updateDetailsId_arr)>0)
		{
			$rID2=execute_query("delete from pro_grey_batch_dtls where id in (".implode(",",$updateDetailsId_arr).")");
			if($flag==1) { if($rID2) $flag=1; else $flag=10; }
			$rID3=execute_query("delete from pro_roll_details where dtls_id in (".implode(",",$updateDetailsId_arr).") and entry_form=62");
			if($flag==1) { if($rID3) $flag=1; else $flag=20; }
		}

		if($data_array_roll!="")
		{
		$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
		if($flag==1) { if($rID4) $flag=1; else $flag=30; }
		}
		if($data_array_dtls!="")
		{
		$rID5=sql_insert("pro_grey_batch_dtls",$field_array_insert,$data_array_dtls,1);
		if($flag==1) { if($rID5) $flag=1; else $flag=40; }
		}
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_no);
			}
			else
			{
				oci_rollback($con);
				echo "5**0**0";
			}
		}
		disconnect($con);
		die;
	}
}


if($action=="grey_item_details_update")
{
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$company_name_array=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$data_array=sql_select("SELECT a.job_no_prefix_num,a.job_no, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst");
	$po_details_array=array();
	foreach($data_array as $row)
	{
		$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no_prefix_num")];
		$po_details_array[$row[csf("po_id")]]['job_no_full']=$row[csf("job_no")];
		$po_details_array[$row[csf("po_id")]]['buyer_name']=$buyer_name_array[$row[csf("buyer_name")]];
		$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
		$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
		$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
		$po_details_array[$row[csf("po_id")]]['buyer_id']=$row[csf("buyer_name")];
	}
	
	$composition_arr=array(); $constructtion_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
/*	$inserted_roll=sql_select("select roll_id from pro_grey_batch_dtls a,inv_receive_mas_batchroll b where a.mst_id=b.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 ");
	$inserted_roll_arr=array();
	foreach($inserted_roll as $value)
	{
		$inserted_roll_arr[]=$value[csf('roll_id')];
	}*/
	// echo $roll_cond;die;
   $data_array=sql_select("SELECT  a.id, a.company_id, a.recv_number,a.booking_no, a.receive_basis, a.receive_date, a.booking_no, a.knitting_source, a.knitting_company,b.id as dtls_id, b.prod_id, b.febric_description_id, b.gsm, c.qnty,b.width,b.body_part_id,b.yarn_lot,b.brand_id,b.shift_name,b.floor_id,b.machine_no_id,b.yarn_count,b.color_id,b.color_range_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id FROM inv_receive_master a,pro_grey_prod_entry_dtls b, pro_roll_details c WHERE  a.id=b.mst_id and b.id=c.dtls_id and  c.entry_form in (2,22)  and c.status_active=1 and c.is_deleted=0 ");
	
	$roll_details_array=array(); 
	foreach($data_array as $row)
	{
		$roll_details_array[$row[csf("roll_id")]]['body_part_id']=$row[csf("body_part_id")];
		$roll_details_array[$row[csf("roll_id")]]['roll_id']=$row[csf("roll_id")];
		$roll_details_array[$row[csf("roll_id")]]['roll_no']=$row[csf("roll_no")];
		$roll_details_array[$row[csf("roll_id")]]['deter_id']=$row[csf("febric_description_id")];
		$roll_details_array[$row[csf("roll_id")]]['gsm']=$row[csf("gsm")];
		$roll_details_array[$row[csf("roll_id")]]['width']=$row[csf("width")];
		$roll_details_array[$row[csf("roll_id")]]['color_id']=$row[csf("color_id")];
		$roll_details_array[$row[csf("roll_id")]]['color_range_id']=$row[csf("color_range_id")];
		$roll_details_array[$row[csf("roll_id")]]['qnty']=number_format($row[csf("qnty")],2,'.','');
		$roll_details_array[$row[csf("roll_id")]]['receive_basis']=$row[csf("receive_basis")];
		$roll_details_array[$row[csf("roll_id")]]['barcode_no']=$row[csf("barcode_no")];
		$roll_details_array[$row[csf("roll_id")]]['booking_no']=$row[csf("booking_no")];
		$roll_details_array[$row[csf("roll_id")]]['knitting_source_id']=$row[csf("knitting_source")];
		$roll_details_array[$row[csf("roll_id")]]['knitting_source']=$row[csf("knitting_source")];
		$roll_details_array[$row[csf("roll_id")]]['knitting_company_id']=$row[csf("knitting_company")];
		$roll_details_array[$row[csf("roll_id")]]['po_breakdown_id']=$row[csf("po_breakdown_id")];
		$roll_details_array[$row[csf("roll_id")]]['prod_id']=$row[csf("prod_id")];
		$roll_details_array[$row[csf("roll_id")]]['dtls_id']=$row[csf("dtls_id")];
		if($row[csf("knitting_source")]==1)
		{
		$roll_details_array[$row[csf("roll_id")]]['knitting_company']=$company_name_array[$row[csf("knitting_company")]];
		}
		else if($row[csf("knitting_source")]==3)
		{
		$roll_details_array[$row[csf("roll_id")]]['knitting_company']=$supplier_arr[$row[csf("knitting_company")]];
		}

	}
       $sql_update=sql_select("select  b.id,b.knitting_company,b.knitting_source,booking_no,b.receive_basis,b.prod_id,b.body_part_id,
	   b.febric_description_id,b.gsm,b.width,b.roll_wgt,b.roll_id,b.buyer_id,b.order_id,b.color_id,a.challan_no
	   from pro_grey_batch_dtls b,inv_receive_mas_batchroll a
	   where a.id=b.mst_id and a.id=$data and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0");
	 
	
        $issue_details_arr=array();
		$j=1;
 		foreach($sql_update as $val)
		{
          $inserted_roll_arr[]=$val[csf('roll_id')];
			?>
          			 
                          <tr id="tr_1" align="center" valign="middle">
                                <td width="60" id="sl_<? echo $j; ?>"><input type="checkbox" id="checkRow_<? echo $j; ?>" name="checkRow[]" checked="checked"> &nbsp; &nbsp;<? echo $j; ?></td>
                                <td width="80" id="barcode_<? echo $j; ?>"> <? echo $roll_details_array[$val[csf('roll_id')]]['roll_no']; ?></td>
                                <td width="100" id="systemId_<? echo $j; ?>"> <? echo $body_part[$val[csf('body_part_id')]]; ?></td>
                                <td width="100" id="progBookId_<? echo $j; ?>"> <? echo $composition_arr[$val[csf('febric_description_id')]]; ?></td>
                                <td width="75" id="basis_<? echo $j; ?>"> <? echo $val[csf('gsm')]; ?></td>
                                <td width="75" id="knitSource_<? echo $j; ?>"> <? echo $val[csf('width')]; ?></td>
                                <td width="70" id="prodDate_<? echo $j; ?>"> <? echo $color_arr[$val[csf('color_id')]]; ?></td>
                                <td width="70" id="prodId_<? echo $j; ?>"><? //echo $val[csf('id')]; ?></td>
                                <td width="70" id="rollWgt_<? echo $j; ?>"><? echo $val[csf('roll_wgt')]; ?></td>  
                                <td width="50" id="job_<? echo $j; ?>"><? echo $po_details_array[$val[csf('order_id')]]['job_no']; ?></td>
                                <td width="50" id="year_<? echo $j; ?>" align="center"><? echo $po_details_array[$val[csf('order_id')]]['year']; ?></td>
                                <td width="65" id="buyer_<? echo $j; ?>"><? echo $po_details_array[$val[csf('order_id')]]['buyer_name']; ?></td>
                                <td width="80" id="order_<? echo $j; ?>" style="word-break:break-all;" align="center"><? echo $po_details_array[$val[csf('order_id')]]['po_number']; ?></td>
                                <td width="80" id="cons_<? echo $j; ?>" style="word-break:break-all;" align="left">
								<? 
								if($val[csf("knitting_source")]==1)
								{
								$knitting_com=$company_name_array[$val[csf("knitting_company")]];
								}
								else if($val[csf("knitting_source")]==3)
								{
								$knitting_com=$supplier_arr[$val[csf("knitting_company")]];
								}
								echo $knitting_com; 
								?></td>
                                <td width="100" id="comps_<? echo $j; ?>" style="word-break:break-all;" align="left"><? echo $val[csf("booking_no")]; ?></td>
                                <td width="100" id="gsm_<? echo $j; ?>">
								
								<? 
								 $receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
								 echo $receive_basis[$val[csf("receive_basis")]]; ?> 
                <input type="hidden" name="updateDetaisId[]" id="updateDetaisId_<? echo $j; ?>" value="<? echo $val[csf('id')]; ?>" />                          
                <input type="hidden" name="rollId[]" id="rollId_<? echo $j; ?>" value="<? echo $val[csf('roll_id')]; ?>" />
                <input type="hidden" name="bodyPartId[]" id="bodyPartId_<? echo $j; ?>" value="<? echo $val[csf("body_part_id")]; ?>"/>
                <input type="hidden" name="colorId[]" id="colorId_<? echo $j; ?>" value="<? echo $val[csf("color_id")]; ?>" />
                <input type="hidden" name="deterId[]" id="deterId_<? echo $j; ?>" value="<? echo $val[csf("febric_description_id")]; ?>"/>
                <input type="hidden" name="productId[]" id="productId_<? echo $j; ?>" value="<? echo $val[csf("prod_id")]; ?>" />
                <input type="hidden" name="orderId[]" id="orderId_<? echo $j; ?>" value="<? echo $val[csf("order_id")]; ?>" />
                <input type="hidden" name="buyerId[]" id="buyerId_<? echo $j; ?>" value="<? echo $po_details_array[$val[csf("order_id")]]['buyer_id']; ?>"/>  
                <input type="hidden" name="rolWgt[]" id="rolWgt_<? echo $j; ?>" value="<? echo $val[csf("roll_wgt")]; ?>"/>
                <input type="hidden" name="rollDia[]" id="rollDia_<? echo $j; ?>" value="<? echo $val[csf("width")]; ?>"/>
                <input type="hidden" name="rollGsm[]" id="rollGsm_<? echo $j; ?>" value="<? echo $val[csf("gsm")]; ?>"/>
                <input type="hidden" name="fabricId[]" id="fabricId_<? echo $j; ?>" value="<? echo $val[csf("febric_description_id")]; ?>"/>
                <input type="hidden" name="receiveBasis[]" id="receiveBasis_<? echo $j; ?>" value="<? echo $val[csf("receive_basis")]; ?>"/>
                <input type="hidden" name="knittingSource[]" id="knittingSource_<? echo $j; ?>" value="<? echo $val[csf("knitting_source")]; ?>"/>
                <input type="hidden" name="knittingComp[]" id="knittingComp_<? echo $j; ?>" value="<? echo $val[csf("knitting_company")]; ?>"/>
                <input type="hidden" name="jobNo[]" id="jobNo_<? echo $j; ?>" value="<? echo $po_details_array[$val[csf("order_id")]]['job_no_full']; ?>"/>
                 <input type="hidden" name="bookingNo[]" id="bookingNo_<? echo $j; ?>" value="<? echo $val[csf('booking_no')]; ?>"/>
                 <input type="hidden" name="barcodNumber[]" id="barcodNumber_<? echo $j; ?>" value="<? echo $val[csf('barcode_no')]; ?>"/>
   </td> 
       </tr>
        <?
		$j++;
		}
	     if($inserted_roll_arr!="") $roll_cond=" and c.roll_id not in (".implode(",",$inserted_roll_arr).") ";
		
		$sql = sql_select("select  b.id, a.issue_number, a.challan_no, a.order_id, a.issue_purpose, b.trans_id, b.distribution_method,
		 b.program_no, b.no_of_roll, b.roll_no, b.roll_po_id, b.roll_wise_issue_qnty, b.prod_id, b.issue_qnty, b.color_id, b.yarn_lot,
		 b.yarn_count, b.store_name, b.rack, b.self, b.stitch_length, b.remarks,c.roll_id,c.barcode_no,c.roll_no
		 from inv_issue_master a, inv_grey_fabric_issue_dtls b, pro_roll_details c
		 where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=61 and c.entry_form=61 and  a.issue_number='".$val[csf('challan_no')]."' $roll_cond ");
		if(	count($sql)>0)
		{
 		foreach($sql as $inf)
		{
			
			
			?>
          			 
                          <tr id="tr_1" align="center" valign="middle">
                                <td width="60" id="sl_<? echo $j; ?>"><input type="checkbox" id="checkRow_<? echo $j; ?>" name="checkRow[]" > &nbsp; &nbsp;<? echo $j; ?></td>
                                <td width="80" id="barcode_<? echo $j; ?>"> <? echo $roll_details_array[$inf[csf('roll_id')]]['roll_no']; ?></td>
                                <td width="100" id="systemId_<? echo $j; ?>"> <? echo $body_part[$roll_details_array[$inf[csf('roll_id')]]['body_part_id']]; ?></td>
                                <td width="100" id="progBookId_<? echo $j; ?>"> <? echo $composition_arr[$roll_details_array[$inf[csf('roll_id')]]['deter_id']]; ?></td>
                                <td width="75" id="basis_<? echo $j; ?>"> <? echo $roll_details_array[$inf[csf('roll_id')]]['gsm']; ?></td>
                                <td width="75" id="knitSource_<? echo $j; ?>"> <? echo $roll_details_array[$inf[csf('roll_id')]]['width']; ?></td>
                                <td width="70" id="prodDate_<? echo $j; ?>"> <? echo $color_arr[$roll_details_array[$inf[csf('roll_id')]]['color_id']]; ?></td>
                                <td width="70" id="prodId_<? echo $j; ?>"><? //echo $inf[csf('id')]; ?></td>
                                <td width="70" id="rollWgt_<? echo $j; ?>"><? echo $roll_details_array[$inf[csf('roll_id')]]['qnty']; ?></td>  
                                
                                <td width="50" id="job_<? echo $j; ?>"><? echo $po_details_array[$roll_details_array[$inf[csf('roll_id')]]['po_breakdown_id']]['job_no']; ?></td>
                                <td width="50" id="year_<? echo $j; ?>" align="center"><? echo $po_details_array[$roll_details_array[$inf[csf('roll_id')]]['po_breakdown_id']]['year']; ?></td>
                                <td width="65" id="buyer_<? echo $j; ?>"><? echo $po_details_array[$roll_details_array[$inf[csf('roll_id')]]['po_breakdown_id']]['buyer_name']; ?></td>
                                <td width="80" id="order_<? echo $j; ?>" style="word-break:break-all;" align="left"><? echo $po_details_array[$roll_details_array[$inf[csf('roll_id')]]['po_breakdown_id']]['po_number']; ?></td>
                                <td width="80" id="cons_<? echo $j; ?>" style="word-break:break-all;" align="left"><? echo $roll_details_array[$inf[csf('roll_id')]]['knitting_company']; ?></td>
                                <td width="100" id="comps_<? echo $j; ?>" style="word-break:break-all;" align="left"><? echo $roll_details_array[$inf[csf('roll_id')]]['booking_no']; ?></td>
                                <td width="100" id="gsm_<? echo $j; ?>">
								
								<? 
								 $receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
								 echo $receive_basis[$roll_details_array[$inf[csf('roll_id')]]['receive_basis']]; ?></td>  
    <input type="hidden" name="updateDetaisId[]" id="updateDetaisId_<? echo $j; ?>" value="0" />                          
    <input type="hidden" name="rollId[]" id="rollId_<? echo $j; ?>" value="<? echo $inf[csf('roll_id')]; ?>" />
    <input type="hidden" name="rollNo[]" id="rollNo_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('roll_id')]]['roll_no']; ?>" />
    <input type="hidden" name="bodyPartId[]" id="bodyPartId_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('roll_id')]]['body_part_id']; ?>"/>
    <input type="hidden" name="colorId[]" id="colorId_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('roll_id')]]['color_id']; ?>" />
    <input type="hidden" name="deterId[]" id="deterId_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('roll_id')]]['deter_id']; ?>"/>
    <input type="hidden" name="productId[]" id="productId_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('roll_id')]]['prod_id']; ?>" />
    <input type="hidden" name="orderId[]" id="orderId_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('roll_id')]]['po_breakdown_id']; ?>" />
    <input type="hidden" name="buyerId[]" id="buyerId_<? echo $j; ?>" value="<? echo $po_details_array[$roll_details_array[$inf[csf('roll_id')]]['po_breakdown_id']]['buyer_id']; ?>"/>  
    <input type="hidden" name="rolWgt[]" id="rolWgt_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('roll_id')]]['qnty']; ?>"/>
    <input type="hidden" name="rollDia[]" id="rollDia_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('roll_id')]]['width']; ?>"/>
    <input type="hidden" name="rollGsm[]" id="rollDia_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('roll_id')]]['gsm']; ?>"/>
    <input type="hidden" name="fabricId[]" id="rollGsm_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('roll_id')]]['dtls_id']; ?>"/>
    <input type="hidden" name="receiveBasis[]" id="receiveBasis_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('roll_id')]]['receive_basis']; ?>"/>
    <input type="hidden" name="knittingSource[]" id="knittingSource_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('roll_id')]]['knitting_source']; ?>"/>
    <input type="hidden" name="knittingComp[]" id="knittingComp_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('roll_id')]]['knitting_company_id']; ?>"/>
    <input type="hidden" name="jobNo[]" id="jobNo_<? echo $j; ?>" value="<? echo $po_details_array[$roll_details_array[$inf[csf('roll_id')]]['po_breakdown_id']]['job_no_full']; ?>"/>
     <input type="hidden" name="bookingNo[]" id="bookingNo_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('roll_id')]]['booking_no']; ?>"/>
    <input type="hidden" name="barcodNumber[]" id="barcodNumber_<? echo $j; ?>" value="<? echo $inf[csf('barcode_no')]; ?>"/>
          </tr>
            <?
			$j++;
		}
	}
	//print_r($issue_details_arr); buyer_id
}



//need for this
if($action=="grey_item_details")
{
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$company_name_array=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$data_array=sql_select("SELECT a.job_no_prefix_num,a.job_no, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst");
	$po_details_array=array();
	foreach($data_array as $row)
	{
		$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no_prefix_num")];
		$po_details_array[$row[csf("po_id")]]['job_no_full']=$row[csf("job_no")];
		$po_details_array[$row[csf("po_id")]]['buyer_name']=$buyer_name_array[$row[csf("buyer_name")]];
		$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
		$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
		$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
		$po_details_array[$row[csf("po_id")]]['buyer_id']=$row[csf("buyer_name")];
	}
	
	$composition_arr=array(); $constructtion_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

	$inserted_roll=sql_select("select roll_id from pro_grey_batch_dtls a,inv_receive_mas_batchroll b where a.mst_id=b.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.entry_form=62 ");
	$inserted_roll_arr=array();
	foreach($inserted_roll as $inf)
	{
		$inserted_roll_arr[]=$inf[csf('roll_id')];
	}
	
     $data_array=sql_select("SELECT  a.id, a.company_id, a.recv_number,a.booking_no, a.receive_basis, a.receive_date, a.booking_no, a.knitting_source, a.knitting_company,b.id as dtls_id, b.prod_id, b.febric_description_id, b.gsm, c.qnty,b.width,b.body_part_id,b.yarn_lot,b.brand_id,b.shift_name,b.floor_id,b.machine_no_id,b.yarn_count,b.color_id,b.color_range_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id FROM inv_receive_master a,pro_grey_prod_entry_dtls b, pro_roll_details c WHERE  a.id=b.mst_id and b.id=c.dtls_id and  c.entry_form in (2,22)  and c.status_active=1 and c.is_deleted=0 ");
	
	$roll_details_array=array(); 
	foreach($data_array as $row)
	{
		$roll_details_array[$row[csf("roll_id")]]['body_part_id']=$row[csf("body_part_id")];
		$roll_details_array[$row[csf("roll_id")]]['roll_id']=$row[csf("roll_id")];
		$roll_details_array[$row[csf("roll_id")]]['roll_no']=$row[csf("roll_no")];
		$roll_details_array[$row[csf("roll_id")]]['deter_id']=$row[csf("febric_description_id")];
		$roll_details_array[$row[csf("roll_id")]]['gsm']=$row[csf("gsm")];
		$roll_details_array[$row[csf("roll_id")]]['width']=$row[csf("width")];
		$roll_details_array[$row[csf("roll_id")]]['color_id']=$row[csf("color_id")];
		$roll_details_array[$row[csf("roll_id")]]['color_range_id']=$row[csf("color_range_id")];
		$roll_details_array[$row[csf("roll_id")]]['qnty']=number_format($row[csf("qnty")],2,'.','');
		$roll_details_array[$row[csf("roll_id")]]['receive_basis']=$row[csf("receive_basis")];
		//$roll_details_array[$row[csf("roll_id")]]['receive_date']=change_date_format($row[csf("receive_date")]);
		$roll_details_array[$row[csf("roll_id")]]['booking_no']=$row[csf("booking_no")];
		$roll_details_array[$row[csf("roll_id")]]['knitting_source_id']=$row[csf("knitting_source")];
		$roll_details_array[$row[csf("roll_id")]]['knitting_source']=$row[csf("knitting_source")];
		$roll_details_array[$row[csf("roll_id")]]['knitting_company_id']=$row[csf("knitting_company")];
		$roll_details_array[$row[csf("roll_id")]]['po_breakdown_id']=$row[csf("po_breakdown_id")];
		$roll_details_array[$row[csf("roll_id")]]['prod_id']=$row[csf("prod_id")];
		$roll_details_array[$row[csf("roll_id")]]['dtls_id']=$row[csf("dtls_id")];
		if($row[csf("knitting_source")]==1)
		{
		$roll_details_array[$row[csf("roll_id")]]['knitting_company']=$company_name_array[$row[csf("knitting_company")]];
		}
		else if($row[csf("knitting_source")]==3)
		{
		$roll_details_array[$row[csf("roll_id")]]['knitting_company']=$supplier_arr[$row[csf("knitting_company")]];
		}

	}
        if(count($inserted_roll_arr)>0) $roll_cond=" and c.roll_id not in (".implode(",",$inserted_roll_arr).") ";

		$sql = sql_select("select  b.id, a.issue_number, a.challan_no, a.order_id, a.issue_purpose, b.trans_id, b.distribution_method,
		 b.program_no, b.no_of_roll, b.roll_no, b.roll_po_id, b.roll_wise_issue_qnty, b.prod_id, b.issue_qnty, b.color_id, b.yarn_lot,
		 b.yarn_count, b.store_name, b.rack, b.self, b.stitch_length, b.remarks,c.roll_id,c.barcode_no
		 from inv_issue_master a, inv_grey_fabric_issue_dtls b, pro_roll_details c
		 where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=61 and c.entry_form=61 and  a.issue_number='$data' $roll_cond ");
		if(	count($sql)>0)
		{
        $issue_details_arr=array();
		$j=1;
 		foreach($sql as $val)
		{
			
			?>
          			 
                          <tr id="tr_1" align="center" valign="middle">
                                <td width="60" id="sl_<? echo $j; ?>"><input type="checkbox" id="checkRow_<? echo $j; ?>" name="checkRow[]" checked="checked"> &nbsp; &nbsp;<? echo $j; ?></td>
                                <td width="80" id="barcode_<? echo $j; ?>"> <? echo $roll_details_array[$val[csf('roll_id')]]['roll_no']; ?></td>
                                <td width="100" id="systemId_<? echo $j; ?>"> <? echo $body_part[$roll_details_array[$val[csf('roll_id')]]['body_part_id']]; ?></td>
                                <td width="100" id="progBookId_<? echo $j; ?>"> <? echo $composition_arr[$roll_details_array[$val[csf('roll_id')]]['deter_id']]; ?></td>
                                <td width="75" id="basis_<? echo $j; ?>"> <? echo $roll_details_array[$val[csf('roll_id')]]['gsm']; ?></td>
                                <td width="75" id="knitSource_<? echo $j; ?>"> <? echo $roll_details_array[$val[csf('roll_id')]]['width']; ?></td>
                                <td width="70" id="prodDate_<? echo $j; ?>"> <? echo $color_arr[$roll_details_array[$val[csf('roll_id')]]['color_id']]; ?></td>
                                <td width="70" id="prodId_<? echo $j; ?>"><? //echo $val[csf('id')]; ?></td>
                                <td width="70" id="rollWgt_<? echo $j; ?>"><? echo $roll_details_array[$val[csf('roll_id')]]['qnty']; ?></td>  
                                
                                <td width="50" id="job_<? echo $j; ?>"><? echo $po_details_array[$roll_details_array[$val[csf('roll_id')]]['po_breakdown_id']]['job_no']; ?></td>
                                <td width="50" id="year_<? echo $j; ?>" align="center"><? echo $po_details_array[$roll_details_array[$val[csf('roll_id')]]['po_breakdown_id']]['year']; ?></td>
                                <td width="65" id="buyer_<? echo $j; ?>"><? echo $po_details_array[$roll_details_array[$val[csf('roll_id')]]['po_breakdown_id']]['buyer_name']; ?></td>
                                <td width="80" id="order_<? echo $j; ?>" style="word-break:break-all;" align="left"><? echo $po_details_array[$roll_details_array[$val[csf('roll_id')]]['po_breakdown_id']]['po_number']; ?></td>
                                <td width="80" id="cons_<? echo $j; ?>" style="word-break:break-all;" align="left"><? echo $roll_details_array[$val[csf('roll_id')]]['knitting_company']; ?></td>
                                <td width="100" id="comps_<? echo $j; ?>" style="word-break:break-all;" align="left"><? echo $roll_details_array[$val[csf('roll_id')]]['booking_no']; ?></td>
                                <td width="100" id="gsm_<? echo $j; ?>">
								
								<? 
								 $receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
								 echo $receive_basis[$roll_details_array[$val[csf('roll_id')]]['receive_basis']]; ?></td>  
                              
    <input type="hidden" name="rollId[]" id="rollId_<? echo $j; ?>" value="<? echo $val[csf('roll_id')]; ?>" />
    <input type="hidden" name="rollNo[]" id="rollNo_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('roll_id')]]['roll_no']; ?>" />
    <input type="hidden" name="bodyPartId[]" id="bodyPartId_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('roll_id')]]['body_part_id']; ?>"/>
    <input type="hidden" name="colorId[]" id="colorId_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('roll_id')]]['color_id']; ?>" />
    <input type="hidden" name="deterId[]" id="deterId_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('roll_id')]]['deter_id']; ?>"/>
    <input type="hidden" name="productId[]" id="productId_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('roll_id')]]['prod_id']; ?>" />
    <input type="hidden" name="orderId[]" id="orderId_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('roll_id')]]['po_breakdown_id']; ?>" />
    <input type="hidden" name="buyerId[]" id="buyerId_<? echo $j; ?>" value="<? echo $po_details_array[$roll_details_array[$val[csf('roll_id')]]['po_breakdown_id']]['buyer_id']; ?>"/>  
    <input type="hidden" name="rolWgt[]" id="rolWgt_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('roll_id')]]['qnty']; ?>"/>
    <input type="hidden" name="rollDia[]" id="rollDia_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('roll_id')]]['width']; ?>"/>
    <input type="hidden" name="rollGsm[]" id="rollDia_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('roll_id')]]['gsm']; ?>"/>
    <input type="hidden" name="fabricId[]" id="rollGsm_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('roll_id')]]['dtls_id']; ?>"/>
    <input type="hidden" name="receiveBasis[]" id="receiveBasis_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('roll_id')]]['receive_basis']; ?>"/>
    <input type="hidden" name="knittingSource[]" id="knittingSource_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('roll_id')]]['knitting_source']; ?>"/>
    <input type="hidden" name="knittingComp[]" id="knittingComp_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('roll_id')]]['knitting_company_id']; ?>"/>
    <input type="hidden" name="jobNo[]" id="jobNo_<? echo $j; ?>" value="<? echo $po_details_array[$roll_details_array[$val[csf('roll_id')]]['po_breakdown_id']]['job_no_full']; ?>"/>
     <input type="hidden" name="bookingNo[]" id="bookingNo_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('roll_id')]]['booking_no']; ?>"/>
    <input type="hidden" name="barcodNumber[]" id="barcodNumber_<? echo $j; ?>" value="<? echo $val[csf('barcode_no')]; ?>"/>
                               
                            </tr>
                            
                        
            
            <?
			
			$j++;
		}
	}
	else
	{
	 echo " Data Already Saved";	
	}
	//print_r($issue_details_arr); buyer_id
}



if($action=="load_drop_down_knitting_com")
{
	$data = explode("_",$data);
	$company_id=$data[1];
	
	if($data[0]==1)
	{
		echo create_drop_down( "cbo_knitting_company", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "--Select Knit Company--", "$company_id", "",1 );
	}
	else if($data[0]==3)
	{	
		echo create_drop_down( "cbo_knitting_company", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select Knit Company--", 1, "",1 );
	}
	else
	{
		echo create_drop_down( "cbo_knitting_company", 152, $blank_array,"",1, "--Select Knit Company--", 1, "",1 );
	}
	exit();
}

if($action=="load_php_form_update")
{

	$sql=sql_select("select  a.id,a.recv_number_prefix_num,a.recv_number, a.challan_no, a.recv_number,a.company_id,a.batch_no, a.receive_basis,a.dyeing_source,a.dyeing_company,a.receive_date
	from  inv_receive_mas_batchroll a
	where  a.id=$data ");
	//echo $sql;die;
	foreach($sql as $val)
	{
		echo "document.getElementById('cbo_company_id').value  = '".($val[csf("company_id")])."';\n"; 
		echo "document.getElementById('txt_batch_no').value  = '".($val[csf("batch_no")])."';\n"; 
		echo "document.getElementById('cbo_knitting_source').value  = '".($val[csf("dyeing_source")])."';\n"; 
		echo "load_drop_down( 'requires/', '".$val[csf("dyeing_source")]."_".$val[csf("company_id")]."', 'load_drop_down_knitting_com', 'knitting_com');\n"; 
		echo "document.getElementById('cbo_knitting_company').value  = '".($val[csf("dyeing_company")])."';\n";  
		echo "document.getElementById('cbo_basis').value  = '".($val[csf("receive_basis")])."';\n"; 
		echo "document.getElementById('update_id').value  = '".($val[csf("id")])."';\n";
	
	}
}

if($action=="load_php_form")
{
	$sql=sql_select("select  a.issue_number, a.challan_no, a.order_id,a.company_id, a.batch_no,a.issue_purpose,a.issue_basis,a.knit_dye_source,a.knit_dye_company,a.issue_number_prefix_num,a.issue_date
			from inv_issue_master a
			where  a.entry_form=61 and   a.issue_number='$data'  ");
	foreach($sql as $val)
	{
		echo "document.getElementById('cbo_company_id').value  = '".($val[csf("company_id")])."';\n"; 
		echo "document.getElementById('txt_batch_no').value  = '".($val[csf("batch_no")])."';\n"; 
		echo "document.getElementById('cbo_knitting_source').value  = '".($val[csf("knit_dye_source")])."';\n"; 
		echo "load_drop_down( 'requires/', '".$val[csf("knit_dye_source")]."_".$val[csf("company_id")]."', 'load_drop_down_knitting_com', 'knitting_com');\n"; 
		echo "document.getElementById('cbo_knitting_company').value  = '".($val[csf("knit_dye_company")])."';\n";  
		echo "document.getElementById('cbo_basis').value  = '".($val[csf("issue_purpose")])."';\n"; 
		echo "document.getElementById('update_id').value  = '".($val[csf("id")])."';\n";
	
	}
}

if($action=="challan_popup")
{
	echo load_html_head_contents("Challan Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
	
		function js_set_value(data,id)
		{
		
			$('#hidden_challan_no').val(data);
			$('#hidden_challan_id').val(id);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:760px;" >
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:760px; margin-left:2px">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table" align="center">
                <thead>
                    <th>Company</th>
                    <th>Delivery Date Range</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="180">Please Enter Challan No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="hidden_challan_no" id="hidden_challan_no">  
                        <input type="hidden" name="hidden_challan_id" id="hidden_challan_id">  
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">
                    	 <? echo create_drop_down( "cbo_company_id", 150,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '-- Select Company --',0,"",0); ?>        
                    </td>
                    <td align="center">
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
					  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
					</td>
                    <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"System No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );
						?>
                    </td>     
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_company_id').value, 'create_challan_search_list_view', 'search_div', '', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                     </td>
                </tr>
                <tr>
                	<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
           </table>
           <div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}



if($action=="create_challan_search_list_view")
{
	
	$data = explode("_",$data);
	$search_string="%".trim($data[0]);
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	if($company_id==0) { echo "Please Select Company First."; die; }
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
		$date_cond="and issue_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
		$date_cond="and issue_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
	$date_cond="";
	}
	
	$search_field_cond="";
	if(trim($data[0])!="")
	{
	if($search_by==1) $search_field_cond="and sys_number like '$search_string'";
	}
	
	if($db_type==0) 
	{
	$year_field=" YEAR(insert_date) as year";
		
	}
	else if($db_type==2) 
	{
	$year_field=" to_char(insert_date,'YYYY') as year";

	}
	else $year_field="";//defined Later
	
	$data_array=sql_select("SELECT c.barcode_no,a.issue_number FROM inv_issue_master a,pro_roll_details c 
	WHERE  a.id=c.mst_id and c.entry_form=61 and a.entry_form=61  and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
	$challan_barcode=array();
	$inserted_barcode=array();
	foreach($data_array as $val)
	{
	$challan_barcode[$val[csf('issue_number')]][]=$val[csf('barcode_no')];
	}
	$inserted_roll=sql_select("select b.challan_no,a.barcode_no from pro_roll_details a,inv_receive_mas_batchroll b where a.mst_id=b.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and a.entry_form=62 and b.entry_form=62");
	foreach($inserted_roll as $b_id)
	{
	$inserted_barcode[$b_id[csf('challan_no')]][]=$b_id[csf('barcode_no')];	
	}
	$sql="select  a.id,a.issue_number, a.challan_no, a.order_id,a.company_id,a.issue_purpose,a.issue_basis,a.knit_dye_source,a.knit_dye_company,
	a.issue_number_prefix_num,a.issue_date,$year_field
	from inv_issue_master a
	where  a.entry_form=61  and  company_id=$company_id $search_field_cond $date_cond ";
	$result = sql_select($sql);
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="140">Company</th>
            <th width="80">System No</th>
            <th width="70">Year</th>
            <th width="120">Knitting Source</th>
            <th width="140">Knitting Company</th>
            <th>Issue date</th>
        </thead>
	</table>
	<div style="width:740px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            { 
			 if(count($challan_barcode[$row[csf('issue_number')]])-count($inserted_barcode[$row[csf('issue_number')]])>0)
			 {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				$knit_comp="&nbsp;";
                if($row[csf('knit_dye_source')]==1) $knit_comp=$company_arr[$row[csf('knit_dye_company')]]; 
				else $knit_comp=$supllier_arr[$row[csf('knit_dye_company')]];
			
        	?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('issue_number')]; ?>','<? echo $row[csf('id')]; ?>');"> 
                    <td width="40"><? echo $i; ?></td>
                    <td width="140"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
                    <td width="80"><p>&nbsp;<? echo $row[csf('issue_number_prefix_num')]; ?></p></td>
                    <td width="70" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="120"><p><? echo $knitting_source[$row[csf('knit_dye_source')]]; ?>&nbsp;</p></td>
                    <td width="140"><p><? echo $knit_comp; ?>&nbsp;</p></td>
                    <td align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                </tr>
        	<?
            $i++;
            }
			
			}
        	?>
        </table>
    </div>
<?	
exit();
}

if($action=="update_system_popup")
{
	echo load_html_head_contents("Challan Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 
	<script>
		function js_set_value(data,id,challan)
		{
		$('#hidden_receive_no').val(data);
		$('#hidden_update_id').val(id);
		$('#hidden_challan_no').val(challan);
		parent.emailwindow.hide();
		}
    </script>

</head>

<body>
<div align="center" style="width:760px;" >
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:760px; margin-left:2px">
		<legend>Receive Number Popup</legend>           
            <table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table" align="center">
                <thead>
                    <th>Company</th>
                    <th>Receive No</th>
                    <th id="" width="250">Receive Date</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="hidden_receive_no" id="hidden_receive_no">  
                        <input type="hidden" name="hidden_update_id" id="hidden_update_id">
                        <input type="hidden" name="hidden_challan_no" id="hidden_challan_no">    
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">
                    	 <? echo create_drop_down( "cbo_company_id", 170,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '-- Select Company --',0,"",0); ?>        
                    </td>
                    <td align="center">
                    <input type="text" style="width:140px" class="text_boxes"  name="txt_receive_number" id="txt_receive_number" />
                    
					</td>
                    <td align="center">	
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:90px" readonly>To
					  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:90px" readonly>
                    </td>     
                  						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_receive_number').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value, 'create_update_search_list_view', 'search_div', '', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                     </td>
                </tr>
                <tr>
                	<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
           </table>
           <div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}



if($action=="create_update_search_list_view")
{
	
	$data = explode("_",$data);
	//$search_string="%".trim($data[0]);
	$receive_number=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[0];
	$year_id =$data[4];
	if($company_id==0) { echo "Please Select Company First."; die; }
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and receive_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and receive_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	$search_field_cond="";
	if(trim($receive_number)!="")
	{
	 $receiv_cond="and a.recv_number_prefix_num=$receive_number and YEAR(insert_date)=$year_id ";
	}
	
	if($db_type==0) 
	{
		$year_field=" YEAR(a.insert_date) as year";
		
	}
	else if($db_type==2) 
	{
		$year_field=" to_char(a.insert_date,'YYYY') as year";

	}
	else $year_field="";//defined Later
	
	$sql="select  a.id,a.recv_number_prefix_num,a.recv_number, a.challan_no, a.recv_number,a.company_id, a.dyeing_source,a.dyeing_company,a.receive_date,$year_field
			from  inv_receive_mas_batchroll a
			where a.entry_form=62 and a.company_id=$company_id $receiv_cond $date_cond order by  a.recv_number_prefix_num, a.receive_date";
			//echo $sql;
	$result = sql_select($sql);
//print_r($result);
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="140">Company</th>
            <th width="80">Receive No</th>
            <th width="70">Year</th>
            <th width="120">Dyeing Source</th>
            <th width="140">Dyeing Company</th>
            <th>Receive date</th>
        </thead>
	</table>
	<div style="width:740px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				 
				$knit_comp="&nbsp;";
                if($row[csf('dyeing_source')]==1)
					$knit_comp=$company_arr[$row[csf('dyeing_company')]]; 
				else
					$knit_comp=$supllier_arr[$row[csf('dyeing_company')]];
				
			
        	?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('recv_number')]; ?>','<? echo $row[csf('id')]; ?>','<? echo $row[csf('challan_no')]; ?>');"> 
                    <td width="40"><? echo $i; ?></td>
                    <td width="140"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
                    <td width="80"><p>&nbsp;<? echo $row[csf('recv_number_prefix_num')]; ?></p></td>
                    <td width="70" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="120"><p><? echo $knitting_source[$row[csf('dyeing_source')]]; ?>&nbsp;</p></td>
                    <td width="140"><p><? echo $knit_comp; ?>&nbsp;</p></td>
                    <td align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                </tr>
        	<?
            $i++;
            }
        	?>
        </table>
    </div>
<?	
exit();
}













































if($action=="grey_delivery_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	
	$company=$data[0];
	$txt_challan_no=$data[1];
	$update_id=$data[2];

	$company_array=array();
	$company_data=sql_select("select id, company_name, company_short_name from lib_company");
	foreach($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname']=$row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name']=$row[csf('company_name')];
	}

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier","id","short_name");
	$buyer_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$machine_details=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no");
	$brand_details=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name");
	
	$mstData=sql_select("select company_id, delevery_date, knitting_source, knitting_company from pro_grey_prod_delivery_mst where id=$update_id");
	
	$job_array=array();
	$job_sql="select a.job_no_prefix_num, a.job_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$job_sql_result=sql_select($job_sql);
	foreach($job_sql_result as $row)
	{
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
		$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
	}
	
	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		if(array_key_exists($row[csf('id')],$composition_arr))
		{
			$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
		else
		{
			$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
	}
	
?>
    <div style="width:1330px;">
    	<table width="1330" cellspacing="0" align="center" border="0">
			<tr>
				<td align="center" style="font-size:x-large"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			<tr>
				<td align="center" style="font-size:18px"><strong><u>Delivery Challan</u></strong></td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px"><strong><u>Knitting Section</u></strong></td>
			</tr>
        </table> 
        <br>
		<table width="1330" cellspacing="0" align="center" border="0">
			<tr>
				<td style="font-size:16px; font-weight:bold;">Challan No</td>
                <td width="170">:&nbsp;<? echo $txt_challan_no; ?></td>
                <td width="1000" id="barcode_img_id" align="right"></td>
			</tr>
            <tr>
				<td style="font-size:16px; font-weight:bold;">Delivery Date </td>
                <td colspan="2" width="1170">:&nbsp;<? echo change_date_format($mstData[0][csf('delevery_date')]); ?></td>
			</tr>
		</table>
        <br>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1330" class="rpt_table" >
            <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="90">Order No</th>
                    <th width="60">Buyer <br> Job</th>
                    <th width="50">System ID</th>
                    <th width="100">Prog. / Booking No</th>
                    <th width="80">Production Basis</th>
                    <th width="70">Knitting Source</th>
                    <th width="70">Knitting Company</th>
                    <th width="50">Yarn Count</th>
                    <th width="70">Yarn Brand</th>
                    <th width="60">Lot No</th>
                    <th width="70">Fab Color</th>
                    <th width="70">Color Range</th>
                    <th width="150">Fabric Type</th>
                    <th width="50">Stich</th>
                    <th width="50">Fin GSM</th>
                    <th width="40">Fab. Dia</th>
                    <th width="40" >M/C Dia</th>
                    <th width="40">Roll No</th>
                    <th>Qty</th>
                </tr>
            </thead>
            <?
				$i=1; $tot_qty=0; $receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
            	$sql="SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, c.barcode_no, c.roll_no, c.po_breakdown_id, d.current_delivery FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0";
				$result=sql_select($sql);
				foreach($result as $row)
				{
					$knit_company="&nbsp;";
					if($row[csf("knitting_source")]==1)
					{
						$knit_company=$company_array[$row[csf("knitting_company")]]['shortname'];
					}
					else if($row[csf("knitting_source")]==3)
					{
						$knit_company=$supplier_arr[$row[csf("knitting_company")]];
					}
					
					$count='';
					$yarn_count=explode(",",$row[csf('yarn_count')]);
					foreach($yarn_count as $count_id)
					{
						if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
					}
				?>
                	<tr>
                        <td width="30"><? echo $i; ?></td>
                        <td width="90" style="word-break:break-all;"><? echo $job_array[$row[csf('po_breakdown_id')]]['po']; ?></td>
                        <td width="60"><? echo $buyer_array[$row[csf('buyer_id')]]."<br>".$job_array[$row[csf('po_breakdown_id')]]['job']; ?></td>
                        <td width="50"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
                        <td width="100" style="word-break:break-all;"><? echo $row[csf('booking_no')]; ?></td>
                        <td width="80"><? echo $receive_basis[$row[csf('receive_basis')]]; ?></td>
                        <td width="70" style="word-break:break-all;"><? echo $knitting_source[$row[csf("knitting_source")]]; ?></td>
                        <td width="70"><? echo $knit_company; ?></td>
                        <td width="50" style="word-break:break-all;"><? echo $count; ?></td>
                        <td width="70" style="word-break:break-all;"><? echo $brand_details[$row[csf("brand_id")]]; ?></td>
                        <td width="60" style="word-break:break-all;"><? echo $row[csf('yarn_lot')]; ?></td>
                        <td width="70" style="word-break:break-all;"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
                        <td width="70" style="word-break:break-all;"><? echo $color_range[$row[csf("color_range_id")]]; ?></td>
                        <td width="150" style="word-break:break-all;"><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></td>
                        <td width="50" style="word-break:break-all;"><? echo $row[csf('stitch_length')]; ?></td>
                        <td width="50" style="word-break:break-all;"><? echo $row[csf('gsm')]; ?></td>
                        <td width="40" style="word-break:break-all;"><? echo $row[csf('width')]; ?></td>
                        <td width="40" style="word-break:break-all;"><? echo $machine_details[$row[csf('machine_no_id')]]; ?></td>
                        <td width="40"><? echo $row[csf('roll_no')]; ?></td>
                        <td align="right"><? echo number_format($row[csf('current_delivery')],2); ?></td>
                    </tr>
                <?
					$tot_qty+=$row[csf('current_delivery')];
					$i++;
				}
			?>
            <tr> 
                <td align="right" colspan="19"><strong>Total</strong></td>
                <td align="right"><? echo number_format($tot_qty,2,'.',''); ?></td>
			</tr>
            <tr>
                <td colspan="2" align="left"><b>Remarks:</b></td>
                <td colspan="18">&nbsp;</td>
            </tr>
		</table>
	</div>
    <? echo signature_table(44, $company, "1330px"); ?>
   	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
		  //alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			 
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			
			$("#barcode_img_id").show().barcode(value, btype, settings);
		} 
		generateBarcode('<? echo $txt_challan_no; ?>');
	</script>
<?
exit();
}

?>
