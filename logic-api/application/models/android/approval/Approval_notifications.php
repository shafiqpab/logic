<?php

class Approval_notifications extends CI_Model {
	function __construct() {
		error_reporting(0);
		parent::__construct();
	}
    
   
    public function getDesc($ref_id,$user_id='')
    {
        $desc ='';
         
        $USER_FULL_NAME = "";
        if(!empty($user_id))
        {
            $USER_FULL_NAME =  return_field_value("USER_FULL_NAME","USER_PASSWD","id=$user_id","USER_FULL_NAME");
        }
        $sql = "SELECT A.ID,
                        A.REQU_NO,
                        B.COMPANY_NAME,
                        C.STORE_NAME,
                        A.REQUISITION_DATE,
                        D.USER_FULL_NAME,
                        A.DELIVERY_DATE,
                        E.LOCATION_NAME,
                        A.REQU_PREFIX_NUM,
                        SUM (X.AMOUNT)                  AS AMOUNT,
                        LISTAGG (Y.SHORT_NAME, ',')     AS CATEGORY,
                        A.PRIORITY_ID
                FROM INV_PURCHASE_REQUISITION_MST A
                        LEFT JOIN INV_PURCHASE_REQUISITION_DTLS X
                            ON A.ID = X.MST_ID AND X.IS_DELETED = 0
                        LEFT JOIN LIB_ITEM_CATEGORY_LIST Y ON X.ITEM_CATEGORY = Y.CATEGORY_ID
                        LEFT JOIN USER_PASSWD D ON A.INSERTED_BY = D.ID
                        LEFT JOIN LIB_COMPANY B ON A.COMPANY_ID = B.ID
                        LEFT JOIN LIB_STORE_LOCATION C ON A.STORE_NAME = C.ID
                        LEFT JOIN LIB_LOCATION E ON A.LOCATION_ID = E.ID
                WHERE A.ID =  $ref_id
                GROUP BY A.ID,
                        A.REQU_NO,
                        B.COMPANY_NAME,
                        C.STORE_NAME,
                        A.REQUISITION_DATE,
                        D.USER_FULL_NAME,
                        A.DELIVERY_DATE,
                        E.LOCATION_NAME,
                        A.REQU_PREFIX_NUM,
                        A.PRIORITY_ID
        ";
        
        $result  = sql_select_arr($sql);
        // $result = $this->sql_select_arr($sql);

        $desc = "";

        $priority_array=array(1=>"Normal",2=>"Urgent",3=>"Critical");
        foreach ($result as $row)
        {
            if(!empty( $USER_FULL_NAME))  $USER_FULL_NAME ="\nForwarded By : ".$USER_FULL_NAME;
            else $USER_FULL_NAME ="\nInserted By : ".$row['USER_FULL_NAME'];
            return $desc = "Company: ".$row['COMPANY_NAME'].", Loc: ".$row['LOCATION_NAME']."\nReq. No: ".$row['REQU_PREFIX_NUM'].", Req. date: ".date('d/m/Y',strtotime($row['REQUISITION_DATE']))."\nPriority: ".$priority_array[$row['PRIORITY_ID']].", Store : ".$row['STORE_NAME']."\nCategory: ".implode(",",array_unique(explode(",",$row['CATEGORY'])))."\nReq. For : ".$row['REQ_FOR']."\nReq. Value: ".number_format($row['AMOUNT'],2,".","").$USER_FULL_NAME;
            
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
                $ret_data = $this->get_purchase_requisition_approval_data_v2($menu->REF_ID,$menu->IS_SEEN,$menu->INSERT_DATE);
                
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


    public function get_purchase_requisition_approval_data($ref_id,$IS_SEEN = 0,$INSERT_DATE = '')
	{
        
        $repartment_arr[0] = "";
        $repartment_arr =  return_library_arr(" select ID,DEPARTMENT_NAME from lib_department where  is_deleted=0 and status_active=1","ID","DEPARTMENT_NAME");
       
        $sql = "SELECT A.ID,
                A.REQU_NO,
                B.COMPANY_NAME,
                C.STORE_NAME,
                A.REQUISITION_DATE,
                D.USER_FULL_NAME,
                A.DELIVERY_DATE,
                D.LOCATION_NAME,
                A.REQU_PREFIX_NUM,
                SUM (X.AMOUNT)                  AS AMOUNT,
                LISTAGG (Y.SHORT_NAME, ',')     AS CATEGORY,
                A.PRIORITY_ID,
                A.DEPARTMENT_ID,
                F.SECTION_NAME
        FROM INV_PURCHASE_REQUISITION_MST A
                LEFT  JOIN INV_PURCHASE_REQUISITION_DTLS X
                    ON A.ID = X.MST_ID AND X.IS_DELETED = 0
                LEFT JOIN LIB_ITEM_CATEGORY_LIST Y ON X.ITEM_CATEGORY = Y.CATEGORY_ID
                LEFT JOIN USER_PASSWD D ON A.INSERTED_BY = D.ID
                LEFT JOIN LIB_COMPANY B ON A.COMPANY_ID = B.ID
                LEFT JOIN LIB_STORE_LOCATION C ON A.STORE_NAME = C.ID
                LEFT JOIN LIB_LOCATION D ON A.LOCATION_ID = D.ID
                LEFT JOIN LIB_SECTION F ON A.SECTION_ID = F.ID
        WHERE A.ID =  ?
        GROUP BY A.ID,
                A.REQU_NO,
                B.COMPANY_NAME,
                C.STORE_NAME,
                A.REQUISITION_DATE,
                D.USER_FULL_NAME,
                A.DELIVERY_DATE,
                D.LOCATION_NAME,
                A.REQU_PREFIX_NUM,
                A.PRIORITY_ID,
                A.DEPARTMENT_ID,
                F.SECTION_NAME";

        $result = $this->db->query($sql, array($ref_id))->result();

        $app_sql ="SELECT APPROVED_BY,APPROVED_DATE FROM APPROVAL_MST WHERE ENTRY_FORM=1 and MST_ID =? ORDER BY SEQUENCE_NO DESC";

        $approval_res = $this->db->query($app_sql, array($ref_id))->result();

        $data_arr = array();
        $priority_arr = array(1=>'High',2=>'Medium',3=>'Low');

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

        if (!empty($result))
        {
            foreach ($result as $row)
            {
                $desc = "Company: ".$row->COMPANY_NAME.", Loc: ".$row->LOCATION_NAME."\nReq. date: ".date('d/m/Y',strtotime($row->REQUISITION_DATE))."\nPriority: ".$priority_arr[$row->PRIORITY_ID].", Store : ".$row->STORE_NAME."\nCategory: ".implode(",",array_unique(explode(",",$row->CATEGORY)))."\nReq. For : ".$repartment_arr[$row->DEPARTMENT_ID]."\nSection For : ".$row->SECTION_NAME."\nReq. Value: ".number_format($row->AMOUNT,2,".","")."\nInserted By : ".$row->USER_FULL_NAME;
                $desc = str_replace(" ", " ", $desc);
                $data_arr[] = array(
                    'ID' => $row->ID,
                    'DATE' => date('d/m/Y',strtotime($row->REQUISITION_DATE)),
                    'DELIVERY_DATE' => $row->DELIVERY_DATE,
                    'COMPANY' => $row->COMPANY_NAME,
                    'BUYER' => '',
                    'SYS_NUMBER' => $row->REQU_NO,
                    'SYS_DEF' => '',
                    'DESC' => $desc,
                    'IS_SEEN' => $IS_SEEN,
                    'INSERT_DATE' => $INSERT_DATE,
                    'APPROVED_DATE' => date('d/m/Y',strtotime( $approval_data['APPROVED_DATE'])),
                    'APPROVED_BY' =>  $approval_data['APPROVED_BY']
                );
            }
        }
        return $data_arr;
	}

    public function get_purchase_requisition_approval_data_v2($ref_id,$IS_SEEN = 0,$INSERT_DATE = '')
	{
        
        $repartment_arr[0] = "";
        $repartment_arr =  return_library_arr(" select ID,DEPARTMENT_NAME from lib_department where  is_deleted=0 and status_active=1","ID","DEPARTMENT_NAME");
       
        $sql = "SELECT A.ID,
                A.REQU_NO,
                B.COMPANY_NAME,
                C.STORE_NAME,
                A.REQUISITION_DATE,
                D.USER_FULL_NAME,
                A.DELIVERY_DATE,
                D.LOCATION_NAME,
                A.REQU_PREFIX_NUM,
                SUM (X.AMOUNT)                  AS AMOUNT,
                LISTAGG (Y.SHORT_NAME, ',')     AS CATEGORY,
                A.PRIORITY_ID,
                A.DEPARTMENT_ID,
                F.SECTION_NAME
        FROM INV_PURCHASE_REQUISITION_MST A
                LEFT  JOIN INV_PURCHASE_REQUISITION_DTLS X
                    ON A.ID = X.MST_ID AND X.IS_DELETED = 0
                LEFT JOIN LIB_ITEM_CATEGORY_LIST Y ON X.ITEM_CATEGORY = Y.CATEGORY_ID
                LEFT JOIN USER_PASSWD D ON A.INSERTED_BY = D.ID
                LEFT JOIN LIB_COMPANY B ON A.COMPANY_ID = B.ID
                LEFT JOIN LIB_STORE_LOCATION C ON A.STORE_NAME = C.ID
                LEFT JOIN LIB_LOCATION D ON A.LOCATION_ID = D.ID
                LEFT JOIN LIB_SECTION F ON A.SECTION_ID = F.ID
        WHERE A.ID =  ?
        GROUP BY A.ID,
                A.REQU_NO,
                B.COMPANY_NAME,
                C.STORE_NAME,
                A.REQUISITION_DATE,
                D.USER_FULL_NAME,
                A.DELIVERY_DATE,
                D.LOCATION_NAME,
                A.REQU_PREFIX_NUM,
                A.PRIORITY_ID,
                A.DEPARTMENT_ID,
                F.SECTION_NAME";

        $result = $this->db->query($sql, array($ref_id))->result();

        $app_sql ="SELECT APPROVED_BY,APPROVED_DATE FROM APPROVAL_MST WHERE ENTRY_FORM=1 and MST_ID =? ORDER BY SEQUENCE_NO DESC";

        $approval_res = $this->db->query($app_sql, array($ref_id))->result();

        $data_arr = array();
        $priority_arr = array(1=>'High',2=>'Medium',3=>'Low');

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

        if (!empty($result))
        {
            foreach ($result as $row)
            {
                $desc = "Company: ".$row->COMPANY_NAME.", Loc: ".$row->LOCATION_NAME."\nReq. date: ".date('d/m/Y',strtotime($row->REQUISITION_DATE))."\nPriority: ".$priority_arr[$row->PRIORITY_ID].", Store : ".$row->STORE_NAME."\nCategory: ".implode(",",array_unique(explode(",",$row->CATEGORY)))."\nReq. For : ".$repartment_arr[$row->DEPARTMENT_ID]."\nSection For : ".$row->SECTION_NAME."\nReq. Value: ".number_format($row->AMOUNT,2,".","")."\nInserted By : ".$row->USER_FULL_NAME;
                $desc = str_replace(" ", " ", $desc);
                $data_arr = array(
                    'ID' => $row->ID,
                    'DATE' => date('d/m/Y',strtotime($row->REQUISITION_DATE)),
                    'DELIVERY_DATE' => $row->DELIVERY_DATE,
                    'COMPANY' => $row->COMPANY_NAME,
                    'BUYER' => '',
                    'SYS_NUMBER' => $row->REQU_NO,
                    'SYS_DEF' => '',
                    'DESC' => $desc,
                    'IS_SEEN' => $IS_SEEN,
                    'INSERT_DATE' => $INSERT_DATE,
                    'APPROVED_DATE' => date('d/m/Y',strtotime( $approval_data['APPROVED_DATE'])),
                    'APPROVED_BY' =>  $approval_data['APPROVED_BY']
                );
            }
        }
        return $data_arr;
	}

    public function  approve_from_apps($post_data)
    { 
		$user_id = $post_data['user_id'];
		$ref_id = $post_data['ref_id'];
		$menu_id = $post_data['menu_id'];
        //return $menu_id;
       
		$entry_form = 1;

        $sql = "SELECT COUNT(ID) FROM inv_purchase_requisition_dtls where is_deleted = 0 and mst_id =  ".$ref_id;
        $cntSql  = sql_select_arr($sql);
           // $cntSql = sql_select_arr($sql);

        // $cntSql=$this->sql_select_arr("SELECT COUNT(ID) FROM inv_purchase_requisition_dtls where is_deleted = 0 and mst_id =  ".$ref_id);

        if(count($cntSql) <= 0 )
        {
            return ['status' => 'fail' ,'message' => 'Deatils Data not found'];
        }
        $response = $this->approve_purchase_requisition_v2($user_id,$menu_id,$ref_id,$entry_form);
        
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

    public function unapprove_from_apps($post_data)
    {
        $user_id = $post_data['user_id'];
		$ref_id = $post_data['ref_id'];
		$menu_id = $post_data['menu_id'];
		$message = $post_data['message'];
        $entry_form = 1;
        //return $ref_id;     
        $response = $this->unapprove_purchase_requisition_v2($user_id,$menu_id,$ref_id,$message);

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

    public function deny_approve_from_apps($post_data)
    {
        $user_id = $post_data['user_id'];
		$ref_id = $post_data['ref_id'];
		$menu_id = $post_data['menu_id'];
		$message = $post_data['message'];
		$entry_form = $post_data['entry_form'];
        //return $menu_id;
       
		//$entry_form =  return_field_value("ENTRY_FORM","ELECTRONIC_APPROVAL_SETUP","page_id=$menu_id","ENTRY_FORM");


            $sql = "SELECT COUNT(ID) FROM inv_purchase_requisition_dtls where is_deleted = 0 and mst_id =  ".$ref_id;
            $cntSql  = sql_select_arr($sql); 
 

            if(count($cntSql) <= 0 )
            {
                return ['status' => 'fail' ,'message' => 'Deatils Data not found'];
            }
            $response = $this->deny_approve_purchase_requisition_v2($user_id,$menu_id,$ref_id,$entry_form);

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

    public function approve_purchase_requisition_v2($user_id,$menu_id,$ref_id,$entry_form)
    {
        $sql = "SELECT COMPANY_ID,BUYER_ID,IS_APPROVED,READY_TO_APPROVE,UPDATED_BY FROM INV_PURCHASE_REQUISITION_MST WHERE id = $ref_id";
        $sql_res  = sql_select_arr($sql);  

        // $sql_res = $this->sql_select_arr("SELECT COMPANY_ID,BUYER_ID,IS_APPROVED,READY_TO_APPROVE,UPDATED_BY FROM INV_PURCHASE_REQUISITION_MST WHERE id = $ref_id");
        $pc_date_time = date("d-M-Y h:i:s A",time());

        $company_name = "";
        $cbo_buyer_name = "";
        foreach($sql_res as $rows)
        {
            $company_name = $rows['COMPANY_ID'];
            $cbo_company_name = $rows['COMPANY_ID'];
            $cbo_buyer_name = $rows['BUYER_ID'];
            $IS_APPROVED = $rows['IS_APPROVED'];
            $READY_TO_APPROVE = $rows['READY_TO_APPROVE'];
            if($IS_APPROVED == 1)
            {
                return "already_approved";
            }
            if($READY_TO_APPROVE != 1)
            {

                return "not_ready_to_approved";
            }
        }

        $CURRENT_APPROVAL_STATUS =  return_field_value("CURRENT_APPROVAL_STATUS","APPROVAL_HISTORY","approved_by=$user_id and entry_form = $entry_form and mst_id = $ref_id ","CURRENT_APPROVAL_STATUS");
        if($CURRENT_APPROVAL_STATUS == 1)
        {

            return "already_approved";
        }

        $company_arr= return_library_arr( "SELECT ID, COMPANY_SHORT_NAME FROM LIB_COMPANY",'ID','COMPANY_SHORT_NAME');
        
        $buyer_arr= return_library_arr( "SELECT ID, BUYER_NAME FROM LIB_BUYER", "ID", "BUYER_NAME"  );
        $brand_arr= return_library_arr( "SELECT ID, BRAND_NAME FROM LIB_BUYER_BRAND", "ID", "BRAND_NAME"  );
        $item_cat_arr= return_library_arr( "SELECT ID, SHORT_NAME FROM LIB_ITEM_CATEGORY_LIST", "ID", "SHORT_NAME"  );
        $lib_store_arr= return_library_arr( "SELECT ID, STORE_NAME FROM LIB_STORE_LOCATION", "ID", "STORE_NAME"  );
        $department_arr= return_library_arr( "SELECT ID,DEPARTMENT_NAME FROM LIB_DEPARTMENT WHERE STATUS_ACTIVE=1 AND IS_DELETED=0",'ID','DEPARTMENT_NAME');
        $location_arr= return_library_arr( "SELECT ID,LOCATION_NAME FROM LIB_LOCATION WHERE COMPANY_ID=$cbo_company_name and STATUS_ACTIVE=1 AND IS_DELETED=0",'ID','LOCATION_NAME');
        
       
       
        $approval_type = 0;
        $target_ids = $ref_id;
        $target_app_id_arr = explode(',',$target_ids);	
        $txt_alter_user_id=$user_id;
        $app_user_id=$user_id;	


        $sql="select A.ID,a.STORE_NAME,a.DEPARTMENT_ID,a.LOCATION_ID, b.ITEM_CATEGORY from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b where a.id=b.mst_id and a.id in($ref_id)";
        $sqlResult  = sql_select_arr($sql);

		// $sqlResult=$this->sql_select_arr( $sql );
        //return $sqlResult;
		foreach ($sqlResult as $row)
		{
			$matchDataArr[$row['ID']]=array('buyer'=>0,'brand'=>0,'item'=>$row['ITEM_CATEGORY'],'store'=>$row['STORE_NAME'],'department'=>$row['DEPARTMENT_ID'],'location_id'=>$row['LOCATION_ID']);
		} 
        //return $matchDataArr;
		
		//$matchDataArr[333]=array('buyer'=>0,'brand'=>0,'item'=>15,'store'=>358);

		$category_mixing_variable =  return_field_value("ALLOCATION","VARIABLE_SETTINGS_INVENTORY","COMPANY_NAME=$company_name AND VARIABLE_LIST=44 AND STATUS_ACTIVE=1 AND IS_DELETED=0 ORDER BY ID DESC ","ALLOCATION");

		$finalDataArr = get_final_user(array('company_id'=>$company_name,'page_id'=>$menu_id,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>$brand_arr,'lib_item_cat_arr'=>$item_cat_arr,'lib_store_arr'=>$lib_store_arr,'lib_department_id_arr'=>$department_arr,'match_data'=>$matchDataArr,'category_mixing'=>$category_mixing_variable,'lib_location_id_arr'=>$location_arr));
        //return $finalDataArr;
		$sequ_no_arr_by_sys_id = array();
		$user_sequence_no = "";
        if(isset($finalDataArr['final_seq']))
        {
            $sequ_no_arr_by_sys_id =$finalDataArr['final_seq'];
        }
		if(isset($finalDataArr['user_seq']))
        {
            $user_sequence_no = $finalDataArr['user_seq'][$app_user_id];
        }
		

        $mst_data_array = "";
        $mst_data_array_up = array();
        $is_mst_final_seq = array();
		
		$mst_field_array="ID, ENTRY_FORM, MST_ID,  SEQUENCE_NO,APPROVED_BY, APPROVED_DATE,INSERTED_BY,INSERT_DATE";
		$mst_field_array_up="IS_APPROVED*APPROVED_SEQU_BY"; 
		$id= get_next_id( "ID","APPROVAL_MST") ;
		foreach($target_app_id_arr as $mst_id)
		{		
			if($mst_data_array!=''){$mst_data_array.=",";}
			$mst_data_array.="(".$id.",1,".$mst_id.",".$user_sequence_no.",".$app_user_id.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."')"; 
			$id=$id+1;
			
			//mst data.......................
            if(!empty($finalDataArr['final_seq'][$mst_id]))
            {
                $approved=(max($finalDataArr['final_seq'][$mst_id])==$user_sequence_no)?1:3;
            }
            else
            {
                $approved=3;
            }

            if($user_sequence_no > 1)
            {
                $is_mst_final_seq[$mst_id] = $mst_id;
            }
           
			
			$mst_data_array_up[$mst_id] =explode(",",("".$approved.",".$user_sequence_no."")); 
		}
		
		 //print_r($data_array_up);die;
		
		
		$flag=1;
		
		//---------------------------------------------------------------History
		$reqs_ids=explode(",",$target_ids);

		$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,IS_SIGNING";  
		
		$i=0;
        $id= get_next_id( "ID","APPROVAL_HISTORY") ;
        
        $approved_no_array=array();
		$data_array='';
		foreach($reqs_ids as $val)
        {
            $approved_no =  return_field_value("MAX(APPROVED_NO) AS APPROVED_NO","APPROVAL_HISTORY","MST_ID='$val' AND ENTRY_FORM=1","APPROVED_NO");
            $approved_no=$approved_no+1;
        
            if($i!=0) $data_array.=",";
             
            $data_array.="(".$id.",1,".$val.",".$approved_no.",'".$user_sequence_no."',1,".$app_user_id.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."',1)";
            
            $approved_no_array[$val]=$approved_no;
                
            $id=$id+1;
            $i++;
        }
		//echo $data_array;die;

        $appr_data = array("USER_ID"=>$user_id,"SEQUENCE_NO" =>$user_sequence_no,"COMPANY_ID"=> $company_name,'NOTIFICATION_TYPE'=>0);

        //return push_all($ref_id,$entry_form,$appr_data);
		
		
		$approved_string="";
        foreach($approved_no_array as $key=>$value)
        {
            $approved_string.=" WHEN $key THEN $value";
        }
        
        $approved_string_mst="CASE id ".$approved_string." END";
        $approved_string_dtls="CASE mst_id ".$approved_string." END";
		
		$sql_insert="INSERT INTO INV_PUR_REQUISITION_MST_HIST(ID, HIST_MST_ID, APPROVED_NO, ENTRY_FORM, REQU_NO, REQU_NO_PREFIX, REQU_PREFIX_NUM, COMPANY_ID, ITEM_CATEGORY_ID, SUPPLIER_ID, LOCATION_ID, DIVISION_ID, DEPARTMENT_ID, SECTION_ID, REQUISITION_DATE, STORE_NAME, PAY_MODE, SOURCE, CBO_CURRENCY, DELIVERY_DATE, DO_NO, ATTENTION, REMARKS, TERMS_AND_CONDITION, MANUAL_REQ, READY_TO_APPROVE, IS_APPROVED, STATUS_ACTIVE, IS_DELETED, INSERTED_BY,INSERT_DATE,UPDATED_BY,UPDATE_DATE) 
		SELECT	
		'', ID, $approved_string_mst, ENTRY_FORM, REQU_NO, REQU_NO_PREFIX, REQU_PREFIX_NUM, COMPANY_ID, ITEM_CATEGORY_ID, SUPPLIER_ID, LOCATION_ID, DIVISION_ID, DEPARTMENT_ID, SECTION_ID, REQUISITION_DATE, STORE_NAME, PAY_MODE, SOURCE, CBO_CURRENCY, DELIVERY_DATE, DO_NO, ATTENTION, REMARKS, TERMS_AND_CONDITION, MANUAL_REQ, READY_TO_APPROVE, IS_APPROVED, STATUS_ACTIVE, IS_DELETED, INSERTED_BY,INSERT_DATE,UPDATED_BY,UPDATE_DATE FROM  INV_PURCHASE_REQUISITION_MST WHERE ID IN ($target_ids)";
		
		//echo $sql_insert;
		$sql_insert_dtls="INSERT INTO  INV_PUR_REQUISITION_DTLS_HIST(ID, APPROVED_NO, MST_ID, PRODUCT_ID, USER_CODE_MAINTAIN, REQUIRED_FOR, JOB_ID, JOB_NO, BUYER_ID, STYLE_REF_NO, COLOR_ID, COUNT_ID, COMPOSITION_ID, COM_PERCENT, YARN_TYPE_ID, YARN_INHOUSE_DATE, CONS_UOM, QUANTITY, RATE, AMOUNT, STOCK, REMARKS, INSERTED_BY, INSERT_DATE, UPDATED_BY, UPDATE_DATE, STATUS_ACTIVE, IS_DELETED) 
		SELECT	
		'', $approved_string_dtls, MST_ID, PRODUCT_ID, USER_CODE_MAINTAIN, REQUIRED_FOR, JOB_ID, JOB_NO, BUYER_ID, STYLE_REF_NO, COLOR_ID, COUNT_ID, COMPOSITION_ID, COM_PERCENT, YARN_TYPE_ID, YARN_INHOUSE_DATE, CONS_UOM, QUANTITY, RATE, AMOUNT, STOCK, REMARKS, INSERTED_BY, INSERT_DATE, UPDATED_BY, UPDATE_DATE, STATUS_ACTIVE, IS_DELETED FROM  INV_PURCHASE_REQUISITION_DTLS WHERE MST_ID IN ($target_ids)";

        $sql="SELECT COMPANY_ID,DEPARTMENT_ID,STORE_NAME FROM INV_PURCHASE_REQUISITION_MST WHERE id = $ref_id";
        $sql_res = sql_select_arr($sql);


        // $sql_res = $this->sql_select_arr("SELECT COMPANY_ID,DEPARTMENT_ID,STORE_NAME FROM INV_PURCHASE_REQUISITION_MST WHERE id = $ref_id");
        $company_name = "";
        $DEPARTMENT_ID = "";
        $STORE_NAME = "";
        foreach($sql_res as $rows)
        {
            $company_name = $rows['COMPANY_ID'];
            $STORE_NAME = $rows['STORE_NAME'];
            $DEPARTMENT_ID = $rows['DEPARTMENT_ID'];
        }
        $desc = $this->getDesc($ref_id);
        $approval_data = $this->get_purchase_requisition_approval_data($ref_id);
        $approval_parameter = array('DEPARTMENT'=>$DEPARTMENT_ID,'LOCATION'=>'','STORE_ID'=>$STORE_NAME,'approval_desc'=>$desc,'approval_data' => $approval_data,'title'=>'Pending Approval :: Purchase Requisition','is_commit'=>0);
       

       

        $this->db->trans_strict(TRUE);
        $this->db->trans_begin();
        try
        {
            $rID=sql_insert("APPROVAL_MST",$mst_field_array,$mst_data_array);
            if($rID!=1) 
            {
                throw new Exception($rID);
            }
            
            $rID1=execute_query(bulk_update_sql_statement( "INV_PURCHASE_REQUISITION_MST", "ID", $mst_field_array_up, $mst_data_array_up, $target_app_id_arr ));
            if($rID1!=1) 
            {
                throw new Exception($rID1);
            }

            $query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=1 and mst_id in ($target_ids)"; //die;
            $rID2=execute_query($query);
            if($rID2!=1) 
            {
                throw new Exception($rID2);
            }
            //echo "insert into approval_history $field_array values($data_array)";die;
            $rID3=sql_insert("approval_history",$field_array,$data_array);
            if($rID3!=1) 
            {
                throw new Exception($rID3);
            }
            $rID4=execute_query($sql_insert);
            if($rID4!=1) 
            {
                throw new Exception($rID4);
            }       
            $rID5=execute_query($sql_insert_dtls);
            if($rID5!=1) 
            {
                throw new Exception($rID5);
            } 

            $res_engine = notificationEngineForApps($ref_id,$company_name,1,$approval_parameter,$user_id);

            if($res_engine!=1)
            {
                throw new Exception($res_engine);
            }

            $notif_res =  insertNotificationData($ref_id,$entry_form,$user_id,$desc,1);
            if($notif_res != 1)
            {
                throw new Exception($notif_res);
            }

            $query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=1 and mst_id in ($target_ids)"; //die;
            $rID2=execute_query($query);
            if($rID2!=1) 
            {
                throw new Exception($rID2);
            }

            $appr_data = array("USER_ID"=>$user_id,"SEQUENCE_NO" =>$user_sequence_no,"COMPANY_ID"=> $company_name,'NOTIFICATION_TYPE'=>0,'approval_data' => $approval_data);
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

    public function unapprove_purchase_requisition_v2($user_id,$menu_id,$ref_id,$message ='')
    {
        $entry_form = 1;
        $sql="SELECT COMPANY_ID,BUYER_ID,IS_APPROVED,DEPARTMENT_ID,STORE_NAME FROM INV_PURCHASE_REQUISITION_MST WHERE id = $ref_id";
        $sql_res = sql_select_arr($sql);

        //$sql_res = $this->sql_select_arr("SELECT COMPANY_ID,BUYER_ID,IS_APPROVED,DEPARTMENT_ID,STORE_NAME FROM INV_PURCHASE_REQUISITION_MST WHERE id = $ref_id");
        $company_name = "";
        $DEPARTMENT_ID = "";
        $STORE_NAME = "";
        $company_name = "";
        foreach($sql_res as $rows)
        {
            $company_name = $rows['COMPANY_ID'];
            $cbo_company_name = $rows['COMPANY_ID'];
            $cbo_buyer_name = $rows['BUYER_ID'];
            $IS_APPROVED = $rows['IS_APPROVED'];
            $company_name = $rows['COMPANY_ID'];
            $STORE_NAME = $rows['STORE_NAME'];
            $DEPARTMENT_ID = $rows['DEPARTMENT_ID'];
            if($IS_APPROVED == 0)
            {
                return "not_approved";
            }
        }
        //return "SELECT ID FROM APPROVAL_MST FROM APPROVED_BY=$user_id and ENTRY_FORM = $entry_form and MST_ID  = $ref_id ";

        $CURRENT_APPROVAL_STATUS = return_field_value("ID","APPROVAL_MST","APPROVED_BY=$user_id and ENTRY_FORM = $entry_form and MST_ID  = $ref_id ","ID");
        if(empty($CURRENT_APPROVAL_STATUS))
        {
            return "not_approved";
        }
        
        $approval_parameter = array('DEPARTMENT'=>$DEPARTMENT_ID,'LOCATION'=>'','STORE_ID'=>$STORE_NAME);
        $desc = $this->getDesc($ref_id);

        $history_data_arr= return_library_arr( "SELECT ID, ID FROM APPROVAL_HISTORY WHERE CURRENT_APPROVAL_STATUS=1 AND ENTRY_FORM=1 AND MST_ID IN ($ref_id)",'ID','ID');
		$app_ids= implode(',',$history_data_arr);
		$pc_date_time = date("d-M-Y h:i:s A",time());  
        
        $approval_data = $this->get_purchase_requisition_approval_data($ref_id);
        $appr_data = array("USER_ID"=>'',"SEQUENCE_NO" =>'',"COMPANY_ID"=> '','NOTIFICATION_TYPE'=>0,'approval_data' => $approval_data);
        push_all($ref_id,$entry_form,$appr_data);
		

        $this->db->trans_strict(TRUE);
        $this->db->trans_begin();
        try
        {
            $rID = sql_multi_row_update("inv_purchase_requisition_mst","is_approved*ready_to_approve*APPROVED_SEQU_BY","0*0*0","id",$ref_id); 
            if($rID!=1) 
            {
                throw new Exception($rID);
            }


            $query="delete from approval_mst  WHERE entry_form=1 and mst_id in ($ref_id)";
            $rID2=execute_query($query); 
            if($rID2!=1) 
            {
                throw new Exception($rID2);
            }
            
            //-----------------------History
            $rID3 = sql_multi_row_update("inv_purchase_requisition_mst","is_approved*ready_to_approve",'0*2',"id",$ref_id);
            if($rID3!=1) 
            {
                throw new Exception($rID3);
            }
            
            $query="UPDATE approval_history SET current_approval_status=0,IS_SIGNING=0 WHERE entry_form=1 and mst_id in ($ref_id)";
            $rID4=execute_query($query);
            if($rID4!=1) 
            {
                throw new Exception($rID4);
            }
            
            if(!empty($app_ids))
            {
                $data = $user_id."*'".$pc_date_time."'*".$user_id."*'".$pc_date_time."'";
                $rID5 = sql_multi_row_update("approval_history","un_approved_by*un_approved_date*updated_by*update_date",$data,"id",$app_ids);
                if($rID5!=1) 
                {
                    throw new Exception($rID5);
                }
            }
            

            $query="DELETE FROM APPROVAL_NOTIFICATION_ENGINE  WHERE ENTRY_FORM=1 AND REF_ID IN ($ref_id) ";
            $rID6=execute_query($query); 

            if($rID6!=1)
            {
                throw new Exception($rID6);
            }

           // $cause = $this->saveCause($user_id,$menu_id,$ref_id,$entry_form,$message,0);
            // if($cause!=1)
            // {
            //     throw new Exception($cause);
            // }

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

    public function deny_approve_purchase_requisition_v2($user_id,$menu_id,$ref_id,$entry_form)
    {
        $sql="SELECT COMPANY_ID,BUYER_ID,IS_APPROVED,DEPARTMENT_ID,STORE_NAME,READY_TO_APPROVE FROM INV_PURCHASE_REQUISITION_MST WHERE id = $ref_id";
        $sql_res = sql_select_arr($sql);

        // $sql_res = $this->sql_select_arr("SELECT COMPANY_ID,BUYER_ID,IS_APPROVED,DEPARTMENT_ID,STORE_NAME,READY_TO_APPROVE FROM INV_PURCHASE_REQUISITION_MST WHERE id = $ref_id");
        foreach($sql_res as $rows)
        {
            $READY_TO_APPROVE = $rows['READY_TO_APPROVE'];
            if($READY_TO_APPROVE != 1)
            {
                return "not_ready_to_approved";
            }
        }

        $CURRENT_APPROVAL_STATUS =  return_field_value("CURRENT_APPROVAL_STATUS","APPROVAL_HISTORY","approved_by=$user_id and entry_form = $entry_form and mst_id = $ref_id ","CURRENT_APPROVAL_STATUS");
        if($CURRENT_APPROVAL_STATUS == 1)
        {

            return "already_approved";
        }
        $approval_data = $this->get_purchase_requisition_approval_data($ref_id);
        $appr_data = array("USER_ID"=>'',"SEQUENCE_NO" =>'',"COMPANY_ID"=> '','NOTIFICATION_TYPE'=>0,'approval_data' => $approval_data);
        push_all($ref_id, $entry_form, $appr_data);
        
        $this->db->trans_strict(TRUE);
        $this->db->trans_begin();
        try
        {
            
            $rID = sql_multi_row_update("inv_purchase_requisition_mst","is_approved*ready_to_approve*APPROVED_SEQU_BY","0*0*0","id",$ref_id); 
            
            if($rID!=1) 
            {
                throw new Exception($rID);
            }
    
            $query="delete from approval_mst  WHERE entry_form=$entry_form and mst_id in ($ref_id)";
            $rID2=execute_query($query); 
            if($rID2!=1) 
            {
                throw new Exception($rID2);
            }
            
            $rID3= sql_multi_row_update("inv_purchase_requisition_mst","is_approved*ready_to_approve",'0*2',"id",$ref_id,0);
            if($rID3!=1) 
            {
                throw new Exception($rID3);
            }

            $query="UPDATE approval_history SET current_approval_status=0,IS_SIGNING=0 WHERE entry_form=$entry_form and mst_id in ($ref_id)";
            $rID4=execute_query($query);
            if($rID4!=1) 
            {
                throw new Exception($rID4);
            }

            $query="DELETE FROM APPROVAL_NOTIFICATION_ENGINE  WHERE ENTRY_FORM=$entry_form AND REF_ID IN ($ref_id) ";
            $rID6=execute_query($query); 

            if($rID6!=1)
            {
                throw new Exception($rID6);
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

    public function saveCause($user_id,$menu_id,$ref_id,$entry_form,$message,$approval_type = 0)
    {
        $this->db->trans_strict(TRUE);
        $this->db->trans_begin();
        try
        {
            $approved_no_history =  return_field_value("APPROVED_NO","APPROVAL_HISTORY","ENTRY_FORM=1 AND MST_ID=$ref_id AND APPROVED_BY=$user_id","APPROVED_NO");
			$approved_no_cause =  return_field_value("APPROVAL_NO","FABRIC_BOOKING_APPROVAL_CAUSE","ENTRY_FORM=1 AND BOOKING_ID=$ref_id AND USER_ID=$user_id AND APPROVAL_TYPE=$approval_type","APPROVAL_NO");
            $pc_date_time = date("d-M-Y h:i:s A",time());
			

			if($approved_no_history=="" && $approved_no_cause=="")
			{
				//echo "insert"; die;

				$id_mst= get_next_id("ID","FABRIC_BOOKING_APPROVAL_CAUSE") ;

				$field_array="ID,PAGE_ID,ENTRY_FORM,USER_ID,BOOKING_ID,APPROVAL_TYPE,APPROVAL_NO,APPROVAL_CAUSE,INSERTED_BY,INSERT_DATE,STATUS_ACTIVE,IS_DELETED";
				$data_array="(".$id_mst.",".$menu_id.",".$entry_form.",".$user_id.",".$ref_id." ,".$approval_type.",0,'".str_replace("'","",$message)."',".$user_id.",'".$pc_date_time."',1,0)";
				$rID=sql_insert("FABRIC_BOOKING_APPROVAL_CAUSE",$field_array,$data_array);
				if($rID!=1)
                {
                    throw new Exception($rID);
                }
			}
			else if($approved_no_history=="" && $approved_no_cause!="")
			{

				$id_cause =  return_field_value("MAX(ID) AS ID","FABRIC_BOOKING_APPROVAL_CAUSE","ENTRY_FORM=$entry_form AND BOOKING_ID=$ref_id AND USER_ID=$user_id AND APPROVAL_TYPE=$approval_type","ID");

				$field_array = "PAGE_ID*ENTRY_FORM*USER_ID*BOOKING_ID*APPROVAL_TYPE*APPROVAL_NO*APPROVAL_CAUSE*UPDATED_BY*UPDATE_DATE*STATUS_ACTIVE*IS_DELETED";
				$data_array = "".$menu_id."*".$entry_form."*".$user_id."*".$ref_id."*".$approval_type."*0*'".str_replace("'","",$message)."'*".$user_id."*'".$pc_date_time."'*1*0";

				$rID= sql_update("FABRIC_BOOKING_APPROVAL_CAUSE",$field_array,$data_array,"ID",$id_cause);
                if($rID!=1)
                {
                    throw new Exception($rID);
                }
				
			}
			else if($approved_no_history!="" && $approved_no_cause!="")
			{
				$max_appv_no_his =  return_field_value("MAX(APPROVED_NO) AS APPROVED_NO","APPROVAL_HISTORY","ENTRY_FORM=1 AND MST_ID=$ref_id AND APPROVED_BY=$user_id","APPROVED_NO");
				$max_appv_no_cause =  return_field_value("MAX(APPROVAL_NO) AS APPROVAL_NO","fABRIC_BOOKING_APPROVAL_CAUSE","ENTRY_FORM=1 AND BOOKING_ID=$ref_id aND USER_ID=$user_id AND APPROVAL_TYPE=$approval_type","APPROVAL_NO");

				if($max_appv_no_his!=$max_appv_no_cause)
				{
					$id_mst = get_next_id( "ID", "FABRIC_BOOKING_APPROVAL_CAUSE") ;

					$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
					$data_array="(".$id_mst.",".$menu_id.",".$entry_form.",".$user_id.",".$ref_id." ,".$approval_type.",".$max_appv_no_his.",'".str_replace("'","",$message)."',".$user_id.",'".$pc_date_time."',1,0)";
					
					$rID=sql_insert("FABRIC_BOOKING_APPROVAL_CAUSE",$field_array,$data_array);
                    if($rID!=1)
                    {
                        throw new Exception($rID);
                    }
				}
				else if($max_appv_no_his==$max_appv_no_cause)
				{
					$id_cause =  return_field_value("MAX(ID) AS ID","FABRIC_BOOKING_APPROVAL_CAUSE","ENTRY_FORM=1 AND BOOKING_ID=$ref_id AND USER_ID=$user_id AND APPROVAL_TYPE=$approval_type","ID");

					$field_array="PAGE_ID*ENTRY_FORM*USER_ID*BOOKING_ID*APPROVAL_TYPE*APPROVAL_NO*APPROVAL_CAUSE*UPDATED_BY*UPDATE_DATE*STATUS_ACTIVE*IS_DELETED";
					$data_array="".$menu_id."*".$entry_form."*".$user_id."*".$ref_id."*".$approval_type."*".$max_appv_no_his."*'".str_replace("'","",$message)."'*".$user_id."*'".$pc_date_time."'*1*0";

					 $rID= sql_update("FABRIC_BOOKING_APPROVAL_CAUSE",$field_array,$data_array,"ID","".$id_cause."");

                     if($rID!=1)
                     {
                         throw new Exception($rID);
                     }
				}
			}
			else if($approved_no_history!="" && $approved_no_cause=="")
			{
				$max_appv_no_his =  return_field_value("MAX(APPROVED_NO) as APPROVED_NO","APPROVAL_HISTORY","ENTRY_FORM=1 AND MST_ID=$ref_id AND APPROVED_BY=$user_id","APPROVED_NO");
				$max_appv_no_cause =  return_field_value("MAX(APPROVAL_NO) AS APPROVAL_NO","fabric_booking_approval_cause","entry_form=$entry_form and booking_id=$ref_id and user_id=$user_id and approval_type=$approval_type","APPROVAL_NO");

				if($max_appv_no_his!=$max_appv_no_cause)
				{
					$id_mst= get_next_id( "ID", "FABRIC_BOOKING_APPROVAL_CAUSE") ;

					$field_array="ID,PAGE_ID,ENTRY_FORM,USER_ID,BOOKING_ID,APPROVAL_TYPE,APPROVAL_NO,APPROVAL_CAUSE,INSERTED_BY,INSERT_DATE,STATUS_ACTIVE,IS_DELETED";
					$data_array="(".$id_mst.",".$menu_id.",".$entry_form.",".$user_id.",".$ref_id." ,".$approval_type.",".$max_appv_no_his.",'".str_replace("'","",$message)."',".$user_id.",'".$pc_date_time."',1,0)";
                    $rID=sql_insert("FABRIC_BOOKING_APPROVAL_CAUSE",$field_array,$data_array);
                    if($rID!=1)
                    {
                        throw new Exception($rID);
                    }
				}
				else if($max_appv_no_his==$max_appv_no_cause)
				{
					$id_cause= get_next_id("MAX(ID) AS ID","FABRIC_BOOKING_APPROVAL_CAUSE","ENTRY_FORM=1 AND BOOKING_ID=$ref_id AND USER_ID=$user_id AND APPROVAL_TYPE=$approval_type","ID");

					$field_array="PAGE_ID*ENTRY_FORM*USER_ID*BOOKING_ID*APPROVAL_TYPE*APPROVAL_NO*APPROVAL_CAUSE*UPDATED_BY*UPDATE_DATE*STATUS_ACTIVE*IS_DELETED";
					$data_array="".$menu_id."*".$entry_form."*".$user_id."*".$ref_id."*".$approval_type."*".$max_appv_no_his."*'".str_replace("'","",$message)."'*".$user_id."*'".$pc_date_time."'*1*0";

                     $rID= sql_update("FABRIC_BOOKING_APPROVAL_CAUSE",$field_array,$data_array,"ID","".$id_cause."");

                     if($rID!=1)
                     {
                         throw new Exception($rID);
                     }
					
				}
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
        catch( Exception $e)
        {
            $this->db->trans_rollback();
            return $e->getMessage();
        }
    }

}