<?
function submit_approved(total_tr,type)
	{
		//var operation=4;
		var cbo_company_name = $('#cbo_company_name').val();
		var req_nos = ""; var requisition_ids = "";
		freeze_window(0);
		// Confirm Message  *********************************************************************************************************
		if($('#cbo_approval_type').val()==1)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to UnApproved All Requisition No");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to UnApproved All Requisition No");
					if(second_confirmation==false)
					{
						release_freezing();
						return;
					}
				}
			}

		}
		else if($('#cbo_approval_type').val()==0)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to Approved All Requisition No");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to Approved All Requisition No");
					if(second_confirmation==false)
					{
						release_freezing();
						return;
					}
				}
			}
		}
		// Confirm Message End ***************************************************************************************************

		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				req_id = $('#req_id_'+i).val();
				if(req_nos=="") req_nos= req_id; else req_nos +=','+req_id;
			}


			requisition_id = parseInt($('#requisition_id_'+i).val());
			if(requisition_id>0)
			{
				if(requisition_ids=="") requisition_ids= requisition_id; else requisition_ids +=','+requisition_id;
			}
		}

		if(req_nos=="")
		{
			alert("Please Select At Least One Booking");
			release_freezing();
			return;
		}

		$('#txt_selected_id').val(req_nos);
		fnSendMail('../','',1,0,0,1,type);

		var data="action=approve&operation="+operation+'&approval_type='+type+'&req_nos='+req_nos+'&requisition_id='+requisition_id+get_submitted_data_string('cbo_company_name*cbo_approval_type*txt_alter_user_id',"../");
		
		//alert(data);

		
		//alert(data);return;
		http.open("POST","requires/yarn_requisition_approval_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_purchase_requisition_approval_Reply_info;
	}

  if ($action=="approve")
  {  
    $process = array( &$_POST );
  
    extract(check_magic_quote_gpc( $process )); 
    $con = connect();
      $company_name=str_replace("'","",$cbo_company_name);
    //$approval_type=str_replace("'","",$cbo_approval_type);
    $txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
    $user_id_approval=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;
  
  
  
  
  
    //echo "10**".'zdhgdsfgsgf';die;
  
    //............................................................................
    
    $sql = "select a.ID  from inv_purchase_requisition_mst a where a.COMPANY_ID=$cbo_company_name and a.STATUS_ACTIVE=1 and a.IS_DELETED=0  and a.id in($req_nos)";
      //echo $sql;die();
    $sqlResult=sql_select( $sql );
    foreach ($sqlResult as $row)
    {
          //if($row['READY_TO_APPROVE'] != 1){echo '21**Ready to approve yes is mandatory';exit();}
      $matchDataArr[$row['ID']]=array('buyer_id'=>0,'brand_id'=>0,'supplier_id'=>0,'store'=>0);
    }
      $finalDataArr=getFinalUser(array('company_id'=>$cbo_company_name,'ENTRY_FORM'=>20,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0,'product_dept_arr'=>0,'match_data'=>$matchDataArr));
    
   
    $sequ_no_arr_by_sys_id =$finalDataArr['final_seq'];
    $user_sequence_no = $finalDataArr['user_seq'][$user_id_approval];
   
  
    if($approval_type==5)
    {
  
      $rID1=sql_multirow_update("inv_purchase_requisition_mst","IS_APPROVED*READY_TO_APPROVE*APPROVED_SEQU_BY",'0*0*0',"id",$req_nos,0);
      if($rID1) $flag=1; else $flag=0;
  
      if($flag==1)
      {
        $query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=20 and current_approval_status=1 and mst_id in ($req_nos)";
        $rID2=execute_query($query,1);
        if($rID2) $flag=1; else $flag=0;
      }
  
          
      if($flag==1) 
      {
        $query="delete from approval_mst  WHERE entry_form=20 and mst_id in ($req_nos)";
        $rID3=execute_query($query,1); 
        if($rID3) $flag=1; else $flag=0; 
      }
      
       // echo "191**".$rID1.",".$rID2.",".$rID3.",".$rID4;oci_rollback($con);die;
      
      $response=$req_nos;
      if($flag==1) $msg='50'; else $msg='51';
  
    } 
    else if($approval_type==0)
    {      
       
      $id=return_next_id( "id","approval_mst", 1 ) ;
      $ahid=return_next_id( "id","approval_history", 1 ) ;	
      
      $target_app_id_arr = explode(',',$req_nos);	
          foreach($target_app_id_arr as $mst_id)
          {		
        if($data_array!=''){$data_array.=",";}
        $data_array.="(".$id.",20,".$mst_id.",".$user_sequence_no.",".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
        $id=$id+1;
        
        $approved_no=$max_approved_no_arr[$mst_id][$user_id_approval]+1;
        if($history_data_array!="") $history_data_array.=",";
        $history_data_array.="(".$ahid.",20,".$mst_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
        $ahid++;
        
        //mst data.......................
        $approved=(max($finalDataArr['final_seq'][$mst_id])==$user_sequence_no)?1:3;
        $data_array_up[$mst_id] = explode(",",("".$approved.",".$user_sequence_no."")); 
          }
     
   
  
          $flag=1;
      if($flag==1) 
      {
        $field_array="id, entry_form, mst_id,  sequence_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
        $rID1=sql_insert("approval_mst",$field_array,$data_array,0);
              //echo "10**insert into approval_mst ($field_array) values" . $data_array;die;
  
  
        if($rID1) $flag=1; else $flag=0; 
      }
      
      if($flag==1) 
      {
        $field_array_up="IS_APPROVED*APPROVED_SEQU_BY"; 
        $rID2=execute_query(bulk_update_sql_statement( "inv_purchase_requisition_mst", "id", $field_array_up, $data_array_up, $target_app_id_arr ));
        if($rID2) $flag=1; else $flag=0; 
      }
  
      if($flag==1)
      {
        $query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=20 and mst_id in ($req_nos)";
        $rID3=execute_query($query,1);
        if($rID3) $flag=1; else $flag=0;
      }
       
      if($flag==1)
      {
        $field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,IS_SIGNING";
        $rID4=sql_insert("approval_history",$field_array,$history_data_array,0);
        if($rID4) $flag=1; else $flag=0;
      }
      
      //echo "21**".$rID1.",".$rID2.",".$rID3.",".$rID4;oci_rollback($con);die;
      
      if($flag==1) $msg='19'; else $msg='21';
  
          
    }
    else
    {              
      
      $next_user_app = sql_select("select id from approval_history where mst_id in($req_nos) and entry_form=20 and sequence_no >$user_sequence_no and current_approval_status=1 group by id");
          
      if(count($next_user_app)>0)
      {
        echo "25**unapproved"; 
        disconnect($con);
        die;
      }
  
      $rID1=sql_multirow_update("inv_purchase_requisition_mst","is_approved*READY_TO_APPROVE*APPROVED_SEQU_BY",'0*0*0',"id",$req_nos,0);
      if($rID1) $flag=1; else $flag=0;
  
  
      if($flag==1)
      {
        $query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=20 and mst_id in ($req_nos)";
        $rID2=execute_query($query,1);
        if($rID2) $flag=1; else $flag=0;
      }
  
      
      if($flag==1) 
      {
        $query="delete from approval_mst  WHERE entry_form=20 and mst_id in ($req_nos)";
        $rID3=execute_query($query,1); 
        if($rID3) $flag=1; else $flag=0; 
      }
      
  
      
      if($flag==1)
      {
        $query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=20 and current_approval_status=1 and mst_id in ($req_nos)";
        $rID4=execute_query($query,1);
        if($rID4) $flag=1; else $flag=0;
      }
       
      echo "5**".$rID1.",".$rID2.",".$rID3.",".$rID4.$flag;oci_rollback($con);die;
      
      $response=$req_nos;
      if($flag==1) $msg='20'; else $msg='22';
      
    }
    
  
    if($db_type==2 || $db_type==1 )
    {
      if($flag==1)
      {
        oci_commit($con);
        echo $msg."**".$response;
      }
      else
      {
        oci_rollback($con);
        echo $msg."**".$response;
      }
    }
    disconnect($con);
    die;
    
  }
  
