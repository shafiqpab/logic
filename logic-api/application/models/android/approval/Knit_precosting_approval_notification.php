<?php

class Knit_precosting_approval_notification extends CI_Model {
	function __construct() {
		error_reporting(0);
		parent::__construct();
	}

    //app............
    public function  approve_from_apps($post_data)
    { 
        $user_id = $post_data['user_id'];
        $ref_id = $post_data['ref_id'];
        $menu_id = $post_data['menu_id'];
        $entry_form = $post_data['entry_form'];

        $response = $this->approve_knit_precost_approval($user_id,$menu_id,$ref_id,$entry_form);

        if($response == 1 )
        {
            return ['status' => 'ok' ,'message' => 'Approved Successfully'];
        }
        else if($response == "already_approved")
        {
            return ['status' => 'fail' ,'message' => 'Already Approved'];
        }
        else if($response == "not_ready_to_approved")
        {
            return ['status' => 'fail' ,'message' => 'Ready To Approved First'];
        }
        else
        {
            return ['status' => 'fail' ,'message' => $response];
        }
    }
    //unapp..............
    public function unapprove_from_apps($post_data)
    {  
        $user_id = $post_data['user_id'];
        $ref_id = $post_data['ref_id'];
        $menu_id = $post_data['menu_id'];
        $message = $post_data['message'];
        $entry_form = $post_data['entry_form'];
        //return $ref_id;     
        $response = $this->unapprove_knit_precost_approval($user_id,$menu_id,$ref_id,$entry_form,$message);

        if($response == 1 )
        {
            return ['status' => 'ok' ,'message' => 'Unapproved Successfully'];
        }
        else if($response == "not_approved")
        {
            return ['status' => 'fail' ,'message' => 'Not Approved!. Approved First.'];
        }
        else
        {
            return ['status' => 'fail' ,'message' => $response];
        }
    }
    //deny....................
    public function deny_approve_from_apps($post_data)
    {
        $user_id = $post_data['user_id'];
        $ref_id = $post_data['ref_id'];
        $menu_id = $post_data['menu_id'];
        $message = $post_data['message'];
        $entry_form = $post_data['entry_form'];
        //return $menu_id;
        //print_r($ref_id);die;
        
            $response = $this->deny_knit_precost_approval($user_id,$menu_id,$ref_id,$entry_form);

            if($response == 1 )
            {
                return ['status' => 'ok' ,'message' => 'Deny Successfully'];
            }
            else if($response == "already_approved")
            {
                return ['status' => 'fail' ,'message' => 'Already Approved'];
            }
            else if($response == "not_ready_to_approved")
            {
                return ['status' => 'fail' ,'message' => 'Ready To Approved First'];
            }
            else
            {
                return ['status' => 'fail' ,'message' => $response];
            }
        
    }  
   
    //app hadle here............................................
    public function approve_knit_precost_approval($app_user_id,$menu_id,$ref_id,$entry_form)
    {
       
        $pc_date_time = date("d-M-Y h:i:s A",time());

        $CURRENT_APPROVAL_STATUS = return_field_value("ID","APPROVAL_MST","APPROVED_BY=$app_user_id and entry_form = $entry_form and mst_id = $ref_id ","ID");

        if($CURRENT_APPROVAL_STATUS == 1)
        {
            return "already_approved";
        }
       
        
        $sql="SELECT A.ID,A.APPROVED,A.READY_TO_APPROVED,A.UPDATED_BY,b.COMPANY_NAME,b.BUYER_NAME, b.BRAND_ID from WO_PRE_COST_MST a,WO_PO_DETAILS_MASTER b where b.id = a.job_id and a.id in($ref_id)";

		$sqlResult=sql_select( $sql );
        $approved_status_arr = array();
		foreach ($sqlResult as $row) 
		{ 
            if($row->APPROVED == 1)
            {
                return "already_approved";
            }
            else if($row->READY_TO_APPROVED != 1)
            {

                return "not_ready_to_approved";
            }

            $company_id = $row->COMPANY_NAME;
            $approved_status_arr[$row->ID] = $row->APPROVED;

            $matchDataArr[$row->ID] = array('buyer'=>$row->BUYER_NAME,'brand'=>$row->BRAND_ID);
		}



        $max_approved_no_arr = return_library_array("SELECT mst_id, max(approved_no) as approved_no from approval_history where mst_id in($ref_id)  and entry_form=77 group by mst_id","mst_id","approved_no");

        $buyer_arr = return_library_array( "SELECT ID, BUYER_NAME FROM LIB_BUYER", "ID", "BUYER_NAME"  );
        $brand_arr = return_library_array( "SELECT ID, BRAND_NAME FROM LIB_BUYER_BRAND", "ID", "BRAND_NAME"  );

		$finalDataArr=$this->get_final_user(array('company_id'=>$company_id,'page_id'=>$menu_id,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>$brand_arr,'match_data'=>$matchDataArr,'entry_form' => $entry_form));
        //print_r($finalDataArr);die;

        $sequ_no_arr_by_sys_id =$finalDataArr['final_seq'];
        $user_sequence_no = $finalDataArr['user_seq'][$app_user_id];
        $user_group_no = $finalDataArr['user_group'][$app_user_id];
        $max_group_no = max($finalDataArr['user_group']);

        $app_mst_data_array = ''; $history_data_array = ''; $mst_data_array_up = array();
     
		//print_r(5);die;
        $app_id = return_next_id( "ID","APPROVAL_MST") ;
        $app_his_id = return_next_id( "ID","APPROVAL_HISTORY") ;
        $target_app_id_arr = explode(',',$ref_id);
        foreach($target_app_id_arr as $mst_id)
        {		

			$approved = ((max($finalDataArr['final_seq'][$mst_id])==$user_sequence_no) || ( (max($finalDataArr['group_bypass_no_data_arr'][$max_group_no][2])==$user_sequence_no) && ($max_group_no == $user_group_no))  || ( (max($finalDataArr['group_bypass_no_data_arr'][$max_group_no][2]) == '') && ($max_group_no == $user_group_no) ) )?1:3;



			//App Muster.......................
            $user_ip='';
			if($app_mst_data_array!=''){$app_mst_data_array.=",";}
			$app_mst_data_array.="(".$app_id.",".$entry_form.",".$mst_id.",".$user_sequence_no.",".$user_group_no.",".$app_user_id.",'".$pc_date_time."',".$app_user_id.",'".$pc_date_time."','".$user_ip."')"; 
			$app_id=$app_id+1;
			
		
			$approved_no=($max_approved_no_arr[$mst_id] == '') ? 1 : $max_approved_no_arr[$mst_id];
			
			$approved_status=$approved_status_arr[$mst_id]*1;
			if($approved_status==2 || $approved_status==0)
			{	
				$approved_no=($max_approved_no_arr[$mst_id] == '') ? 1 : $max_approved_no_arr[$mst_id] + 1;
				$approved_no_array[$mst_id] = $approved_no;
			}
		 
            //App Hist.........................................
			if($history_data_array!="") $history_data_array.=",";
			$history_data_array.="(".$app_his_id.",".$entry_form.",".$mst_id.",".$approved_no.",'".$user_sequence_no."',1,".$app_user_id.",'".$pc_date_time."',".$app_user_id.",'".$pc_date_time."',1,".$approved.")";
			$app_his_id++;
			
			//mst data................................
			$mst_data_array_up[$mst_id] = explode(",",("".$approved.",".$user_sequence_no.",".$user_group_no.",".$app_user_id.",'".$pc_date_time."'")); 

			if($approved == 1)
			{
				$is_mst_final_seq[$mst_id] = $mst_id;
			}

        }
       

        $this->db->trans_strict(TRUE);
        $this->db->trans_begin();
        try
        {
           
            $app_mst_field_array="id, entry_form, mst_id,  sequence_no,group_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,USER_IP";
      
            $rID=sql_insert("APPROVAL_MST",$app_mst_field_array,$app_mst_data_array);

            if($rID!=1) 
            {
                throw new Exception($rID);
            }

           
            
            $mst_field_array_up="APPROVED*APPROVED_SEQU_BY*APPROVED_GROUP_BY*APPROVED_BY*APPROVED_DATE"; 
            $rID1=execute_query(bulk_update_sql_statement( "wo_pre_cost_mst", "ID", $mst_field_array_up, $mst_data_array_up, $target_app_id_arr ));
            //print_r($rID1);die;
            if($rID1!=1) 
            {
                throw new Exception($rID1);
            }
   
          
            $query = "UPDATE approval_history SET current_approval_status=0 WHERE entry_form=77 and mst_id in ($ref_id)";
            $rID2 = execute_query($query);
            if($rID2 != 1) 
            {
                throw new Exception($rID2);
            }
        
            $history_field_array = "id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,IS_SIGNING,APPROVED";
            $rID3 = sql_insert("approval_history",$history_field_array,$history_data_array);
            if($rID3 != 1 ) 
            {
                throw new Exception($rID);
            }
          
       
            if(count($approved_no_array)>0)
            {
                $approved_string="";
                foreach($approved_no_array as $key => $value)
                {
                    $approved_string.=" WHEN $key THEN ".$value."";
                }
                $approved_string_mst="CASE job_id ".$approved_string." END";
                $approved_string_dtls="CASE job_id ".$approved_string." END";

                
                $sql_insert="insert into wo_pre_cost_mst_histry(id, approved_no, pre_cost_mst_id, garments_nature, job_no, costing_date, incoterm, incoterm_place,
                machine_line, prod_line_hr, costing_per, remarks, copy_quatation, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent,
                cut_effi_percent, efficiency_wastage_percent, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active,
                is_deleted)
                        select
                        '', $approved_string_mst, id, garments_nature, job_no, costing_date, incoterm, incoterm_place, machine_line, prod_line_hr, costing_per,
                remarks, copy_quatation, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent,
                efficiency_wastage_percent, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted
                from wo_pre_cost_mst where job_id in ($ref_id)";
                // echo $sql_insert;die;


                $sql_precost_dtls="insert into wo_pre_cost_dtls_histry(id,approved_no,pre_cost_dtls_id,job_no,costing_per_id,order_uom_id,fabric_cost,  fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,
                commission_percent,	lab_test,lab_test_percent,inspection, inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,
                currier_percent, certificate_pre_cost,certificate_percent,common_oh,common_oh_percent,total_cost,total_cost_percent,price_dzn,price_dzn_percent,
                margin_dzn,margin_dzn_percent,cost_pcs_set,cost_pcs_set_percent,price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,
                cm_for_sipment_sche,margin_pcs_bom, margin_bom_per,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
                        select
                        '', $approved_string_dtls, id,job_no,costing_per_id,order_uom_id,fabric_cost,  fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,
                commission_percent,	lab_test,lab_test_percent,inspection, inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,
                currier_percent, certificate_pre_cost,certificate_percent,common_oh,common_oh_percent,total_cost,total_cost_percent,price_dzn,price_dzn_percent,
                margin_dzn,margin_dzn_percent,cost_pcs_set,cost_pcs_set_percent,price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,
                cm_for_sipment_sche,margin_pcs_bom, margin_bom_per,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_dtls  where  job_id in ($ref_id)";
                //echo $sql_precost_dtls;die;


                //------------------wo_pre_cost_fabric_cost_dtls_h-------------------------------------------------
                $sql_precost_fabric_cost_dtls="insert into wo_pre_cost_fabric_cost_dtls_h(id,approved_no,pre_cost_fabric_cost_dtls_id, job_no, item_number_id, body_part_id, fab_nature_id, color_type_id,lib_yarn_count_deter_id,construction,composition, fabric_description, gsm_weight,color_size_sensitive,	color, avg_cons, fabric_source, rate, amount,avg_finish_cons,avg_process_loss, inserted_by, insert_date,updated_by,update_date, status_active, is_deleted, company_id, costing_per,consumption_basis,process_loss_method,cons_breack_down,msmnt_break_down,color_break_down,yarn_breack_down,marker_break_down,width_dia_type)
                    select
                    '', $approved_string_dtls, id,job_no, item_number_id, body_part_id, fab_nature_id, color_type_id,lib_yarn_count_deter_id,construction,composition, fabric_description, gsm_weight,color_size_sensitive,	color, avg_cons, fabric_source, rate,amount,avg_finish_cons,avg_process_loss, inserted_by, insert_date,updated_by,update_date, status_active, is_deleted, company_id, costing_per,consumption_basis,process_loss_method,cons_breack_down,msmnt_break_down,color_break_down,yarn_breack_down,marker_break_down,width_dia_type from wo_pre_cost_fabric_cost_dtls where  job_id in ($ref_id)";
                //echo $sql_precost_fabric_cost_dtls;die;

                //--------------------wo_pre_cost_fab_yarn_cst_dtl_h--------------------------------------------------------
                $sql_precost_fab_yarn_cst="insert into wo_pre_cost_fab_yarn_cst_dtl_h(id,approved_no,pre_cost_fab_yarn_cost_dtls_id,fabric_cost_dtls_id,job_no,count_id,copm_one_id,percent_one,copm_two_id,percent_two,type_id,cons_ratio,cons_qnty,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
                    select
                    '', $approved_string_dtls, id,fabric_cost_dtls_id,job_no,count_id,copm_one_id,percent_one,copm_two_id,percent_two,type_id,cons_ratio,cons_qnty,rate,amount,
                inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_fab_yarn_cost_dtls  where  job_id in ($ref_id)";
                    //echo $sql_precost_fab_yarn_cst;die;

                //----------------------wo_pre_cost_comarc_cost_dtls_h----------------------------------------
                $sql_precost_fcomarc_cost_dtls="insert into wo_pre_cost_comarc_cost_dtls_h(id,approved_no,pre_cost_comarci_cost_dtls_id,job_no,item_id,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
                    select
                    '', $approved_string_dtls,id,job_no,item_id,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,
                is_deleted from wo_pre_cost_comarci_cost_dtls where  job_id in ($ref_id)";
                    //echo $sql_precost_fcomarc_cost_dtls;die;


                //-------------------------------------pre_cost_commis_cost_dtls_h-------------------------------------------
                $sql_precost_commis_cost_dtls="insert into wo_pre_cost_commis_cost_dtls_h(id,approved_no,pre_cost_commiss_cost_dtls_id,job_no,particulars_id,
                commission_base_id,commision_rate,commission_amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
                    select
                    '', $approved_string_dtls, id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_commiss_cost_dtls where  job_id in ($ref_id)";
                //	echo $sql_precost_commis_cost_dtls;die;

                //--------------------------------------   wo_pre_cost_embe_cost_dtls_his---------------------------------------------------------------------------
                $sql_precost_embe_cost_dtls="insert into  wo_pre_cost_embe_cost_dtls_his(id,approved_no,pre_cost_embe_cost_dtls_id,job_no,emb_name,
                emb_type,cons_dzn_gmts,rate,amount,charge_lib_id,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
                    select
                    '', $approved_string_dtls, id,job_no,emb_name,
                emb_type,cons_dzn_gmts,rate,amount,charge_lib_id,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_embe_cost_dtls  where  job_id in ($ref_id)";
                    //echo $sql_precost_commis_cost_dtls;die;

                //---------------------------------wo_pre_cost_fab_yarnbkdown_his------------------------------------------------

                $sql_precost_fab_yarnbkdown_his="insert into  wo_pre_cost_fab_yarnbkdown_his(id,approved_no,pre_cost_fab_yarnbreakdown_id,fabric_cost_dtls_id,job_no,count_id,copm_one_id,percent_one,copm_two_id,percent_two,type_id,cons_ratio,cons_qnty,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
                    select
                    '', $approved_string_dtls, id,fabric_cost_dtls_id,job_no,count_id,copm_one_id,percent_one,copm_two_id,percent_two,type_id,cons_ratio,cons_qnty,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_fab_yarnbreakdown  where  job_id in ($ref_id)";
                    //echo $sql_precost_fab_yarnbkdown_his;die;

                //------------------------------wo_pre_cost_sum_dtls_histroy-----------------------------------------------

                $sql_precost_fab_sum_dtls="insert into  wo_pre_cost_sum_dtls_histroy(id,approved_no,pre_cost_sum_dtls_id,job_no,fab_yarn_req_kg,fab_woven_req_yds,fab_knit_req_kg,fab_woven_fin_req_yds,fab_knit_fin_req_kg,fab_amount,avg,yarn_cons_qnty,yarn_amount,conv_req_qnty,conv_charge_unit,conv_amount,trim_cons,trim_rate,trim_amount,emb_amount,comar_rate,
                comar_amount,commis_rate,commis_amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
                    select
                    '', $approved_string_dtls, id,job_no,fab_yarn_req_kg,fab_woven_req_yds,fab_knit_req_kg,fab_woven_fin_req_yds,fab_knit_fin_req_kg,fab_amount,avg,yarn_cons_qnty,yarn_amount,conv_req_qnty,conv_charge_unit,conv_amount,trim_cons,trim_rate,trim_amount,emb_amount,comar_rate,
                comar_amount,commis_rate,commis_amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_sum_dtls  where  job_id in ($ref_id)";
                    //echo $sql_precost_fab_sum_dtls;die;
                    //-----------------------------wo_pre_cost_trim_cost_dtls_his------------------------------	-------------

                $sql_precost_trim_cost_dtls="INSERT into  wo_pre_cost_trim_cost_dtls_his(id,approved_no,pre_cost_trim_cost_dtls_id,job_no, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,cons_breack_down,inserted_by, insert_date,updated_by,update_date, status_active,is_deleted)
                    select
                    '', $approved_string_dtls, id,job_no, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,cons_breack_down,inserted_by, insert_date,updated_by,update_date, status_active,is_deleted from wo_pre_cost_trim_cost_dtls  where  job_id in ($ref_id)";
                    //echo $sql_precost_trim_cost_dtls;die;


                //---------------------------wo_pre_cost_trim_co_cons_dtl_h--------------------------------------------------

                $sql_precost_trim_co_cons_dtl="insert into   wo_pre_cost_trim_co_cons_dtl_h(id,approved_no,pre_cost_trim_co_cons_dtls_id,wo_pre_cost_trim_cost_dtls_id,job_no, po_break_down_id,item_size, cons, place, pcs,country_id)
                    select
                    '', $approved_string_dtls, id,wo_pre_cost_trim_cost_dtls_id,job_no,po_break_down_id,item_size, cons,place, pcs,country_id from wo_pre_cost_trim_co_cons_dtls  where  job_id in ($ref_id)";
                //---------------------------wo_pre_cost_fab_con_cst_dtls_h-----------------------------------------

                $sql_precost_fab_con_cst_dtls="insert into   wo_pre_cost_fab_con_cst_dtls_h(id,approved_no,pre_cost_fab_conv_cst_dtls_id,job_no, fabric_description,cons_process,req_qnty,charge_unit,amount,color_break_down,charge_lib_id,inserted_by,insert_date,updated_by,update_date, status_active,is_deleted)
                    select
                    '', $approved_string_dtls, id,job_no, fabric_description,cons_process,req_qnty,charge_unit,amount,color_break_down,charge_lib_id,inserted_by,insert_date,updated_by,update_date, status_active,is_deleted from wo_pre_cost_fab_conv_cost_dtls  where  job_id in ($ref_id)";


              
                    $rID4 = execute_query($sql_precost_trim_cost_dtls);
                    if($rID4 != 1){throw new Exception($rID4);}
                
                    $rID5= execute_query($sql_precost_trim_co_cons_dtl);
                    if($rID5 != 1){throw new Exception($rID5);}
         
                    $rID6= execute_query($sql_precost_fab_con_cst_dtls);
                    if($rID6 != 1){throw new Exception($rID6);}
      
                    $rID15= execute_query($sql_insert,0);
                    if($rID15 != 1){throw new Exception($rID15);}
     
                    $rID7= execute_query($sql_precost_dtls);
                    if($rID7 != 1){throw new Exception($rID7);}
            
                    $rID8= execute_query($sql_precost_fabric_cost_dtls);
                    if($rID8 != 1){throw new Exception($rID8);}
           
                    $rID9= execute_query($sql_precost_fab_yarn_cst);
                    if($rID9 != 1){throw new Exception($rID9);}
             
                    $rID10= execute_query($sql_precost_fcomarc_cost_dtls);
                    if($rID10 != 1){throw new Exception($rID10);}
              
                    $rID11= execute_query($sql_precost_commis_cost_dtls);
                    if($rID11 != 1){throw new Exception($rID11);}
                
                    $rID12= execute_query($sql_precost_embe_cost_dtls);
                    if($rID12 != 1){throw new Exception($rID12);}
               
                    $rID13= execute_query($sql_precost_fab_yarnbkdown_his);
                    if($rID13 != 1){throw new Exception($rID13);}
                
                    $rID14= execute_query($sql_precost_fab_sum_dtls);
                    if($rID14 != 1){throw new Exception($rID14);}
                  
            }
       
            
            $approval_data = $this->get_notification_approval_data($ref_id,0,$pc_date_time);
            $desc = $this->get_desc($ref_id);


            $approval_parameter = array('BUYER_ID' => $matchDataArr[$ref_id]['buyer'],'BRAND_ID' => $matchDataArr[$ref_id]['brand'],'approval_desc'=>$desc,'approval_data' => $approval_data,'title'=>"Pending Approval :: Pre Cost Approval",'is_commit'=>0);
           
            $res_engine = notificationEngineForApps($ref_id,$company_id,$entry_form,$approval_parameter,$app_user_id);
            if($res_engine!=1)
            {
                throw new Exception($res_engine);
            }
            //print_r($desc);die;
            $notif_res =  insertNotificationData($ref_id,$entry_form,$app_user_id,$desc,1);
            if($notif_res != 1)
            {
                throw new Exception($notif_res);
            }
            $appr_data = array("USER_ID"=>$app_user_id,"SEQUENCE_NO" =>$user_sequence_no,"COMPANY_ID"=> $company_id,'NOTIFICATION_TYPE'=>0,'approval_data' => $approval_data);
            $ret_push =  push_all($ref_id,$entry_form,$appr_data);
            if($ret_push!=1) 
            {
                throw new Exception($ret_push);
            }


 

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return 0;
            }
            else
            {
                $this->db->trans_commit();
                return 1;
            }
        }
        catch(Exception $e)
        {
            $this->db->trans_rollback();
            return $e->getMessage();
        }
    }
    //unapp hadle here............................................
    public function unapprove_knit_precost_approval($user_id,$menu_id,$ref_id,$entry_form,$message ='')
    {   //print_r(5);die;
        $pc_date_time = date("d-M-Y h:i:s A",time());
  
		$CURRENT_APPROVAL_STATUS = return_field_value("ID","APPROVAL_MST","APPROVED_BY=$user_id and ENTRY_FORM = $entry_form and MST_ID  = $ref_id ","ID");
        if(empty($CURRENT_APPROVAL_STATUS))
        {
            return "not_approved";
        }

        $appr_data = array("USER_ID"=>'',"SEQUENCE_NO" =>'',"COMPANY_ID"=> '','NOTIFICATION_TYPE'=>0,'approval_data' =>'');
        push_all($ref_id, $entry_form, $appr_data);
        
        $this->db->trans_strict(TRUE);
        $this->db->trans_begin();
        try
        {
        
            $rID1=sql_multi_row_update("wo_pre_cost_mst","approved*ready_to_approved*APPROVED_SEQU_BY*APPROVED_GROUP_BY*APPROVED_BY",'0*0*0*0*0',"id",$ref_id,0);
            if($rID1!=1) 
            {
                throw new Exception($rID1);
            }

            $query="UPDATE approval_history SET current_approval_status=0,APPROVED=0,IS_SIGNING=0 WHERE entry_form=$entry_form and mst_id in ($ref_id) and approved_by <> $user_id ";
			$rID2=execute_query($query,1);
            if($rID2!=1) 
            {
                throw new Exception($rID2);
            }

            $query="delete from approval_mst  WHERE entry_form=$entry_form and mst_id in ($ref_id)";
			$rID3=execute_query($query,1); 
			if($rID3!=1) 
            {
                throw new Exception($rID3);
            }
            

            $query="UPDATE approval_history SET current_approval_status=0,IS_SIGNING=0, un_approved_by='".$user_id."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' ,APPROVED=0 WHERE entry_form=$entry_form and current_approval_status=1 and mst_id in ($ref_id)";
			$rID4=execute_query($query,1);
			if($rID4!=1) 
            {
                throw new Exception($rID4);
            }

            $query="DELETE FROM APPROVAL_NOTIFICATION_ENGINE  WHERE ENTRY_FORM=77 AND REF_ID IN ($ref_id) ";
            $rID6=execute_query($query); 

            if($rID6!=1)
            {
                throw new Exception($rID6);
            }
           // print_r(5);die;

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return 0;
            }
            else
            {
                $this->db->trans_commit();
                return 1;
                
            }
        }
        catch(Exception $e)
        {
            $this->db->trans_rollback();
            return $e->getMessage();
        }     
    }
    //deny hadle here............................................
    public function deny_knit_precost_approval($user_id,$menu_id,$ref_id,$entry_form)
    {
        $pc_date_time = date("d-M-Y h:i:s A",time());
  
		
        $sql="SELECT APPROVED,READY_TO_APPROVED FROM WO_PRE_COST_MST WHERE id = $ref_id";
        $sql_res = sql_select_arr($sql);
        foreach($sql_res as $rows)
        {
            $READY_TO_APPROVED = $rows['READY_TO_APPROVED'];
            if($READY_TO_APPROVED != 1)
            {
                return "not_ready_to_approved";
            }
        } 

        $appr_data = array("USER_ID"=>'',"SEQUENCE_NO" =>'',"COMPANY_ID"=> '','NOTIFICATION_TYPE'=>0,'approval_data' =>'');
        push_all($ref_id, $entry_form, $appr_data);

        $this->db->trans_strict(TRUE);
        $this->db->trans_begin();
        try
        {
        
            $rID1=sql_multi_row_update("wo_pre_cost_mst","approved*ready_to_approved*APPROVED_SEQU_BY*APPROVED_GROUP_BY*APPROVED_BY",'0*0*0*0*0',"id",$ref_id,0);
            if($rID1!=1) 
            {
                throw new Exception($rID1);
            }

            $query="UPDATE approval_history SET current_approval_status=0,APPROVED=0,IS_SIGNING=0 WHERE entry_form=$entry_form and mst_id in ($ref_id) and approved_by <> $user_id ";
			$rID2=execute_query($query,1);
            if($rID2!=1) 
            {
                throw new Exception($rID2);
            }

            $query="delete from approval_mst  WHERE entry_form=$entry_form and mst_id in ($ref_id)";
			$rID3=execute_query($query,1); 
			if($rID3!=1) 
            {
                throw new Exception($rID3);
            }
            

            $query="UPDATE approval_history SET current_approval_status=0,IS_SIGNING=0, un_approved_by='".$user_id."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' ,APPROVED=0 WHERE entry_form=$entry_form and current_approval_status=1 and mst_id in ($ref_id)";
			$rID4=execute_query($query,1);
			if($rID4!=1) 
            {
                throw new Exception($rID4);
            }

            $query="DELETE FROM APPROVAL_NOTIFICATION_ENGINE  WHERE ENTRY_FORM=77 AND REF_ID IN ($ref_id) ";
            $rID6=execute_query($query); 

            if($rID6!=1)
            {
                throw new Exception($rID6);
            }
           // print_r(5);die;

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return 0;
            }
            else
            {
                $this->db->trans_commit();
                return 1;
                
            }
        }
        catch(Exception $e)
        {
            $this->db->trans_rollback();
            return $e->getMessage();
        }

    }

    function  get_final_user($parameterArr=array())
    {
        $lib_buyer_arr=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));
     
        $brandSql = "SELECT ID, BRAND_NAME,BUYER_ID from lib_buyer_brand where STATUS_ACTIVE=1 and IS_DELETED=0";
        $brandSqlRes=sql_select($brandSql);
        
        foreach($brandSqlRes as $row){
            $buyer_wise_brand_id_arr[$row->BUYER_ID][$row->ID] = $row->ID;
        }
    
        //Electronic app setup data.....................
        $sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT as DEPARTMENT_ID,LOCATION as LOCATION_ID ,ITEM_CATEGORY as ITEM_ID,GROUP_NO FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND PAGE_ID = {$parameterArr['page_id']} AND IS_DELETED = 0  order by SEQUENCE_NO";
        $sql_result = $this->db->query($sql)->result();

        foreach($sql_result as $rows)
        {
          
            $userDataArr[$rows->USER_ID]['BUYER_ID']=$rows->BUYER_ID;
            $userDataArr[$rows->USER_ID]['BRAND_ID']=$rows->BRAND_ID;

            if($userDataArr[$rows->USER_ID]['BUYER_ID']=='' || $userDataArr[$rows->USER_ID]['BUYER_ID'] == 0){
                $userDataArr[$rows->USER_ID]['BUYER_ID']=$lib_buyer_arr;
            }

            if($userDataArr[$rows->USER_ID]['BRAND_ID']=='' || $userDataArr[$rows->USER_ID]['BRAND_ID'] == 0){
                $tempBrandArr = array();
                foreach(explode(',',$userDataArr[$rows->USER_ID]['BUYER_ID']) as $buyer_id){
                    if(count($buyer_wise_brand_id_arr[$buyer_id])){$tempBrandArr[]= implode(',',$buyer_wise_brand_id_arr[$buyer_id]);}
                }
                $userDataArr[$rows->USER_ID]['BRAND_ID']=implode(',',$tempBrandArr);
            }

    
            $usersDataArr[$rows->USER_ID]['BUYER_ID']=explode(',',$userDataArr[$rows->USER_ID]['BUYER_ID']);
            $usersDataArr[$rows->USER_ID]['BRAND_ID']=explode(',',$userDataArr[$rows->USER_ID]['BRAND_ID']);
            
            $userSeqDataArr[$rows->USER_ID]=$rows->SEQUENCE_NO;
            $userGroupDataArr[$rows->USER_ID]=$rows->GROUP_NO;
            $groupBypassNoDataArr[$rows->GROUP_NO][$rows->BYPASS][$rows->SEQUENCE_NO]=$rows->SEQUENCE_NO;
        
        }

        $finalSeq=array();
        foreach($parameterArr['match_data'] as $sys_id=>$bbtsRows){
            foreach($userSeqDataArr as $user_id=>$seq){
                if(
                    (in_array($bbtsRows['buyer'],$usersDataArr[$user_id]['BUYER_ID']) && $bbtsRows['buyer']>0)
                    && (in_array($bbtsRows['brand'],$usersDataArr[$user_id]['BRAND_ID']) || $bbtsRows['brand']==0)
                 
                ){
                    $finalSeq[$sys_id][$user_id]=$seq;
                }
            }
        }
    
        //var_dump($finalSeq);
        //die;
       
        return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr,'user_group'=>$userGroupDataArr,'group_bypass_no_data_arr'=>$groupBypassNoDataArr);
    }
    
    //Use for data reay..................common use
    public function get_notification_approval_data($ref_id,$IS_SEEN = 0,$INSERT_DATE = '')
	{
        $priority_arr = array(1=>'High',2=>'Medium',3=>'Low');

        $app_sql ="SELECT APPROVED_BY,APPROVED_DATE FROM APPROVAL_MST WHERE ENTRY_FORM=77 and MST_ID =? ORDER BY SEQUENCE_NO DESC";
        $approval_res = $this->db->query($app_sql, array($ref_id))->result();
        $approval_data = array();
        if(!empty($approval_res))
        {
            foreach ($approval_res as $row)
            {
                $approval_data['APPROVED_BY'] = $row->APPROVED_BY;
                $approval_data['APPROVED_DATE'] = $row->APPROVED_DATE;
                break;
            }
        }

        $buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
        
		$company_name=return_library_array( "select id,company_name from lib_company",'id','company_name');
		$dealing_mar=return_library_array( "select ID,TEAM_MEMBER_NAME FROM lib_mkt_team_member_info",'id','TEAM_MEMBER_NAME');
		$user_name=return_library_array( "select ID,USER_FULL_NAME FROM USER_PASSWD",'id','USER_FULL_NAME');
        //$sql = "SELECT p.ID,p.COSTING_DATE,a.JOB_NO,a.STYLE_OWNER,a.TOTAL_PRICE,a.INSERT_DATE,a.STYLE_REF_NO,a.SET_SMV,a.GMTS_ITEM_ID,a.JOB_QUANTITY,pd.CM_COST,pd.CM_COST_PERCENT,pd.TRIMS_COST_PERCENT,pd.MARGIN_PCS_SET_PERCENT,p.INSERTED_BY,a.DEALING_MARCHANT,d.COMPANY_NAME,a.COMPANY_NAME as COMPANY_ID, a.BUYER_NAME as BUYER_ID,b.BUYER_NAME,a.BRAND_ID,c.BRAND_NAME from WO_PRE_COST_MST p left join WO_PRE_COST_DTLS pd on pd.JOB_ID = p.JOB_ID,WO_PO_DETAILS_MASTER a left join lib_buyer b on a.buyer_name = b.id left join LIB_BUYER_BRAND c on a.brand_id = c.id left join LIB_COMPANY d on a.company_name = d.id where  a.id = p.job_id and A.id = ?";
        //$sql = "SELECT a.ID,a.JOB_NO,a.EXCHANGE_RATE,a.SEW_SMV,a.COSTING_DATE,a.INSERTED_BY,b.USER_FULL_NAME,d.COMPANY_NAME,e.BUYER_NAME,a.INSERT_DATE,b.USER_FULL_NAME,f.PUB_SHIPMENT_DATE FROM WO_PRE_COST_MST a,USER_PASSWD b,WO_PO_DETAILS_MASTER c LEFT JOIN WO_PO_BREAK_DOWN f on c.ID = f.JOB_ID,LIB_COMPANY d, LIB_BUYER e where a.STATUS_ACTIVE = 1 and a.IS_DELETED = 0 and a.INSERTED_BY = b.id and c.ID = a.JOB_ID and c.COMPANY_NAME = d.ID and c.BUYER_NAME = e.ID and a.ID = ?";
        $sql = "SELECT a.ID AS job_id, p.ID, p.COSTING_DATE,p.COSTING_PER,pd.MARGIN_DZN,pd.FABRIC_COST, a.JOB_NO, a.STYLE_OWNER, a.TOTAL_PRICE, a.STYLE_REF_NO, LISTAGG(d.ITEM_NAME, ',') WITHIN GROUP (ORDER BY d.ITEM_NAME) AS gmts_names,
        LISTAGG(c.SMV_SET, ',') WITHIN GROUP (ORDER BY c.SMV_SET) AS SMV_SET_LIST, a.JOB_QUANTITY, pd.CM_COST, pd.CM_COST_PERCENT, 
        pd.TRIMS_COST_PERCENT, pd.MARGIN_PCS_SET_PERCENT, p.INSERTED_BY, a.DEALING_MARCHANT,a.AVG_UNIT_PRICE, a.COMPANY_NAME AS COMPANY_ID, a.BUYER_NAME AS BUYER_ID,b.PUB_SHIPMENT_DATE,p.INSERT_DATE,
        a.BRAND_ID,a.QUOTATION_ID FROM WO_PRE_COST_MST p, WO_PRE_COST_DTLS pd, WO_PO_DETAILS_MASTER a , WO_PO_BREAK_DOWN b, WO_PO_DETAILS_MAS_SET_DETAILS c,lib_garment_item d 
        WHERE pd.JOB_ID = p.JOB_ID AND a.ID = p.JOB_ID AND a.ID = b.JOB_ID AND c.JOB_ID = a.ID and c.GMTS_ITEM_ID = d.ID  AND p.ID = ? 
        GROUP BY a.ID, p.ID, p.COSTING_DATE, a.JOB_NO, 
        a.STYLE_OWNER, a.TOTAL_PRICE, a.STYLE_REF_NO, a.JOB_QUANTITY, pd.CM_COST, pd.CM_COST_PERCENT, pd.TRIMS_COST_PERCENT, 
        pd.MARGIN_PCS_SET_PERCENT, p.INSERTED_BY, a.DEALING_MARCHANT, a.COMPANY_NAME, a.BUYER_NAME, a.BRAND_ID,a.AVG_UNIT_PRICE,p.COSTING_PER,a.QUOTATION_ID,pd.MARGIN_DZN,pd.FABRIC_COST,b.PUB_SHIPMENT_DATE,p.INSERT_DATE";
        $result = $this->db->query($sql, array($ref_id))->result();
        //print_r($result);die;
        $data_arr = array();
        if (!empty($result))
        {
            foreach ($result as $row)
            {   //print_r($row->AVG_UNIT_PRICE);die;
                if($row->costing_per==1) $price_costing_per=$row->AVG_UNIT_PRICE*1*12;
                if($row->costing_per==2) $price_costing_per=$row->AVG_UNIT_PRICE*1*1;
                if($row->costing_per==3) $price_costing_per=$row->AVG_UNIT_PRICE*2*12;
                if($row->costing_per==4) $price_costing_per=$row->AVG_UNIT_PRICE*3*12;
                if($row->costing_per==5) $price_costing_per=$row->AVG_UNIT_PRICE*4*12;
                //$desc = "Job no: ".$row->JOB_NO.", Exchange Rate: ".$row->EXCHANGE_RATE."\nCosting. date: ".date('d/m/Y',strtotime($row->COSTING_DATE)).",SMV : ".$row->SEW_SMV."\nCompany: ".$row->COMPANY_NAME."\nBuyer name : ".$row->BUYER_NAME."Inserted by: ". $row->USER_FULL_NAME;
                //$desc = "PO.Com: ".$row['COMPANY_NAME']."    Style Owner: ".$company_name[$row['STYLE_OWNER']]." \nBOM No: ".$row['JOB_NO']."    BOM date: ".date('d/m/Y',strtotime($row['INSERT_DATE']))."\nStyle Ref: ".$row['STYLE_REF_NO']."    Style (Pcs): ".$row['JOB_QUANTITY']."\nGMT Item: ".$row['GMTS_ITEM_ID']."    SMV: ".$row['SET_SMV']."\nStyle Value: ".$row['TOTAL_PRICE']."    CM Cost $: ".$row['CM_COST']."\nFOB/Pc:     Costing/Pc:     Mar/Pcs $: \nFab:     Trims: ".$row['TRIMS_COST_PERCENT']."    CM: ".$row['CM_COST_PERCENT']."    Margin: ".$row['MARGIN_PCS_SET_PERCENT']."\nMarchent: ".$dealing_mar[$row['DEALING_MARCHANT']]."    Forward by: ".$user_name[$row['INSERTED_BY']];
                $desc = "PO.Com: ".$company_name[$row->COMPANY_ID]."    Style Owner: ".$company_name[$row->STYLE_OWNER].
                " \nBOM No: ".$row->JOB_NO."    BOM date: ".date('d/m/Y',strtotime($row->COSTING_DATE)).
                "\nStyle Ref: ".$row->STYLE_REF_NO."    Style (Pcs): ".$row->JOB_QUANTITY.
                "\nGMT Item: ".$row->GMTS_NAMES.
                "\nSMV: ".$row->SMV_SET_LIST."   Style Value: ".$row->TOTAL_PRICE.

                "\nCM Cost $: ".$row->CM_COST."    Avg FOB:".$row->AVG_UNIT_PRICE."     Costing/Pc:".$price_costing_per."     Mar/Pcs $:".$row->MARGIN_DZN.
                "\nFab:".$row->FABRIC_COST."     Trims: ".$row->TRIMS_COST_PERCENT."    CM: ".$row->CM_COST_PERCENT."    Margin: ".$row->MARGIN_PCS_SET_PERCENT.

                "\nMarchent: ".$dealing_mar[$row->DEALING_MARCHANT]."    Forward by: ".$user_name[$row->INSERTED_BY];
                //$desc = str_replace(" ", " ", $desc);
                $data_arr = array(
                    'ID' => $row->ID,
                    'DATE' => date('d/m/Y',strtotime($row->COSTING_DATE)),
                    'DELIVERY_DATE' => date('d/m/Y',strtotime($row->PUB_SHIPMENT_DATE)),
                    'COMPANY' => $company_name[$row->COMPANY_ID],
                    'BUYER' => $buyer_name_arr[$row->BUYER_ID],
                    'SYS_NUMBER' => $row->JOB_NO,
                    'SYS_DEF' => "",
                    'DESC' => $desc,
                    'IS_SEEN' => $IS_SEEN,
                    'INSERT_DATE' =>date('d/m/Y',strtotime( $row->INSERT_DATE)),
                    'APPROVED_DATE' => date('d/m/Y',strtotime($approval_data['APPROVED_DATE'])),
                    'APPROVED_BY' =>  $approval_data['APPROVED_BY'],
                    'MENU_ID' => return_field_value("page_id as menu_id","ELECTRONIC_APPROVAL_SETUP","entry_form=77","menu_id")
                );
            }
        }
        return $data_arr;
	}
    //use for data desc ready..................common use
    public function get_desc($ref_id,$user_id='')
    {   
        $priority_array=array(1=>"Normal",2=>"Urgent",3=>"Critical");
        $USER_FULL_NAME = ""; $desc = "";
        if(!empty($user_id))
        {
            $USER_FULL_NAME =  return_field_value("USER_FULL_NAME","USER_PASSWD","id=$user_id","USER_FULL_NAME");
        }
       
        $sql = "SELECT a.ID,a.JOB_NO,a.EXCHANGE_RATE,a.SEW_SMV,a.COSTING_DATE,a.INSERTED_BY,b.USER_FULL_NAME FROM WO_PRE_COST_MST a,USER_PASSWD b where a.STATUS_ACTIVE = 1 and a.IS_DELETED = 0 and a.INSERTED_BY = b.id and a.ID = $ref_id";
        $result  = sql_select_arr($sql);
        
        foreach ($result as $row)
        {
            if(!empty( $USER_FULL_NAME))  
            $USER_FULL_NAME ="\nForwarded By : ".$USER_FULL_NAME;
            else 
            $USER_FULL_NAME ="\nInserted By : ".$row['USER_FULL_NAME'];

            return $desc = "Job no: ".$row['JOB_NO']."Exchange Rate: ".$row['EXCHANGE_RATE']."Sew SMV: ".$row['SEW_SMV']."Costing Date: ".$row['COSTING_DATE']."Inserted By: ".$row['USER_FULL_NAME'];
        }
    
        return $desc;
    }

	public function get_notification_details($user_id,$menu_id)
	{
		$menu_sql = "SELECT D.ENTRY_FORM,
                            D.REF_ID,
                            D.M_MENU_ID,
                            D.IS_SEEN,
                            D.IS_APPROVED,
                            D.INSERT_DATE
                    FROM APPROVAL_NOTIFICATION_ENGINE D
                    WHERE     D.M_MENU_ID = ?
                            AND D.USER_ID = ?
                            AND D.STATUS_ACTIVE = 1
                    ORDER BY D.INSERT_DATE DESC";
        //return $menu_sql;

		$menu_result = $this->db->query($menu_sql, array($menu_id,$user_id))->result();
        //return $menu_result;
        
		$menu_arr = array();
        $approve_data = [];
        $unapprove_data = [];
       

		if (!empty($menu_result))
		{
			foreach ($menu_result as $menu)
			{ 
                $ret_data = $this->get_notification_approval_data($menu->REF_ID,$menu->IS_SEEN,$menu->INSERT_DATE);
                //print_r($ret_data);die;
                if(!empty($ret_data))
                {
                    if($menu->IS_APPROVED == 1)
                    {
                        $approve_data[]=$ret_data;
                    }
                    else
                    {
                        $unapprove_data[]=$ret_data;
                    }
                }
			}
		}
        
        $data = array(
            'approve_data' => $approve_data,
            'unapprove_data' => $unapprove_data,
        );
		return $data;

	}




}