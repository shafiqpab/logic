<?php

class Knit_precosting_approval_notification extends CI_Model {
	function __construct() {
		error_reporting(0);
		parent::__construct();
	}

    public function  bulkUpdateSqlStatement($table, $id_column, $update_column, $data_values, $id_count)
    {
        $field_array = explode("*", $update_column);
        //$id_count=explode("*",$id_count);
        //$data_values=explode("*",$data_values);
        //print_r($data_values);die;
        $sql_up = "";
        $sql_up .= "UPDATE $table SET ";
    
        for ($len = 0; $len < count($field_array); $len++) {
            $sql_up .= " " . $field_array[$len] . " = CASE $id_column ";
            for ($id = 0; $id < count($id_count); $id++) {
                if (trim($data_values[$id_count[$id]][$len]) == "") {
                    $sql_up .= " when " . $id_count[$id] . " then  '" . $data_values[$id_count[$id]][$len] . "'";
                } else {
                    $sql_up .= " when " . $id_count[$id] . " then  " . $data_values[$id_count[$id]][$len] . "";
                }
    
            }
            if ($len != (count($field_array) - 1)) {
                $sql_up .= " END, ";
            } else {
                $sql_up .= " END ";
            }
    
        }
        $sql_up .= " where $id_column in (" . implode(",", $id_count) . ")";
        return $sql_up;
    }

    public function returnLibraryArray($sql,$key,$value)
    {
        $result = $this->sqlSelect($sql);
        $data = array();
		foreach($result as $row)
        {
            if(isset($row[$key]))
            {
                $data[$row[$key]]=$row[$value];
            }
		}
		return $data;
    }

    public function sqlSelect($sql)
	{
		$result = $this->db->query($sql)->result();
		$res = array();

        foreach($result as $row)
        {
            $res[] = (array) $row;
        }
        return $res;
	}

    public function returnFieldValue( $field_name, $table_name, $query_cond, $return_fld_name )
    {
		if ($return_fld_name=="") $return_fld_name=$field_name;
	
		$sql="select ".strtoupper($field_name)." from ".$table_name." where ".$query_cond;

        $result = $this->db->query($sql)->result();
		foreach($result as $row)
		{
            $row = (array) $row;
            if($row[strtoupper($return_fld_name)]!="") return $row[strtoupper($return_fld_name)];
            else return false;
        }
	}

    public function sqlInsert( $strTable, $arrNames, $arrValues )
    {
        
        $tmpv=explode(")",$arrValues);
        if(count($tmpv)>2)
            $strQuery= "INSERT ALL \n";
        else
            $strQuery= "INSERT  \n";

        for($i=0; $i<count($tmpv)-1; $i++)
        {
            if( strpos(trim($tmpv[$i]), ",")==0)
                $tmpv[$i]=substr_replace($tmpv[$i], " ", 0, 1);
            $strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$tmpv[$i].") \n";
        }

       if(count($tmpv)>2) $strQuery .= "SELECT * FROM dual";
    
       $result = $this->db->query($strQuery);

       
       if ($result)
       {
         return 1;
       }
       else
       {
            $error = $this->db->error();
            return "Error Code: " . $error['code'] . "\n"."Error Message: " . $error['message'];
       }
    }

    public function executeQuery($sql)
    {
        $result = $this->db->query($sql);

       if ($result)
       {
            return 1;
       }
       else
       {
            $error = $this->db->error();
            return "Error Code: " . $error['code'] . "\n"."Error Message: " . $error['message'];
       }
    }

    function sqlUpdate($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$return_query=0)
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

            //return $strQuery;die;
      
        $result = $this->db->query($strQuery);

       
        if ($result)
        {
            return 1;
        }
        else
        {
            $error = $this->db->error();
            return "Error Code: " . $error['code'] . "\n"."Error Message: " . $error['message'];
        }
    }

    public function sqlMultirowUpdate($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues)
    {
        $strQuery = "UPDATE ".$strTable." SET ";
        $arrUpdateFields=explode("*",$arrUpdateFields);
        $arrUpdateValues=explode("*",$arrUpdateValues);
    
    
        if(is_array($arrUpdateFields))
        {
            $arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
            $Arraysize = count($arrayUpdate);
            $i = 1;
            foreach($arrayUpdate as $key=>$value):
                $strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value." WHERE ";
                $i++;
            endforeach;
        }
        else
        {
            $strQuery .= $arrUpdateFields."=".$arrUpdateValues." WHERE ";
        }
        $strQuery .= $arrRefFields." in (".$arrRefValues.")";
        $result = $this->db->query($strQuery);

       if ($result)
       {
         return 1;
       }
       else
       {
         return 0;
       }
    }

    public function getNextId($column,$table)
    {
        $column = strtoupper($column);
        $sql = "SELECT max(".$column.") as ".$column." from ".$table;
        $result = $this->sqlSelect($sql);
        if(!empty($result))
        {
            $nextId = $result[0][$column] + 1;
        }
        else {
            $nextId = 1;
        }
        
        return $nextId;
    }

    public function  getFinalUser($parameterArr=array())
    {
        
        $lib_buyer_arr=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));
	

        $brandSql = "select ID, BRAND_NAME,BUYER_ID from lib_buyer_brand where STATUS_ACTIVE=1 and IS_DELETED=0";
        $brandSqlRes=sql_select($brandSql);
        //print_r($parameterArr);die;
        foreach($brandSqlRes as $row){
            $buyer_wise_brand_id_arr[$row->BUYER_ID][$row->ID] = $row->ID;
        }
        
        
        //Electronic app setup data.....................
        $sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT,GROUP_NO FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = ".$parameterArr['company_id']." AND ENTRY_FORM =". $parameterArr['entry_form']." AND IS_DELETED = 0  order by SEQUENCE_NO";
            //echo $sql;die;
        $sql_result=sql_select($sql);
        foreach($sql_result as $rows){
            $userDataArr[$rows->USER_ID][$rows->BUYER_ID]=$rows->BUYER_ID;
            $userDataArr[$rows->USER_ID][$rows->BRAND_ID]=$rows->BRAND_ID;
            
    
            if($userDataArr[$rows->USER_ID][$rows->BUYER_ID]=='' || $userDataArr[$rows->USER_ID][$rows->BUYER_ID] == 0){
                $userDataArr[$rows->USER_ID][$rows->BUYER_ID]=$lib_buyer_arr;
            }
            
    
            if($userDataArr[$rows->USER_ID][$rows->BRAND_ID]=='' || $userDataArr[$rows->USER_ID][$rows->BRAND_ID] == 0){
                $tempBrandArr = array();
                foreach(explode(',',$userDataArr[$rows->USER_ID][$rows->BUYER_ID]) as $buyer_id){
                    if(count($buyer_wise_brand_id_arr[$buyer_id])){$tempBrandArr[]= implode(',',$buyer_wise_brand_id_arr[$buyer_id]);}
                }
                $userDataArr[$rows->USER_ID][$rows->BRAND_ID]=implode(',',$tempBrandArr);
            }
             

            $usersDataArr[$rows->USER_ID][$rows->BUYER_ID]=explode(',',$userDataArr[$rows->USER_ID][$rows->BUYER_ID]);
            $usersDataArr[$rows->USER_ID][$rows->BRAND_ID]=explode(',',$userDataArr[$rows->USER_ID][$rows->BRAND_ID]);
            
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
        

        
        return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr,'user_group'=>$userGroupDataArr,'group_bypass_no_data_arr'=>$groupBypassNoDataArr);
        
  
        
    }

    public function notificationEngineForApps($id,$company,$entry_form,$array_param = array(),$user_id =0)
    {
        $user_ids = "";
        $user_ids = $this->getElectronicApprovalUser($company,$entry_form,$array_param,$user_id);
        //return  $user_ids;
        if(!empty($user_ids))
        {
            $users = explode(",",$user_ids);
                $data = $this->getDataForApps($id,$entry_form,$user_id);
                //return $data;

                $desc = "";
                foreach($data as $row)
                {
                    $desc = $row['DESC'];
                }
            
                $res =  insertNotificationData($id,$entry_form,$user_ids,$desc);
            if($res == 1)
            {
                    if($entry_form == 1)
                    {
                        $title = "Pending Approval :: Purchase Requisition";
                    }
                   
                    $user_email =$this->returnLibraryArray("SELECT ID,USER_EMAIL FROM USER_PASSWD WHERE ID IN(".$user_ids.")  AND USER_EMAIL IS NOT NULL","ID","USER_EMAIL");
                    
                    foreach($users as $user_id)
                    {
                        if(!empty($user_email[$user_id]) && filter_var($user_email[$user_id], FILTER_VALIDATE_EMAIL))
                        {
                            $this->sendMail($user_email[$user_id],$title,$data);
                        }
                        
                        $json = json_encode($data);
                        $result  = $this->sqlSelect("select FCM_TOKEN from APPROVAL_NOTI_USER_DEVICES where user_id = $user_id order by ID DESC");
                       
                        foreach($result as $row)
                        {
                            $key = $row['FCM_TOKEN'];
                            $this->pushToFCM($key,$title,$json,1);
                        }
                    }
            }
            return $res == 1;
        }
        else return 1;
    }
    public function getElectronicApprovalUser($company,$entry_form,$array_param = array(),$user_id ='') 
    {
        if(empty($company) || empty($entry_form)) return "";

        $extra_cond = "";
        $BRAND_ID       = $array_param['BRAND_ID'];
        $BUYER_ID       = $array_param['BUYER_ID'];
        $DEPARTMENT     = $array_param['DEPARTMENT'];
        $SUPPLIER_ID    = $array_param['SUPPLIER_ID'];
        $PARTY_ID       = $array_param['PARTY_ID'];
        $LOCATION       = $array_param['LOCATION'];
        $ITEM_CATEGORY  = $array_param['ITEM_CATEGORY'];

       
       
        if(!empty($BRAND_ID))
        {
            $extra_cond .= " AND  ( BRAND_ID like '%,$BRAND_ID,%' OR BRAND_ID like '%,$BRAND_ID' OR BRAND_ID like '$BRAND_ID,%' OR BRAND_ID = '$BRAND_ID' OR BRAND_ID IS NULL) ";
        }
        if(!empty($BUYER_ID))
        {
            $extra_cond .= " AND  ( BUYER_ID like '%,$BUYER_ID,%' OR BUYER_ID like '%,$BUYER_ID' OR BUYER_ID like '$BUYER_ID,%' OR BUYER_ID = '$BUYER_ID' OR BUYER_ID IS NULL) ";
        }
        if(!empty($DEPARTMENT))
        {
            $extra_cond .= " AND  ( DEPARTMENT like '%,$DEPARTMENT,%' OR DEPARTMENT like '%,$DEPARTMENT' OR DEPARTMENT like '$DEPARTMENT,%' OR DEPARTMENT = '$DEPARTMENT' OR DEPARTMENT IS NULL) ";
        }
        if(!empty($SUPPLIER_ID))
        {
            $extra_cond .= " AND  ( SUPPLIER_ID like '%,$SUPPLIER_ID,%' OR SUPPLIER_ID like '%,$SUPPLIER_ID' OR SUPPLIER_ID like '$SUPPLIER_ID,%' OR SUPPLIER_ID = '$SUPPLIER_ID' OR SUPPLIER_ID IS NULL) ";
        }
        if(!empty($PARTY_ID))
        {
            $extra_cond .= " AND  ( PARTY_ID like '%,$PARTY_ID,%' OR PARTY_ID like '%,$PARTY_ID' OR PARTY_ID like '$PARTY_ID,%' OR PARTY_ID = '$PARTY_ID' OR PARTY_ID IS NULL) ";
        }
        if(!empty($LOCATION))
        {
           $extra_cond .= " AND  ( LOCATION like '%,$LOCATION,%' OR LOCATION like '%,$LOCATION' OR LOCATION like '$LOCATION,%' OR LOCATION = '$LOCATION' OR LOCATION IS NULL) ";
    
        }
        if(!empty($ITEM_CATEGORY))
        {
            $extra_cond .= " AND  (";
            $categories  = explode(",",$ITEM_CATEGORY);
            $sl = 0;
            foreach($categories as $category_id)
            {
                if($sl > 0) $extra_cond .= " OR ";
                $extra_cond .= "  ( ITEM_CATEGORY like '%,$category_id,%' OR ITEM_CATEGORY like '%,$category_id' OR ITEM_CATEGORY like '$category_id,%' OR ITEM_CATEGORY = '$category_id' ) ";
                $sl++;
            }
            $extra_cond .= "  OR ITEM_CATEGORY IS NULL )";
        }

        if(!empty($user_id))
        {
            $SEQUENCE_NO = $this->returnFieldValue("SEQUENCE_NO","ELECTRONIC_APPROVAL_SETUP","COMPANY_ID=$company AND ENTRY_FORM=$entry_form $extra_cond AND IS_DELETED = 0 and  USER_ID in ($user_id)","SEQUENCE_NO");
       

            if(!empty($SEQUENCE_NO ))
            {
                $extra_cond .= " AND SEQUENCE_NO > $SEQUENCE_NO ";
            }
            $extra_cond .= " AND USER_ID NOT IN ( $user_id ) ";
        }

        $sql_elec = "SELECT SEQUENCE_NO,BYPASS,USER_ID,GROUP_NO FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID=$company AND ENTRY_FORM=$entry_form $extra_cond AND IS_DELETED = 0 ORDER BY SEQUENCE_NO ASC";
        //return $sql_elec;
    
        $data_array=$this->sqlSelect($sql_elec);
        $userIdInSetup = array();
        foreach($data_array as $row)
        {
            $userIdInSetup[$row['USER_ID']] = $row['USER_ID'];
        }
        $userIds="";
        $user_cred_sql = array();
        if(count($userIdInSetup) > 0)
        {
            $sql_cred = "SELECT UNIT_ID AS COMPANY_ID, STORE_LOCATION_ID, ITEM_CATE_ID, SUPPLIER_ID,BUYER_ID,COMPANY_LOCATION_ID,DEPARTMENT_ID,SUPPLIER_ID,BRAND_ID,ID FROM USER_PASSWD WHERE ID in (".implode(",",$userIdInSetup).")";
            //return $sql_cred;
            $user_cred_sql = $this->sqlSelect($sql_cred);
        }
        
    
        $user_cred_store = array();
        
    
        foreach($user_cred_sql as $row)
        {
           
            if(!empty($row['STORE_LOCATION_ID']))
            {
                $stores = explode(",",$row['STORE_LOCATION_ID']);
                foreach($stores as $store_id)
                {
                    $user_cred_store[$row['ID']][$store_id] = $store_id;
                }
            }
            
        }

        $prev_group = "";
        
        foreach($data_array as $row)
        {   
           
            $flag = true; 
            if(!empty($array_param['STORE_ID']))
            {
                if(count($user_cred_store[$row['USER_ID']]) > 0 && empty($user_cred_store[$row['USER_ID']][$array_param['STORE_ID']]))
                {
                    $flag = false;
                }
            }
           
            
            if($prev_group == "" || $prev_group == $row['GROUP_NO'])
            {
                if($row['BYPASS']==1 && $flag == true)
                {
                    $userIds .=$row['USER_ID'].","; 
                    $prev_group = $row['GROUP_NO'];
                }
                elseif($row['BYPASS']==2 && $flag == true)
                {
                    $userIds .=$row['USER_ID'].",";
                    $prev_group = $row['GROUP_NO'];
                    break;
                }
            }
        }
        return  rtrim($userIds,",");  
    }
    public function getDataForApps($id,$entry_form,$user_id='')
    {
        if($entry_form == 1) //Purchase Requisition Approval
        {
            return $this->getPurchaseRequisitionApprovalData($id,$user_id);
            
        }
    }
    public function pushToFCM($key,$title,$data,$type = '')
    {
        
        $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
        $server_key ='AAAA71zSyIc:APA91bFxBmJaK59r4T-DIMvSbyTWPalRf5Q9BcV2JaTmefpzRH90NIaOudwmFi6HpUbpjvgB9pLyaborMPF9L6iO7r5NOI5CEh9ZxtLOzLKFL0EI53EmG7ynym4H_r9SDwq1Da0wWJmj';
        
        $headers = array(
                        'Authorization:key=' .$server_key,
                        'Content-Type:application/json'
                        );

        $arr = [
                    'data'=> [
                        'title'=> $title,
                        'body'=> $data,
                         'type' => $type
                        ],
                    "to" => $key
               ];
    
        $payload = json_encode($arr);
    
        $curl_session = curl_init();
        curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
        curl_setopt($curl_session, CURLOPT_POST, true);
        curl_setopt($curl_session, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
        curl_setopt($curl_session, CURLOPT_POSTFIELDS, $payload);
    
    
        $result = curl_exec($curl_session);
        curl_close($curl_session);
        return $result;
    }

    public function pushAll($ref_id,$entry_form,$array_param = array())
    {
        $cond = "";
        $user_id = $array_param['USER_ID'];
        $notification_type = $array_param['NOTIFICATION_TYPE'];
        $user_arr = array();
        if(!empty($user_id) )
        {
            $user_arr[ $user_id] =  $user_id;
            $SEQUENCE_NO = $array_param['SEQUENCE_NO'];
            $COMPANY_ID = $array_param['COMPANY_ID'];
            $cond = " AND USER_ID IN ( SELECT USER_ID FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID=$COMPANY_ID AND ENTRY_FORM=$entry_form  AND IS_DELETED = 0 AND SEQUENCE_NO <$SEQUENCE_NO)";
        }
        $users=$this->sqlSelect("SELECT USER_ID FROM APPROVAL_NOTIFICATION_ENGINE  WHERE ENTRY_FORM=$entry_form AND REF_ID IN ($ref_id) $cond");

       

        foreach($users as $row)
        {
            $user_arr[$row['USER_ID']] = $row['USER_ID'];
        }
       

        if(count($user_arr) > 0)
        {
            
            $data = $this->getDataForApps($ref_id,$entry_form,$user_id);
            $json = json_encode($data);
            if($entry_form == 1)
            {
                $title = "";
            }
            $result  = $this->sqlSelect("select FCM_TOKEN from APPROVAL_NOTI_USER_DEVICES where user_id IN (".implode(",",$user_arr).") order by ID DESC");
            $flag = 1;
            foreach($result as $row)
            {
                $key = $row['FCM_TOKEN'];
                $ret = $this->pushToFCM($key,$title,$json,0);
                if($flag == 0 || !isset($ret))
                {
                    $flag = 0;
                }
            }

            if(!empty($cond))
            {
                $this->db->trans_begin();
                $res = $this->executeQuery("DELETE APPROVAL_NOTIFICATION_ENGINE  WHERE ENTRY_FORM=$entry_form AND REF_ID IN ($ref_id) $cond");
                if($res !=1)
                {
                    $this->db->trans_rollback();
                    return 0;
                }
                else
                {
                    $this->db->trans_commit();
                }
                return 1;
            }
            return $flag;
        }
    }

    public function getPurchaseRequisitionApprovalData($ref_id,$user_id='')
	{
        $USER_FULL_NAME = "";
       if(!empty($user_id))
       {
         $USER_FULL_NAME = $this->returnFieldValue("USER_FULL_NAME","USER_PASSWD","id=$user_id","USER_FULL_NAME");
       }

       $MENU_ID = $this->returnFieldValue("PAGE_ID AS MENU_ID","ELECTRONIC_APPROVAL_SETUP","entry_form=1","MENU_ID");
       

        
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

        $result = $this->sqlSelect($sql);

        $data_arr = array();

        $priority_array=array(1=>"Normal",2=>"Urgent",3=>"Critical");
        
        foreach ($result as $row)
        {
            if(!empty( $USER_FULL_NAME))  $USER_FULL_NAME ="\nForwarded By : ".$USER_FULL_NAME;
            else $USER_FULL_NAME ="\nInserted By : ".$row['USER_FULL_NAME'];
            $desc = "Company: ".$row['COMPANY_NAME']." Loc: ".$row['LOCATION_NAME']."\nReq. No: ".$row['REQU_PREFIX_NUM']." Req. date: ".date('d-m-Y',strtotime($row['REQUISITION_DATE']))."\nPriority: ".$priority_array[$row['PRIORITY_ID']]." Store : ".$row['STORE_NAME']."\nCategory: ".implode(",",array_unique(explode(",",$row['CATEGORY'])))."\nReq. For : ".$row['REQ_FOR']."\nReq. Value: ".$row['AMOUNT'].$USER_FULL_NAME;
            $data_arr[] = array(
                'ID' => $row['ID'],
                'DATE' => $row['REQUISITION_DATE'],
                'DELIVERY_DATE' => $row['DELIVERY_DATE'],
                'COMPANY' => $row['COMPANY_NAME'],
                'BUYER' => '',
                'SYS_NUMBER' => $row['REQU_NO'],
                'SYS_DEF' => '',
                'DESC' => $desc,
                'MENU_ID'=>$MENU_ID
            );
        }
        
        return $data_arr;
	}
    public function getDesc($entry_form,$ref_id,$user_id='')
    {
        $desc ='';
        if($entry_form == 1)
        {
            $MENU_ID = $this->returnFieldValue("PAGE_ID AS MENU_ID","ELECTRONIC_APPROVAL_SETUP","entry_form=$entry_form","MENU_ID");
            $USER_FULL_NAME = "";
            if(!empty($user_id))
            {
              $USER_FULL_NAME = $this->returnFieldValue("USER_FULL_NAME","USER_PASSWD","id=$user_id","USER_FULL_NAME");
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
    
            $result = $this->sqlSelect($sql);
    
            $desc = "";
    
            $priority_array=array(1=>"Normal",2=>"Urgent",3=>"Critical");
            foreach ($result as $row)
            {
                if(!empty( $USER_FULL_NAME))  $USER_FULL_NAME ="\nForwarded By : ".$USER_FULL_NAME;
                else $USER_FULL_NAME ="\nInserted By : ".$row['USER_FULL_NAME'];
                return $desc = "Company: ".$row['COMPANY_NAME'].", Loc: ".$row['LOCATION_NAME']."\nReq. No: ".$row['REQU_PREFIX_NUM'].", Req. date: ".date('d-m-Y',strtotime($row['REQUISITION_DATE']))."\nPriority: ".$priority_array[$row['PRIORITY_ID']].", Store : ".$row['STORE_NAME']."\nCategory: ".implode(",",array_unique(explode(",",$row['CATEGORY'])))."\nReq. For : ".$row['REQ_FOR']."\nReq. Value: ".number_format($row['AMOUNT'],2,".","").$USER_FULL_NAME;
                
            }
        }
        return $desc;
    }


    public function insert_fcm_token($user_id,$device_id,$fcm_token)
	{
		$insert_date = date("d-M-Y");
		$this->db->trans_begin();
        $FCM_TOKEN=$this->returnFieldValue("FCM_TOKEN AS FCM_TOKEN","APPROVAL_NOTI_USER_DEVICES","USER_ID=$user_id and DEVICE_ID = '".$device_id."'","FCM_TOKEN");
        if(empty($FCM_TOKEN))
        {
            $ID = $this->getNextId('ID','APPROVAL_NOTI_USER_DEVICES');
            $fcm_token_data = array(
                array(
                    "ID" => $ID,
                    "USER_ID" => $user_id,
                    "DEVICE_ID" => $device_id,
                    "FCM_TOKEN" => $fcm_token,
                    "INSERT_DATE" => $insert_date,
                )
            );
    
            $this->db->insert_batch("APPROVAL_NOTI_USER_DEVICES", $fcm_token_data);
        }
        else 
        {
           $res = $this->executeQuery("UPDATE APPROVAL_NOTI_USER_DEVICES SET FCM_TOKEN ='".$fcm_token."' WHERE  USER_ID = $user_id and DEVICE_ID = '".$device_id."'");
           if($res !=1)
           {
                $this->db->trans_rollback();
                return 0;
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

	public function get_approval_menu_by_privilege($user_id)
	{
       
		$menu_sql = "SELECT A.MENU_NAME          AS MENU,
                            A.F_LOCATION         AS MENU_LINK,
                            A.M_MENU_ID          AS MENU_ID,
                            C.USER_FULL_NAME     AS FULL_NAME,
                            C.ID                 AS USER_LOGIN_ID,
                            A.SLNO               AS SLNO,
                            D.IS_SEEN            AS IS_SEEN,
                            COUNT (D.ID)         AS NOTIFICATIONS,
                            E.IS_ACTIVE
                    FROM MAIN_MENU A
                            INNER JOIN USER_PRIV_MST B ON A.M_MENU_ID = B.MAIN_MENU_ID
                            INNER JOIN USER_PASSWD C ON B.USER_ID = C.ID
                            LEFT JOIN APPROVAL_NOTIFICATION_ENGINE D
                                ON     A.M_MENU_ID = D.M_MENU_ID
                                AND D.USER_ID = C.ID
                                AND D.IS_APPROVED != 1
                                AND D.IS_SEEN != 1
                                AND D.STATUS_ACTIVE = 1
                            LEFT JOIN APPROVAL_NOTI_MENU_SETTING E
                                ON  A.M_MENU_ID  = E.MENU_ID
                                    AND E.USER_ID = $user_id

                    WHERE     B.USER_ID = ?
                            AND A.STATUS = 1
                            AND A.M_MODULE_ID = 12
                            AND A.IS_MOBILE_MENU = 1
                            AND B.VALID = 1
                    GROUP BY A.MENU_NAME,
                            A.F_LOCATION,
                            A.M_MENU_ID,
                            C.USER_FULL_NAME,
                            C.ID,
                            A.SLNO,
                            D.IS_SEEN,
                            E.IS_ACTIVE
                    ORDER BY A.SLNO ASC";

				$menu_result = $this->db->query($menu_sql, array($user_id))->result();

				$menu_arr = array();

				if (!empty($menu_result)) {
					foreach ($menu_result as $menu) {
						$menu_arr[] = array(
							'MENU' => $menu->MENU,
							'MENU_LINK' => $menu->MENU_LINK,
							'MENU_ID' => $menu->MENU_ID,
							'FULL_NAME' => $menu->FULL_NAME,
							'USER_LOGIN_ID' => $menu->USER_LOGIN_ID,
							'SLNO' => $menu->SLNO,
							'NOTIFICATIONS' => $menu->NOTIFICATIONS,
							'IS_SEEN' => $menu->IS_SEEN,
							'IS_ACTIVE' => $menu->IS_ACTIVE,
						);
					}
				}

		

		return $menu_arr;

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
                
                $ret_data = $this->get_purchase_requisition_approval_data($menu->REF_ID,$menu->IS_SEEN,$menu->INSERT_DATE);
                    
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

	public function get_price_quatation_approval_data($ref_id)
	{
        $sql = "SELECT a.id,
                        a.style_ref,
                        b.company_name,
                        c.buyer_name,
                        a.quot_date,
                        d.user_full_name,
                        a.est_ship_date
                FROM   wo_price_quotation a
                        LEFT JOIN user_passwd d
                        ON a.inserted_by = d.id
                        LEFT JOIN lib_company b
                        ON a.company_id = b.id
                        LEFT JOIN lib_buyer c
                        ON a.buyer_id = c.id
                WHERE a.id = ? ";

        $result = $this->db->query($sql, array($ref_id))->result();

        $data_arr = array();

        if (!empty($result))
        {
            foreach ($result as $row)
            {
                $desc = "Quotation Id: ".$row->ID.",\nBuyer: ".$row->BUYER_NAME.",\nStyle Ref.: ".$row->STYLE_REF.",\nQuotation date: ".date('d-m-Y',strtotime($row->QUOT_DATE)).",\Shipment date: ".date('d-m-Y',strtotime($row->EST_SHIP_DATE)).",\nInserted by : ".$row->USER_FULL_NAME;
                $data_arr[] = array(
                    'ID' => $row->ID,
                    'DATE' => $row->QUOT_DATE,
                    'DELIVERY_DATE' => $row->EST_SHIP_DATE,
                    'COMPANY' => $row->COMPANY_NAME,
                    'BUYER' => $row->BUYER_NAME,
                    'SYS_NUMBER' => '',
                    'SYS_DEF' => $row->STYLE_REF,
                    'DESC' => $desc
                );

                
            }
        }
        return $data_arr;
	}

    public function get_purchase_requisition_approval_data($ref_id,$IS_SEEN = 0,$INSERT_DATE = '')
	{
        
        $repartment_arr = $this->returnLibraryArray(" select ID,DEPARTMENT_NAME from lib_department where  is_deleted=0 and status_active=1","ID","DEPARTMENT_NAME");
        $repartment_arr[0] = "";
        $sql = "SELECT  A.ID,
                        A.JOB_NO,
                        B.COMPANY_NAME,
                        C.BUYER_NAME,
                        A.COSTING_DATE,
                        D.USER_FULL_NAME,
                        E.BRAND_NAME
                FROM WO_PRE_COST_MST A
                        JOIN WO_PO_DETAILS_MASTER F ON A.JOB_ID = F.ID
                        LEFT JOIN USER_PASSWD D ON A.INSERTED_BY = D.ID
                        LEFT JOIN LIB_COMPANY B ON F.COMPANY_NAME = B.ID
                        LEFT JOIN LIB_BUYER C ON F.BUYER_NAME = C.ID
                        LEFT JOIN LIB_BUYER_BRAND E ON F.BRAND_ID = E.ID
                WHERE A.ID =  ?
                ";

        $result = $this->db->query($sql, array($ref_id))->result();

        $data_arr = "";
        

        if (!empty($result))
        {
            foreach ($result as $row)
            {
                $desc = "Company: ".$row->COMPANY_NAME.", Buyer: ".$row->BUYER_NAME."\nReq. No: ".$row->JOB_NO.", Req. date: ".date('d/m/Y',strtotime($row->COSTING_DATE))."\nBrand: ".$row->BRAND_NAME.",\nInserted By : ".$row->USER_FULL_NAME;
                $desc = str_replace(" ", " ", $desc);
                $data_arr = array(
                    'ID' => $row->ID,
                    'DATE' => date('d/m/Y',strtotime($row->COSTING_DATE)),
                    'DELIVERY_DATE' => '',
                    'COMPANY' => $row->COMPANY_NAME,
                    'BUYER' => $row->BUYER_NAME,
                    'SYS_NUMBER' => $row->JOB_NO,
                    'SYS_DEF' => '',
                    'DESC' => $desc,
                    'IS_SEEN' => $IS_SEEN,
                    'INSERT_DATE' => $INSERT_DATE,
                );
            }
        }
        return $data_arr;
	}

    public function insert_counting($user_id)
    {
        $this->db->trans_begin();
        $ID = $this->getNextId('ID','APPROVAL_NOTIFICATION_ENGINE');
        $data = array(
			array(
				"ID" =>  $ID,
				"REF_ID" => 10679,
				"ENTRY_FORM" => 1,
				"M_MENU_ID" => 2302,
				"NOTIFI_DESC" => 'Requisition Id: 10679,
                Store: General Store-DnC [ICT],
                Requisition No: OG-RQSN-23-00353,
                Requisition date: 28-08-2023,
                Delivery date: 01-01-1970,
                Inserted by : Hossain Mahmud Rana',
				"USER_ID" => $user_id,
                "IS_APPROVED" =>0,
                "STATUS_ACTIVE" =>1,
                "IS_SEEN" =>0
			)
		);
        
        
		$this->db->insert_batch("APPROVAL_NOTIFICATION_ENGINE", $data);

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






//-----------------------------------------------------



    public function  approve_from_apps($post_data)
    { 
		$user_id = $post_data['user_id'];
		$ref_id = $post_data['ref_id'];
		$menu_id = $post_data['menu_id'];
		$entry_form = $post_data['entry_form'];

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

    public function approve_purchase_requisition_v2($app_user_id,$menu_id,$ref_id,$entry_form)
    {
       
        $pc_date_time = date("d-M-Y h:i:s A",time());

        $CURRENT_APPROVAL_STATUS = return_field_value("ID","APPROVAL_MST","APPROVED_BY=$app_user_id and entry_form = $entry_form and mst_id = $ref_id ","ID");
        if($CURRENT_APPROVAL_STATUS == 1)
        {
            return "already_approved";
        }
        
        $sql="SELECT A.ID,A.APPROVED,A.READY_TO_APPROVED,A.UPDATED_BY,b.COMPANY_NAME,b.BUYER_NAME, b.BRAND_ID from WO_PRE_COST_MST a,WO_PO_DETAILS_MASTER b where b.id = a.job_id and a.id in($ref_id)";
		
		$sqlResult=$this->sqlSelect( $sql );
        $approved_status_arr = array();
		foreach ($sqlResult as $row) 
		{
            if($row['APPROVED'] == 1)
            {
                return "already_approved";
            }
            else if($row['READY_TO_APPROVED'] != 1)
            {

                return "not_ready_to_approved";
            }

            $company_id = $row['COMPANY_NAME'];
            $approved_status_arr[$row['ID']] = $row['APPROVED'];

            $matchDataArr[$row['ID']] = array('buyer'=>$row['BUYER_NAME'],'brand'=>$row['BRAND_ID']);
		}


        $max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($ref_id)  and entry_form=77 group by mst_id","mst_id","approved_no");

        $buyer_arr = return_library_array( "SELECT ID, BUYER_NAME FROM LIB_BUYER", "ID", "BUYER_NAME"  );
        $brand_arr = return_library_array( "SELECT ID, BRAND_NAME FROM LIB_BUYER_BRAND", "ID", "BRAND_NAME"  );

		$finalDataArr=$this->getFinalUser(array('company_id'=>$company_id,'page_id'=>$menu_id,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>$brand_arr,'match_data'=>$matchDataArr,'entry_form' => $entry_form));
        

        $sequ_no_arr_by_sys_id =$finalDataArr['final_seq'];
        $user_sequence_no = $finalDataArr['user_seq'][$app_user_id];
        $user_group_no = $finalDataArr['user_group'][$app_user_id];
        $max_group_no = max($finalDataArr['user_group']);

        $app_mst_data_array = ''; $history_data_array = ''; $mst_data_array_up = array();
     
		
        $app_id = return_next_id( "ID","APPROVAL_MST") ;
        $app_his_id = return_next_id( "ID","APPROVAL_HISTORY") ;
        $target_app_id_arr = explode(',',$ref_id);
        foreach($target_app_id_arr as $mst_id)
        {		

			$approved = ((max($finalDataArr['final_seq'][$mst_id])==$user_sequence_no) || ( (max($finalDataArr['group_bypass_no_data_arr'][$max_group_no][2])==$user_sequence_no) && ($max_group_no == $user_group_no))  || ( (max($finalDataArr['group_bypass_no_data_arr'][$max_group_no][2]) == '') && ($max_group_no == $user_group_no) ) )?1:3;



			//App Muster.......................
			if($app_mst_data_array!='')
            {
                $app_mst_data_array.=",";
            }
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
            $rID=$this->sqlInsert("APPROVAL_MST",$app_mst_field_array,$app_mst_data_array);
            if($rID!=1) 
            {
                throw new Exception($rID);
            }

            $mst_field_array_up="APPROVED*APPROVED_SEQU_BY*APPROVED_GROUP_BY*APPROVED_BY*APPROVED_DATE"; 
            $rID1=$this->executeQuery($this->bulkUpdateSqlStatement( "wo_pre_cost_mst", "ID", $mst_field_array_up, $mst_data_array_up, $target_app_id_arr ));
            if($rID1!=1) 
            {
                throw new Exception($rID1);
            }


            $query = "UPDATE approval_history SET current_approval_status=0 WHERE entry_form=77 and mst_id in ($ref_id)";
            $rID2 = $this->executeQuery($query);
            if($rID2 != 1) 
            {
                throw new Exception($rID2);
            }

            $history_field_array = "id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,IS_SIGNING,APPROVED";
            $rID3 = $this->sqlInsert("approval_history",$history_field_array,$history_data_array);
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


              
                    $rID4 = $this->executeQuery($sql_precost_trim_cost_dtls);
                    if($rID4 != 1){throw new Exception($rID4);}
                
                    $rID5= $this->executeQuery($sql_precost_trim_co_cons_dtl);
                    if($rID5 != 1){throw new Exception($rID5);}
         
                    $rID6= $this->executeQuery($sql_precost_fab_con_cst_dtls);
                    if($rID6 != 1){throw new Exception($rID6);}
      
                    $rID15= $this->executeQuery($sql_insert,0);
                    if($rID15 != 1){throw new Exception($rID15);}
     
                    $rID7= $this->executeQuery($sql_precost_dtls);
                    if($rID7 != 1){throw new Exception($rID7);}
            
                    $rID8= $this->executeQuery($sql_precost_fabric_cost_dtls);
                    if($rID8 != 1){throw new Exception($rID8);}
           
                    $rID9= $this->executeQuery($sql_precost_fab_yarn_cst);
                    if($rID9 != 1){throw new Exception($rID9);}
             
                    $rID10= $this->executeQuery($sql_precost_fcomarc_cost_dtls);
                    if($rID10 != 1){throw new Exception($rID10);}
              
                    $rID11= $this->executeQuery($sql_precost_commis_cost_dtls);
                    if($rID11 != 1){throw new Exception($rID11);}
                
                    $rID12= $this->executeQuery($sql_precost_embe_cost_dtls);
                    if($rID12 != 1){throw new Exception($rID12);}
               
                    $rID13= $this->executeQuery($sql_precost_fab_yarnbkdown_his);
                    if($rID13 != 1){throw new Exception($rID13);}
                
                    $rID14= $this->executeQuery($sql_precost_fab_sum_dtls);
                    if($rID14 != 1){throw new Exception($rID14);}
                  
            }

           

            $approval_parameter = array('BUYER_ID' => $matchDataArr[$ref_id]['buyer'],'BRAND_ID' => $matchDataArr[$ref_id]['brand']);
            $res_engine = $this->notificationEngineForApps($ref_id,$company_id,$entry_form,$approval_parameter,$app_user_id);
            if($res_engine!=1)
            {
                throw new Exception($res_engine);
            }

            $desc = $this->getDesc($entry_form,$ref_id);
            $notif_res =  insertNotificationData($ref_id,$entry_form,$app_user_id,$desc,1);
            if($notif_res != 1)
            {
                throw new Exception($notif_res);
            }


            $appr_data = array("USER_ID"=>$app_user_id,"SEQUENCE_NO" =>$user_sequence_no,"COMPANY_ID"=> $company_id,'NOTIFICATION_TYPE'=>0);
            $ret_push =  $this->pushAll($ref_id,$entry_form,$appr_data);
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

    

    public function unapprove_from_apps($post_data)
    {  //print_r(5);die;
        $user_id = $post_data['user_id'];
		$ref_id = $post_data['ref_id'];
		$menu_id = $post_data['menu_id'];
		$message = $post_data['message'];
        $entry_form = $post_data['entry_form'];
        //return $ref_id;     
        $response = $this->unapprove_purchase_requisition_v2($user_id,$menu_id,$ref_id,$entry_form,$message);

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
        //print_r($ref_id);die;
        
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

    public function CheckWorking($ref)
    {
        $this->db->trans_begin();
        $ID = $this->getNextId('ID','APPROVAL_TESTING');
        $data = array(
			array(
				"ID" =>  $ID,
				"NAME" => $ref,
			)
		);
        
        
		$this->db->insert_batch("APPROVAL_TESTING", $data);

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



    public function unapprove_purchase_requisition_v2($user_id,$menu_id,$ref_id,$entry_form,$message ='')
    {   //print_r(5);die;
        $pc_date_time = date("d-M-Y h:i:s A",time());
  
		$CURRENT_APPROVAL_STATUS = return_field_value("ID","APPROVAL_MST","APPROVED_BY=$user_id and ENTRY_FORM = $entry_form and MST_ID  = $ref_id ","ID");
        if(empty($CURRENT_APPROVAL_STATUS))
        {
            return "not_approved";
        }
        
        $this->db->trans_strict(TRUE);
        $this->db->trans_begin();
        try
        {
        
            $rID1=sql_multi_row_update("wo_pre_cost_mst","approved*ready_to_approved*APPROVED_SEQU_BY*APPROVED_GROUP_BY*APPROVED_BY",'0*0*0*0*0',"id",$ref_id,0);
            if($rID!=1) 
            {
                throw new Exception($rID);
            }

            $query="UPDATE approval_history SET current_approval_status=0,APPROVED=0,IS_SIGNING=0 WHERE entry_form=$entry_form and mst_id in ($ref_id) and approved_by <> $user_id ";
			$rID2=execute_query($query,1);
            if($rID2!=1) 
            {
                throw new Exception($rID);
            }

            $query="delete from approval_mst  WHERE entry_form=$entry_form and mst_id in ($ref_id)";
			$rID3=execute_query($query,1); 
			if($rID3!=1) 
            {
                throw new Exception($rID);
            }


            $query="UPDATE approval_history SET current_approval_status=0,IS_SIGNING=0, un_approved_by='".$user_id."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' ,APPROVED=0 WHERE entry_form=$entry_form and current_approval_status=1 and mst_id in ($ref_id)";
			$rID4=execute_query($query,1);
			if($rID4!=1) 
            {
                throw new Exception($rID);
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



    public function deny_approve_purchase_requisition_v2($user_id,$menu_id,$ref_id,$entry_form)
    {
        $sql_res = $this->sqlSelect("SELECT APPROVED,READY_TO_APPROVED FROM WO_PRE_COST_MST WHERE id = $ref_id");
        //print_r($entry_form);die;
        foreach($sql_res as $rows)
        {
            $READY_TO_APPROVE = $rows['READY_TO_APPROVED'];
            if($READY_TO_APPROVE != 1)
            {
                return "not_ready_to_approved";
            }
        }

        $CURRENT_APPROVAL_STATUS = $this->returnFieldValue("CURRENT_APPROVAL_STATUS","APPROVAL_HISTORY","approved_by=$user_id and entry_form = $entry_form and mst_id = $ref_id ","CURRENT_APPROVAL_STATUS");
        //print_r($CURRENT_APPROVAL_STATUS);die;
        if($CURRENT_APPROVAL_STATUS == 1)
        {

            return "already_approved";
        }
        $appr_data = array("USER_ID"=>'',"SEQUENCE_NO" =>'',"COMPANY_ID"=> '','NOTIFICATION_TYPE'=>0);
        $this->pushAll($ref_id,$entry_form,$appr_data);
        
        $this->db->trans_strict(TRUE);
        $this->db->trans_begin();
        try
        {
            
            $rID=$this->sqlMultirowUpdate("WO_PRE_COST_MST","APPROVED*READY_TO_APPROVED*APPROVED_SEQU_BY*APPROVED_GROUP_BY","0*0*0*0","id",$ref_id); 
            
            if($rID!=1) 
            {
                throw new Exception($rID);
            }
    
            $query="delete from approval_mst  WHERE entry_form=$entry_form and mst_id in ($ref_id)";
            $rID2=$this->executeQuery($query); 
            if($rID2!=1) 
            {
                throw new Exception($rID2);
            }
            
          

            $query="UPDATE approval_history SET current_approval_status=0,IS_SIGNING=0 WHERE entry_form=$entry_form and mst_id in ($ref_id)";
            $rID4=$this->executeQuery($query);
            if($rID4!=1) 
            {
                throw new Exception($rID4);
            }

            $query="DELETE FROM APPROVAL_NOTIFICATION_ENGINE  WHERE ENTRY_FORM=$entry_form AND REF_ID IN ($ref_id) ";
            $rID6=$this->executeQuery($query); 

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
            $approved_no_history=$this->returnFieldValue("APPROVED_NO","APPROVAL_HISTORY","ENTRY_FORM=1 AND MST_ID=$ref_id AND APPROVED_BY=$user_id","APPROVED_NO");
			$approved_no_cause=$this->returnFieldValue("APPROVAL_NO","FABRIC_BOOKING_APPROVAL_CAUSE","ENTRY_FORM=1 AND BOOKING_ID=$ref_id AND USER_ID=$user_id AND APPROVAL_TYPE=$approval_type","APPROVAL_NO");
            $pc_date_time = date("d-M-Y h:i:s A",time());
			

			if($approved_no_history=="" && $approved_no_cause=="")
			{
				//echo "insert"; die;

				$id_mst=$this->getNextId("ID","FABRIC_BOOKING_APPROVAL_CAUSE") ;

				$field_array="ID,PAGE_ID,ENTRY_FORM,USER_ID,BOOKING_ID,APPROVAL_TYPE,APPROVAL_NO,APPROVAL_CAUSE,INSERTED_BY,INSERT_DATE,STATUS_ACTIVE,IS_DELETED";
				$data_array="(".$id_mst.",".$menu_id.",".$entry_form.",".$user_id.",".$ref_id." ,".$approval_type.",0,'".str_replace("'","",$message)."',".$user_id.",'".$pc_date_time."',1,0)";
				$rID=$this->sqlInsert("FABRIC_BOOKING_APPROVAL_CAUSE",$field_array,$data_array);
				if($rID!=1)
                {
                    throw new Exception($rID);
                }
			}
			else if($approved_no_history=="" && $approved_no_cause!="")
			{

				$id_cause=$this->returnFieldValue("MAX(ID) AS ID","FABRIC_BOOKING_APPROVAL_CAUSE","ENTRY_FORM=$entry_form AND BOOKING_ID=$ref_id AND USER_ID=$user_id AND APPROVAL_TYPE=$approval_type","ID");

				$field_array="PAGE_ID*ENTRY_FORM*USER_ID*BOOKING_ID*APPROVAL_TYPE*APPROVAL_NO*APPROVAL_CAUSE*UPDATED_BY*UPDATE_DATE*STATUS_ACTIVE*IS_DELETED";
				$data_array="".$menu_id."*".$entry_form."*".$user_id."*".$ref_id."*".$approval_type."*0*'".str_replace("'","",$message)."'*".$user_id."*'".$pc_date_time."'*1*0";

				$rID=$this->sqlUpdate("FABRIC_BOOKING_APPROVAL_CAUSE",$field_array,$data_array,"ID",$id_cause);
                if($rID!=1)
                {
                    throw new Exception($rID);
                }
				
			}
			else if($approved_no_history!="" && $approved_no_cause!="")
			{
				$max_appv_no_his=$this->returnFieldValue("MAX(APPROVED_NO) AS APPROVED_NO","APPROVAL_HISTORY","ENTRY_FORM=1 AND MST_ID=$ref_id AND APPROVED_BY=$user_id","APPROVED_NO");
				$max_appv_no_cause=$this->returnFieldValue("MAX(APPROVAL_NO) AS APPROVAL_NO","fABRIC_BOOKING_APPROVAL_CAUSE","ENTRY_FORM=1 AND BOOKING_ID=$ref_id aND USER_ID=$user_id AND APPROVAL_TYPE=$approval_type","APPROVAL_NO");

				if($max_appv_no_his!=$max_appv_no_cause)
				{
					$id_mst=$this->getNextId( "ID", "FABRIC_BOOKING_APPROVAL_CAUSE") ;

					$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
					$data_array="(".$id_mst.",".$menu_id.",".$entry_form.",".$user_id.",".$ref_id." ,".$approval_type.",".$max_appv_no_his.",'".str_replace("'","",$message)."',".$user_id.",'".$pc_date_time."',1,0)";
					
					$rID=$this->sqlInsert("FABRIC_BOOKING_APPROVAL_CAUSE",$field_array,$data_array);
                    if($rID!=1)
                    {
                        throw new Exception($rID);
                    }
				}
				else if($max_appv_no_his==$max_appv_no_cause)
				{
					$id_cause=$this->returnFieldValue("MAX(ID) AS ID","FABRIC_BOOKING_APPROVAL_CAUSE","ENTRY_FORM=1 AND BOOKING_ID=$ref_id AND USER_ID=$user_id AND APPROVAL_TYPE=$approval_type","ID");

					$field_array="PAGE_ID*ENTRY_FORM*USER_ID*BOOKING_ID*APPROVAL_TYPE*APPROVAL_NO*APPROVAL_CAUSE*UPDATED_BY*UPDATE_DATE*STATUS_ACTIVE*IS_DELETED";
					$data_array="".$menu_id."*".$entry_form."*".$user_id."*".$ref_id."*".$approval_type."*".$max_appv_no_his."*'".str_replace("'","",$message)."'*".$user_id."*'".$pc_date_time."'*1*0";

					 $rID=$this->sqlUpdate("FABRIC_BOOKING_APPROVAL_CAUSE",$field_array,$data_array,"ID","".$id_cause."");

                     if($rID!=1)
                     {
                         throw new Exception($rID);
                     }
				}
			}
			else if($approved_no_history!="" && $approved_no_cause=="")
			{
				$max_appv_no_his=$this->returnFieldValue("MAX(APPROVED_NO) as APPROVED_NO","APPROVAL_HISTORY","ENTRY_FORM=1 AND MST_ID=$ref_id AND APPROVED_BY=$user_id","APPROVED_NO");
				$max_appv_no_cause=$this->returnFieldValue("MAX(APPROVAL_NO) AS APPROVAL_NO","fabric_booking_approval_cause","entry_form=$entry_form and booking_id=$ref_id and user_id=$user_id and approval_type=$approval_type","APPROVAL_NO");

				if($max_appv_no_his!=$max_appv_no_cause)
				{
					$id_mst=$this->getNextId( "ID", "FABRIC_BOOKING_APPROVAL_CAUSE") ;

					$field_array="ID,PAGE_ID,ENTRY_FORM,USER_ID,BOOKING_ID,APPROVAL_TYPE,APPROVAL_NO,APPROVAL_CAUSE,INSERTED_BY,INSERT_DATE,STATUS_ACTIVE,IS_DELETED";
					$data_array="(".$id_mst.",".$menu_id.",".$entry_form.",".$user_id.",".$ref_id." ,".$approval_type.",".$max_appv_no_his.",'".str_replace("'","",$message)."',".$user_id.",'".$pc_date_time."',1,0)";
                    $rID=$this->sqlInsert("FABRIC_BOOKING_APPROVAL_CAUSE",$field_array,$data_array);
                    if($rID!=1)
                    {
                        throw new Exception($rID);
                    }
				}
				else if($max_appv_no_his==$max_appv_no_cause)
				{
					$id_cause=$this->getNextId("MAX(ID) AS ID","FABRIC_BOOKING_APPROVAL_CAUSE","ENTRY_FORM=1 AND BOOKING_ID=$ref_id AND USER_ID=$user_id AND APPROVAL_TYPE=$approval_type","ID");

					$field_array="PAGE_ID*ENTRY_FORM*USER_ID*BOOKING_ID*APPROVAL_TYPE*APPROVAL_NO*APPROVAL_CAUSE*UPDATED_BY*UPDATE_DATE*STATUS_ACTIVE*IS_DELETED";
					$data_array="".$menu_id."*".$entry_form."*".$user_id."*".$ref_id."*".$approval_type."*".$max_appv_no_his."*'".str_replace("'","",$message)."'*".$user_id."*'".$pc_date_time."'*1*0";

                     $rID=$this->sqlUpdate("FABRIC_BOOKING_APPROVAL_CAUSE",$field_array,$data_array,"ID","".$id_cause."");

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

    public function logout_from_apps($post_data)
    {
        $user_id = $post_data['user_id'];
		$device_id = $post_data['device_id'];

        $FCM_TOKEN=$this->returnFieldValue("FCM_TOKEN AS FCM_TOKEN","APPROVAL_NOTI_USER_DEVICES","USER_ID=$user_id and DEVICE_ID = '".$device_id."'","FCM_TOKEN");
        if(empty($FCM_TOKEN))
        {
            return ['status'=>'ok','message'=>'Success'];
        }
        else 
        {
            $this->db->trans_strict(TRUE);
            $this->db->trans_begin();
            try
            {
                $res = $this->executeQuery("DELETE APPROVAL_NOTI_USER_DEVICES  WHERE  USER_ID = $user_id and DEVICE_ID = '".$device_id."'");
                if($res !=1)
                {
                    throw new Exception($res);
                }

                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();
                    return ['status'=>'fail','message'=>'Failed'];
                }
                else
                {
                    $this->db->trans_commit();
                    return ['status'=>'ok','message'=>'Success'];
                    
                }
            }
           catch( Exception $e)
           {
               $this->db->trans_rollback();
               return ['status'=>'fail','message'=>$e->getMessage()];
           }
        }
    }

    public function user_notification_is_active_approval_menu($post_data)
    {
        $user_id = $post_data['user_id'];
		$menu_id = $post_data['menu_id'];
		$is_active = $post_data['is_active'];

      

        $exist=$this->returnFieldValue("ID","APPROVAL_NOTI_MENU_SETTING","USER_ID=$user_id and MENU_ID = '".$menu_id."'","IS_ACTIVE");

        $this->db->trans_strict(TRUE);
            $this->db->trans_begin();
        try
        {

            if(empty($exist))
            {
                $ID = $this->getNextId('ID','APPROVAL_NOTI_MENU_SETTING');
                $fcm_token_data = array(
                    array(
                        "ID" => $ID,
                        "USER_ID" => $user_id,
                        "MENU_ID" => $menu_id,
                        "IS_ACTIVE" => $is_active
                    )
                );
        
                $this->db->insert_batch("APPROVAL_NOTI_USER_DEVICES", $fcm_token_data);
            }
            else 
            {
                
                $res = $this->executeQuery("UPDATE APPROVAL_NOTI_USER_DEVICES SET IS_ACTIVE =$is_active WHERE  USER_ID = $user_id and MENU_ID = $menu_id");
                if($res !=1)
                {
                        $this->db->trans_rollback();
                        return 0;
                }
                if($res !=1)
                {
                    throw new Exception($res);
                }   
            }
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return ['status'=>'fail','message'=>'Failed'];
            }
            else
            {
                $this->db->trans_commit();
                return ['status'=>'ok','message'=>'Success'];
                
            }
        }
        catch( Exception $e)
        {
            $this->db->trans_rollback();
            return ['status'=>'fail','message'=>$e->getMessage()];
        }
    }

    public function sendMail($to,$subject,$body,$attach='',$from='')
    {
        $active_cunnection='logicsoftware97@gmail.com_split_fshvgkbrluxlymmn_split_Logic Platform_split_smtp.gmail.com_split_587_split_TLS_split_reza@logicsoftbd.com,muktobani@gmail.com_split_1_split_0';
        list($user,$pass,$sender,$host,$port,$secure_port,$send_to,$is_smtp,$smtp_debug)=explode('_split_',$active_cunnection);
        $config = Array(
            'protocol' => 'smtp',
            'smtp_host' => $host,
            'smtp_port' => $port,
            'smtp_user' => $user, // change it to yours
            'smtp_pass' => $pass, // change it to yours
            'mailtype' => 'html',
            'charset' => 'iso-8859-1',
            'wordwrap' => TRUE
        );

        if(empty($from))
        {
            $from = $sender;
        }
    
        $this->load->library('email', $config);
        $this->email->set_newline("\r\n");
        $this->email->from($from); 
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message(str_replace("\n","<br>",$body));
        if($this->email->send())
        {
            return 1;
        }
        else
        {
          return $this->email->print_debugger();
        }
    
    }

    public function send_email_check()
    {
        return $this->sendMail("helaluddin.bru@gmail.com","Email Check","Email Testing from codeignitor",$attach='',$from='');
    }

}