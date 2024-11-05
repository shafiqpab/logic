<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');


$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($_SESSION['logic_erp']["data_level_secured"]==1)
{
	if($_SESSION['logic_erp']["buyer_id"]!=0) $buyer_cond=" and id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
	if($_SESSION['logic_erp']["company_id"]!=0) $company_cond=" and id in (".$_SESSION['logic_erp']["company_id"].")"; else $company_cond="";
}
else
{
	$buyer_cond="";	$company_cond="";
}
$permission=$_SESSION['page_permission'];
//---------------------------------------------------- Start
$sample_library=return_library_array( "select id,sample_name from lib_sample", "id", "sample_name"  );
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );

 if($action=="show_sample_approved_list1")
 {
	 $data=explode('_',$data);
	$sql="select distinct sample_type_id from wo_po_sample_approval_info where job_no_mst='$data[0]' and is_deleted=0 and status_active=1";
	$data_array=sql_select($sql);
	?>
    <table>
        
                    <?
					foreach($data_array as $row)
	                {
	
					?>
                    <tr>
                    <td>
                    <a href="##" onClick="load_form(<? echo $row['sample_type_id'];  ?>)"><? echo $sample_library[$row['sample_type_id']];  ?> </a>
                    </td>
                    </tr>
                    <?
					}
		?>
				
      </table>             
        
   <?	
 }
 
 if($action=="show_sample_approved_list")
{
	
	$data=explode('_',$data);
    $sql="select min(id) as id , sample_name as sample_type_id from sample_development_dtls where sample_mst_id='$data[0]' and is_deleted=0 and status_active=1  group by sample_name order by id";
 	$data_array=sql_select($sql);
    foreach($data_array as $row)
	{
	?>
        <h3 align="left" id="accordion_h<? echo $row[csf("sample_type_id")]; ?>" style="width:910px" class="accordion_h" onClick="load_form(<? echo $row[csf("sample_type_id")];?>,<? echo $data ?>)"><span id="accordion_h<? echo $row[csf("sample_type_id")]; ?>span">+</span><? echo $sample_library[$row[csf("sample_type_id")]]; ?></h3>
	<?
	}
}

 
if ($action=="show_sample_approval_list_form")
{
	$data=explode('_',$data);
	$sql=sql_select("select mst_id,max(approved_date) as dt from approval_history where entry_form=25  group by mst_id  order by mst_id");
	foreach($sql as $val)
	{
		$app_date_arr[$val[csf("mst_id")]]=$val[csf("dt")];
	}
	$dt=explode(' ',$app_date_arr[$data[0]]); 
	 
 	$i=0;
		?>
		<h3 align="left" class="accordion_h" > +<? echo $sample_library[$data[1]];  ?> </h3>
		<div id="row_<? echo $data[1];  ?>" > 
            <form name="sampleapproval_1" id="sampleapproval_1" autocomplete="off">
             
                <table id="tbl_sample_info" class="rpt_table" rules="all">
                    <thead>
                        <tr>
                        <th width="100">Garments Item</th>
                        <th width="80">Color </th>
                        <th width="100">Sample Type </th>
                        <th width="155" class="must_entry_caption">Target Approval Date</th>
                        <th width="180" class="must_entry_caption">Sent To Sample Section</th>
                        <th width="155" class="must_entry_caption">Submission to Buyer </th>					     
                        <th width="60" class="must_entry_caption">Action</th>
                        <th width="100" class="must_entry_caption">Action Date</th>
                        <th width="140">Merchant Comments</th>
                        <th width="160" style="display:none">Sample Department Comments</th>
                        <th width="60">Status</th>
                        <th width="100"></th>
                        </tr>
                    </thead>
                    <tbody> 
						<?
						$data_array1=sql_select("select a.id as req_id,b.gmts_item_id ,b.sample_color, b.id as dtls_id,c.id as sample_table_id  from  sample_development_mst a, sample_development_dtls b, wo_po_sample_approval_info c where a.id=b.sample_mst_id and b.id=c.sample_dtls_id and a.id='$data[0]' and c.sample_type_id ='$data[1]' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and a.entry_form_id in(117,203,449) and b.entry_form_id in(117,203,449) and c.entry_form_id=137 and  b.status_active=1  group by a.id,b.gmts_item_id ,b.sample_color, b.id,c.id");
						  
 						if (count($data_array1)<=0)
					    {
                           $data_array1=sql_select("select a.id as req_id,b.gmts_item_id , b.sample_color,b.id as dtls_id from  sample_development_mst a, sample_development_dtls b  where a.id=b.sample_mst_id and a.id='$data[0]' and a.entry_form_id in(117,203,449) and b.entry_form_id in(117,203,449) and b.sample_name='$data[1]' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id,b.gmts_item_id,b.sample_color,b.id");
						}

                        foreach ( $data_array1 as $row1)
                        {
						   $i++;
							$data_array_sample_table=sql_select("select id,target_approval_date,send_to_factory_date,submitted_to_buyer,approval_status,approval_status_date,sample_comments ,current_status,status_active from wo_po_sample_approval_info where requisition_id='$data[0]'  and color_number_id ='".$row1[csf('sample_color')]."' and sample_type_id ='$data[1]' and entry_form_id=137 and   id='".$row1[csf('sample_table_id')]."'");
							
							list($sample_table_data_array )=$data_array_sample_table;
							if($sample_table_data_array[csf("approval_status")]==2 || $sample_table_data_array[csf("approval_status")]==3 )
							{
							$disabled=1;
							}
							else
							{
							$disabled=0;
							}
						
                        ?>
                            <tr>
                                <td>
                                 
									<?
									if(count($data_array_sample_table)>0)
									{
										?>
										<input name="updateIdForBtn_<? echo $i; ?>" type="hidden" id="updateIdForBtn_<? echo $i; ?>" style="width:180px" class="text_boxes" value="<? echo $sample_table_data_array[csf("id")];  ?>"/>

										<?
									}
									else
										{
											?>
											<input name="updateIdForBtn_<? echo $i; ?>" type="hidden" id="updateIdForBtn_<? echo $i; ?>" style="width:180px" class="text_boxes" value=""/>

										<?
										}
                                     echo create_drop_down( "cbogarmentsitem_".$i, 140, $garments_item,"", '', "", $row1[csf("gmts_item_id")], "",1,'' );
                                    ?>

                                    <input name="sampledtlsid_<? echo $i; ?>" type="hidden" id="sampledtlsid_<? echo $i; ?>" style="width:180px" class="text_boxes" value="<? echo $row1[csf("dtls_id")];  ?>"/>
                                </td>
                                <td>
									<?
                                    echo create_drop_down( "cbocolor_".$i, 100, "select b.id,b.color_name, a.sample_color from sample_development_dtls a, lib_color b where a.sample_color=b.id and a.sample_mst_id='".$row1[csf('req_id')]."' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.id,b.color_name, a.sample_color order by b.id","id,color_name", '', "", $row1[csf('sample_color')], "",1,'' );
                                    ?>
                                    <!-- <input name="colorsizetableid_<? echo $i; ?>" type="hidden" id="colorsizetableid_<? echo $i; ?>" style="width:180px" class="text_boxes" value="<? echo $row1[csf("color_mst_id")];  ?>"/> -->


                                </td>
                                <td>
									<?
                                    echo create_drop_down( "cbosampletype_".$i, 140, "select sample_name,id from lib_sample where is_deleted=0 and status_active=1 order by sample_name","id,sample_name", '', "", $data[1], "",1,'' );
                                    ?>
                                </td>
                                <td>
                                    <input name="txttargetapprovaldate_<? echo $i; ?>" type="text" id="txttargetapprovaldate_<? echo $i; ?>" style="width:80px" class="datepicker" onChange="copy_value(this.value,'txttargetapprovaldate_',<? echo $i;?>)" value="<? if($sample_table_data_array[csf("target_approval_date")]!="") echo change_date_format($sample_table_data_array[csf("target_approval_date")],'dd-mm-yyyy','-');  ?>" <? if($sample_table_data_array[csf("approval_status")]==2 || $sample_table_data_array[csf("approval_status")]==3 ){echo "disabled";} else{echo "";} ?>/>
                                </td>
                                <td>
                                    <input name="txtsendtofatorydate_<? echo $i; ?>" type="text" id="txtsendtofatorydate_<? echo $i; ?>" onChange="copy_value(this.value,'txtsendtofatorydate_',<? echo $i;?>)" style="width:80px"   class="datepicker"  value="<? if($sample_table_data_array[csf("send_to_factory_date")]!="") echo change_date_format($sample_table_data_array[csf("send_to_factory_date")],'dd-mm-yyyy','-');  ?>"   <? if($sample_table_data_array[csf("approval_status")]==2 || $sample_table_data_array[csf("approval_status")]==3 ){echo "disabled";} else{echo "";} ?>/>
                                </td>
                                <td>
                                    <input name="txtsubmissiontobuyerdate_<? echo $i; ?>" type="text" id="txtsubmissiontobuyerdate_<? echo $i; ?>"   onchange="copy_value(this.value,'txtsubmissiontobuyerdate_',<? echo $i;?>)" style="width:80px"  class="datepicker" value="<? if($sample_table_data_array[csf("submitted_to_buyer")]!="" || $sample_table_data_array[csf("submitted_to_buyer")]!="0000-00-00") echo change_date_format($sample_table_data_array[csf("submitted_to_buyer")],'dd-mm-yyyy','-');  ?>" <? if($sample_table_data_array[csf("approval_status")]==2 || $sample_table_data_array[csf("approval_status")]==3 ){echo "disabled";} else{echo "";} ?>/>
                                </td>
                                <td>
									<?
                                    echo create_drop_down( "cboapprovalstatus_".$i, 100, $approval_status,"", 1, "--   --", $sample_table_data_array[csf("approval_status")], "copy_value(this.value,'cboapprovalstatus_',". $i.")",$disabled,'' );
                                    ?>
                                </td>
                                <td>
                                    <input name="txtapprovalrejectdate_<? echo $i; ?>" type="text" id="txtapprovalrejectdate_<? echo $i; ?>" style="width:80px" class="datepicker" onChange="copy_value(this.value,'txtapprovalrejectdate_',<? echo $i;?>)" value="<? if($sample_table_data_array[csf("approval_status_date")]!="" || $sample_table_data_array[csf("approval_status_date")]!="0000-00-00") echo change_date_format($sample_table_data_array[csf("approval_status_date")],'dd-mm-yyyy','-');  ?>" <? if($sample_table_data_array[csf("approval_status")]==2 || $sample_table_data_array[csf("approval_status")]==3 ){echo "disabled";} else{echo "";} ?>/>
                                </td>
                                <td>
                                    <input name="txtsamplecomments_<? echo $i; ?>" type="text" id="txtsamplecomments_<? echo $i; ?>" style="width:180px" class="text_boxes" onChange="copy_value(this.value,'txtsamplecomments_',<? echo $i;?>)" value="<? echo $sample_table_data_array[csf("sample_comments")];  ?>" <? if($sample_table_data_array[csf("approval_status")]==2 || $sample_table_data_array[csf("approval_status")]==3 ){echo "disabled";} else{ echo "";} ?> placeholder="Single Click" onClick="fnc_comments(this.id,this.value)" readonly/>
                                </td>
                                 <td style="display:none">
                                    <input name="txtsampledepartmentcomments_<? echo $i; ?>" type="text" id="txtsampledepartmentcomments_<? echo $i; ?>" style="width:180px" class="text_boxes" onChange="copy_value(this.value,'txtsamplecomments_',<? echo $i;?>)" value="<? echo $sample_table_data_array[csf("sample_department_comments")];  ?>" <? if($sample_table_data_array[csf("approval_status")]==2 || $sample_table_data_array[csf("approval_status")]==3 ){echo "disabled";} else{ echo "";} ?> placeholder="Single Click" onClick="fnc_comments(this.id,this.value)" readonly/>
                                </td>
                                <td>
									<?
                                    echo create_drop_down( "cbostatus_".$i, 80, $row_status,"", '', "", $sample_table_data_array[csf("status_active")], "",$disabled,'' );
                                    ?>
                                    <input type="hidden" id="updateid_<? echo $i; ?>" value="<? echo $sample_table_data_array[csf(id)];  ?>" style="width:40">
                                </td>
                                
                                <td>
                                <?
								
								if($sample_table_data_array[csf("approval_status")]==2 && $sample_table_data_array[csf("current_status")]==1)
								{
								?>
								<input type="button" id="addrow_<? echo $i; ?>"  name="addrow_<? echo $i; ?>" style="width:60px" class="formbutton" value=" Re-Submit" onClick="resubmit(<? echo $i; ?>)" />
                                <?
								}
								?>
                                </td>
                            </tr>
                        <?
                        }
                        ?>
                        
                    </tbody>
                </table>
                <table>
                </table>
            </form>
		</div> 
		<?
}

if ($action=="load_drop_down_buyer")
{
   
	echo create_drop_down( "cbo_buyer_name", 157, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$data and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
} 

if ($action=="load_drop_down_po")
{
	echo create_drop_down( "cbo_po_no", 140, "select id , po_number from wo_po_break_down where job_no_mst='$data' and status_active=1 and is_deleted=0","id,po_number", 1, "-- Select Po --", '', "load_drop_down( '../requires/sample_approval_before_order_place_controller', this.value, 'load_drop_down_color', 'color_td' );" );
} 

if ($action=="load_drop_down_color")
{
	echo create_drop_down( "cbo_color", 140, "select a.color_number_id, b.color_name from wo_po_color_size_breakdown a, lib_color b where a.color_number_id=b.id and po_break_down_id='$data' and a.status_active=1 and a.is_deleted=0 group by a.color_number_id","color_number_id,color_name", 1, "-- Select Color --", '', "" );
} 




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
		
		 $id=return_next_id( "id", "wo_po_sample_approval_info", 1 ) ;
		 $field_array="id,requisition_id,sample_dtls_id,gmts_item_id, color_number_id,sample_type_id,target_approval_date,send_to_factory_date,submitted_to_buyer,approval_status,approval_status_date,sample_comments,is_deleted,status_active,inserted_by,insert_date,entry_form_id"; 
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cboItem="cbogarmentsitem_".$i;
			 $sampledtlsid="sampledtlsid_".$i;
			 $cbocolor="cbocolor_".$i;
			 $cbosampletype="cbosampletype_".$i;
			 $txttargetapprovaldate="txttargetapprovaldate_".$i;
			 $txtsendtofatorydate="txtsendtofatorydate_".$i;
			 $txtsubmissiontobuyerdate="txtsubmissiontobuyerdate_".$i;
			 $cboapprovalstatus="cboapprovalstatus_".$i;
			 $txtapprovalrejectdate="txtapprovalrejectdate_".$i;
			 $txtsamplecomments="txtsamplecomments_".$i;
			 $cbostatus="cbostatus_".$i;
			 $updateid="updateid_".$i;
			 if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$hidden_requisition_id.",".$$sampledtlsid.",".$$cboItem.",".$$cbocolor.",".$$cbosampletype.",".$$txttargetapprovaldate.",".$$txtsendtofatorydate.",".$$txtsubmissiontobuyerdate.",".$$cboapprovalstatus.",".$$txtapprovalrejectdate.",".$$txtsamplecomments.",0,".$$cbostatus.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',137)";
 			$id=$id+1;
		 }		
		$rID=sql_insert("wo_po_sample_approval_info",$field_array,$data_array,1);
 		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**"."**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**"."**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			 if($rID )
			    {
					oci_commit($con);   
					echo "0**".$rID;
				}
				else{
					oci_rollback($con);
					echo "10**".$rID;
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
		 $id=return_next_id( "id", "wo_po_sample_approval_info", 1 ) ;
		 $add_comma=0;
		 $field_array="id,requisition_id,sample_dtls_id,gmts_item_id, color_number_id,sample_type_id,target_approval_date,send_to_factory_date,submitted_to_buyer,approval_status,approval_status_date,sample_comments,is_deleted,status_active,inserted_by,insert_date,entry_form_id"; 
		 $field_array_update="requisition_id*sample_dtls_id*gmts_item_id*color_number_id*sample_type_id*target_approval_date*send_to_factory_date*submitted_to_buyer*approval_status*approval_status_date*sample_comments*is_deleted*status_active*updated_by*update_date";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			$cboItem="cbogarmentsitem_".$i;
			 $sampledtlsid="sampledtlsid_".$i;
			 $cbocolor="cbocolor_".$i;
			 $cbosampletype="cbosampletype_".$i;
			 $txttargetapprovaldate="txttargetapprovaldate_".$i;
			 $txtsendtofatorydate="txtsendtofatorydate_".$i;
			 $txtsubmissiontobuyerdate="txtsubmissiontobuyerdate_".$i;
			 $cboapprovalstatus="cboapprovalstatus_".$i;
			 $txtapprovalrejectdate="txtapprovalrejectdate_".$i;
			 $txtsamplecomments="txtsamplecomments_".$i;
			 $cbostatus="cbostatus_".$i;
			 $updateid="updateid_".$i;
  			 if(str_replace("'",'',$$updateid)!="")
			 {
				$id_arr[]=str_replace("'",'',$$updateid);
				$data_array_update[str_replace("'",'',$$updateid)] = explode(",",("".$hidden_requisition_id.",".$$sampledtlsid.",".$$cboItem.",".$$cbocolor.",".$$cbosampletype.",".$$txttargetapprovaldate.",".$$txtsendtofatorydate.",".$$txtsubmissiontobuyerdate.",".$$cboapprovalstatus.",".$$txtapprovalrejectdate.",".$$txtsamplecomments.",0,".$$cbostatus.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));
			 }
			 if(str_replace("'",'',$$updateid)=="")
			 {
				 if ($add_comma!=0) $data_array .=",";
 				 $data_array .="(".$id.",".$hidden_requisition_id.",".$$sampledtlsid.",".$$cboItem.",".$$cbocolor.",".$$cbosampletype.",".$$txttargetapprovaldate.",".$$txtsendtofatorydate.",".$$txtsubmissiontobuyerdate.",".$$cboapprovalstatus.",".$$txtapprovalrejectdate.",".$$txtsamplecomments.",0,".$$cbostatus.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',137)";

				 $add_comma++;
				 $id=$id+1;
			 }
		 }
		 $rID=execute_query(bulk_update_sql_statement( "wo_po_sample_approval_info", "id", $field_array_update,$data_array_update, $id_arr ),1);
		 if($data_array !='')
		 {
		 $rID=sql_insert("wo_po_sample_approval_info",$field_array,$data_array,1);
		 }
		 $resubmit_id=rtrim(str_replace("'","",$resubmit_id),",");
		 if( $resubmit_id!="")
		 {
			$rID=execute_query("update wo_po_sample_approval_info set current_status=0 where id in($resubmit_id)",1); 
		 }
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "1**"."**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**"."**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{    
		    if($rID)
			    {
					oci_commit($con);   
					echo "1**".$rID;
				}
				else{
					oci_rollback($con);
					echo "10**".$rID;
				}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";

		$rID=sql_delete("wo_po_sample_approval_info",$field_array,$data_array,"id","".$update_id."",1);
		//echo $rID;die;
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "2**".$rID;
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

if($action=="requisition_popup")
{
    extract($_REQUEST);
    echo load_html_head_contents("Sample Requisition Info","../../", 1, 1, $unicode);
?>
    <script>
        $(document).ready(function(e) {
            $("#txt_search_common").focus();
        });
        function search_populate(str)
        {
            //alert(str); 
            if(str==0) 
            {       
                document.getElementById('search_by_th_up').innerHTML="Enter Style ID";
                document.getElementById('search_by_td').innerHTML='<input   type="text" name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common" value=""  />';       
            }
            else if(str==1) 
            {
                document.getElementById('search_by_th_up').innerHTML="Enter Style Name";
                document.getElementById('search_by_td').innerHTML='<input   type="text" name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common" value=""  />';
            }                                                                                                                                                               
        }
        
        function js_set_value( mst_id )
        {
            document.getElementById('selected_job').value=mst_id;
            parent.emailwindow.hide();
        }
    </script>
</head>
<body>
    <div align="center" style="width:100%;" >
    <form name="searchsampledevelopmentfrm_1"  id="searchsampledevelopmentfrm_1" autocomplete="off">
        <table width="950" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <th colspan="8"><? echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" ); ?></th>
            </thead>
            <thead>
                <th width="140" class="must_entry_caption">Company Name</th>
                <th width="157">Buyer Name</th> 
                <th width="70">Requisition No</th> 
                <th width="80">Int. Ref. No </th>                   
                <th width="70">Style ID</th>
                <th width="90" >Style Name</th>
                <th width="160">Est. Ship Date</th>
                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:80px;" /></th>
            </thead>
            <tr class="general">
                <td> 
                    <input type="hidden" id="selected_job">
                    <? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( '../requires/sample_approval_before_order_place_controller', this.value, 'load_drop_down_buyer', 'buyer_td_req' );" ); ?>
                </td>
                <td id="buyer_td_req"><? echo create_drop_down( "cbo_buyer_name", 157, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
                <td><input type="text" style="width:60px" class="text_boxes" name="txt_requisition_num" id="txt_requisition_num"  /></td>
                <td><input type="text" style="width:70px;" name="txt_int_ref_no" id="txt_int_ref_no" class="text_boxes" placeholder="Write"  /></td>
                <td><input type="text" style="width:60px" class="text_boxes"  name="txt_style_id" id="txt_style_id"  /></td>
                <td><input type="text" style="width:80px" class="text_boxes"  name="txt_style_name1" id="txt_style_name1"  /></td>
                <td>
                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px"> To
                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px">
                </td> 
                <td>
                    <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style_id').value+'_'+document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_style_name1').value+'_'+document.getElementById('txt_requisition_num').value+'_'+document.getElementById('txt_int_ref_no').value, 'create_requisition_id_search_list_view', 'search_div', '../requires/sample_approval_before_order_place_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
                </td>
            </tr>
            <tr>
                <td colspan="8" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
            </tr>
        </table>
    </form>
    <div id="search_div"></div>
    </div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_requisition_id_search_list_view")
{
    $data=explode('_',$data);
    if ($data[2]!=0) $company=" and company_id='$data[2]'"; else { echo "Please Select Company First."; die; }
    if ($data[3]!=0) $buyer=" and buyer_name='$data[3]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
    if($data[0]==1)
	{
	   if (trim($data[1])!="") $style_id_cond=" and id='$data[1]'"; else $style_id_cond="";
	   if ($data[6]!="") $style_cond=" and style_ref_no='$data[6]'"; else $style_cond="";
	}
    else if($data[0]==4 || $data[0]==0)
	{
	  if (trim($data[1])!="") $style_id_cond=" and id like '%$data[1]%' "; else $style_id_cond="";
	  if ($data[6]!="") $style_cond=" and style_ref_no like '%$data[6]%' "; else $style_cond="";
	}
    else if($data[0]==2)
	{
	  if (trim($data[1])!="") $style_id_cond=" and id like '$data[1]%' "; else $style_id_cond="";
	  if ($data[6]!="") $style_cond=" and style_ref_no like '$data[6]%' "; else $style_cond="";
	}
    else if($data[0]==3)
	{
	  if (trim($data[1])!="") $style_id_cond=" and id like '%$data[1]' "; else $style_id_cond="";
	  if ($data[6]!="") $style_cond=" and style_ref_no like '%$data[6]' "; else $style_cond="";
	}
    
    
    if($db_type==0)
    {
    	if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and estimated_shipdate  between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'"; else $estimated_shipdate ="";
    }
    else if($db_type==2)
    {
    	if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and estimated_shipdate  between '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[5], "yyyy-mm-dd", "-",1)."'"; else $estimated_shipdate ="";
    }
    if ($data[7]!="") $requisition_num=" and requisition_number_prefix_num like '%$data[7]' "; else $requisition_num="";
    
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
    
    $dealing_marchant=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
    $team_leader=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');
    $txt_int_ref_no=trim(str_replace("'","",$data[8]));
	if($txt_int_ref_no!="") $ref_cond=" and internal_ref like '%$txt_int_ref_no%'";else $ref_cond="";
		
	
    $arr=array (2=>$buyer_arr,4=>$product_dept,6=>$dealing_marchant);
    $sql="";
	if($db_type==0)
    {
		$sql= "select id, requisition_number_prefix_num, SUBSTRING_INDEX(`insert_date`, '-', 1) as year, company_id, buyer_name, style_ref_no, product_dept, dealing_marchant, internal_ref from sample_development_mst where entry_form_id in(117,203,449) and sample_stage_id=2 and status_active=1 and is_deleted=0 $company $buyer $style_id_cond $style_cond  $ref_cond $estimated_shipdate $requisition_num and id in(select sample_development_id from sample_ex_factory_dtls where status_active=1 and is_deleted=0)order by id DESC";
    }
    else if($db_type==2)
    {
    	$sql= "select id, requisition_number_prefix_num, to_char(insert_date,'YYYY') as year, company_id, buyer_name, style_ref_no, product_dept, dealing_marchant, internal_ref from sample_development_mst where entry_form_id in(117,203,449) and  status_active=1 and is_deleted=0 $company $buyer $style_id_cond $ref_cond $style_cond $estimated_shipdate $requisition_num and id in(select sample_development_id from sample_ex_factory_dtls where status_active=1 and is_deleted=0) order by id DESC";
    }
	 //echo $sql;
     
    echo  create_list_view("list_view", "Year,Requisition No,Buyer Name,Style Name,Product Department,Int. Ref. No ,Dealing Merchant", "60,140,140,100,90,90,90","900","240",0, $sql , "js_set_value", "id", "", 1, "0,0,buyer_name,0,product_dept,0,dealing_marchant", $arr , "year,requisition_number_prefix_num,buyer_name,style_ref_no,product_dept,internal_ref,dealing_marchant", "",'','0,0,0,0,0,0') ;

    exit();
}
if ($action=="populate_data_from_search_popup")
{
	
	$team_leader_sql=sql_select(" select a.sample_development_id,b.sample_team from sample_sewing_output_mst a,sample_sewing_output_dtls b where a.id=b.sample_sewing_output_mst_id and a.entry_form_id=130 and b.entry_form_id=130 group by  a.sample_development_id,b.sample_team ");
	foreach($team_leader_sql as $values)
	{
		$team_leader_arr[$values[csf("sample_development_id")]]=$values[csf("sample_team")];
	}
	
   	$buyer_id=""; 
	$data_array=sql_select("select * from sample_development_mst where id='$data' and entry_form_id in(117,203,449)  and status_active=1 and is_deleted=0");
	foreach ($data_array as $row)
	{
 	   echo "document.getElementById('txt_requisition_no').value = '".$row[csf("requisition_number_prefix_num")]."';\n";  
        echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('hidden_requisition_id').value = '".$row[csf("id")]."';\n";  
		echo "document.getElementById('cbo_location_name').value = '".$row[csf("location_id")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";  
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref_no")]."';\n";  
 		echo "document.getElementById('cbo_product_department').value = '".$row[csf("product_dept")]."';\n";
 		echo "document.getElementById('cbo_sample_team').value = '".$team_leader_arr[$row[csf("id")]]."';\n";
		echo "document.getElementById('cbo_season').value = '".$row[csf("season")]."';\n";  
		echo "document.getElementById('cbo_agent').value = '".$row[csf("agent_name")]."';\n";  
 		 
		echo "document.getElementById('cbo_dealing_merchant').value = '".$row[csf("dealing_marchant")]."';\n";  
		//echo "document.getElementById('update_id').value = '".$row[csf("job_no")]."';\n"; 
		$buyer_id=$row[csf("buyer_name")];
	}
	    $sample_array=array();
		$sample_tag=sql_select("select a.id,a.sample_name,b.tag_sample,b.sequ from lib_sample a, lib_buyer_tag_sample b where a.id=b.tag_sample and b.sequ!=0 and b.buyer_id='".$buyer_id."' order by b.sequ");
		foreach($sample_tag as $sample_tag_row)
		{
		 $sample_array[$sample_tag_row[csf('id')]]=	$sample_tag_row[csf('sample_name')];
		}
		// print_r($sample_array);die;
		if(count($sample_array)>0)
		{
    		$sample_dropdown=create_drop_down( "cbo_sample_type", 140, $sample_array,"", '1', "--Select--", '', "load_form(this.value)",1,'' );
    		echo "document.getElementById('dropdown_span').innerHTML = '".$sample_dropdown."';\n"; 
    		echo "document.getElementById('msg_span').innerHTML = '';\n";
		}
		else
		{
			echo "document.getElementById('msg_span').innerHTML = 'No Sample Taged with this buyer';\n";
		}

}


if($action=="comments_popup")
{
    echo load_html_head_contents("Comments Info", "../../", 1, 1,'','','');
    extract($_REQUEST); 
	?>
    
</head>

<body>
<div style="width:430px;" align="center">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:400px; margin-top:10px;">
             <table cellspacing="0" cellpadding="0" border="1" rules="all" width="400" class="rpt_table" >
                <tr>
               		<td><textarea name="txt_comments" id="txt_comments" class="text_area" style="width:385px; height:120px;"><? echo $comments_data; ?></textarea></td>
                </tr>
            </table>
            <table width="400" id="tbl_close">
                 <tr>
                    <td align="center" >
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:100px" />
                    </td>
                </tr>
            </table>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}
?>