<?
        header('Content-type:text/html; charset=utf-8');
        session_start();
        include('../../../../includes/common.php');

        //This for crone fire mail...........................................start;
        extract($_REQUEST);
        if($auto_mail_user_id!=''){$_SESSION['logic_erp']["user_id"]=$auto_mail_user_id;}
        //............................end;



        $user_id = $_SESSION['logic_erp']["user_id"];
        if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
        $permission=$_SESSION['page_permission'];

        $data=$_REQUEST['data'];
        $action=$_REQUEST['action'];

        if($db_type==0)
        {
            $select_year="year";
            $year_con="";
        }
        else
        {
            $select_year="to_char";
            $year_con=",'YYYY'";
        }


        //load drop down Buyer
        if ($action=="load_drop_down_buyer")
        {
            echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ,0);
            die;
        }

        if ($action=="load_drop_down_store")
        {
             
            echo create_drop_down( "cbo_store_name", 100, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$data and a.status_active=1 and a.is_deleted=0 $cetegory_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", $selected, "" ,0);
            die;
        }


        if($action=="style_refarence_surch")
        {
            echo load_html_head_contents("Item Description Info", "../../../../", 1, 1,'','','');
            extract($_REQUEST);
            ?>
            <script>

                var selected_id = new Array;
                var selected_name = new Array;
                var selected_no = new Array;
                function check_all_data() {
                    var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
                    tbl_row_count = tbl_row_count - 0;
                    for( var i = 1; i <= tbl_row_count; i++ ) {
                        var onclickString = $('#tr_' + i).attr('onclick');
                        var paramArr = onclickString.split("'");
                        var functionParam = paramArr[1];
                        js_set_value( functionParam );

                    }
                }

                function toggle( x, origColor ) {
                    var newColor = 'yellow';
                    if ( x.style ) {
                        x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
                    }
                }

                function js_set_value( strCon )
                {
                    //alert(strCon);
                        var splitSTR = strCon.split("_");
                        var str = splitSTR[0];
                        var selectID = splitSTR[1];
                        var selectDESC = splitSTR[2];
                        //$('#txt_individual_id' + str).val(splitSTR[1]);
                        //$('#txt_individual' + str).val('"'+splitSTR[2]+'"');

                        toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );

                        if( jQuery.inArray( selectID, selected_id ) == -1 ) {
                            selected_id.push( selectID );
                            selected_name.push( selectDESC );
                            selected_no.push( str );
                        }
                        else {
                            for( var i = 0; i < selected_id.length; i++ ) {
                                if( selected_id[i] == selectID ) break;
                            }
                            selected_id.splice( i, 1 );
                            selected_name.splice( i, 1 );
                            selected_no.splice( i, 1 );
                        }
                        var id = ''; var name = ''; var job = ''; var num='';
                        for( var i = 0; i < selected_id.length; i++ ) {
                            id += selected_id[i] + ',';
                            name += selected_name[i] + ',';
                            num += selected_no[i] + ',';
                        }
                        id 		= id.substr( 0, id.length - 1 );
                        name 	= name.substr( 0, name.length - 1 );
                        num 	= num.substr( 0, num.length - 1 );
                        //alert(num);
                        $('#txt_selected_id').val( id );
                        $('#txt_selected').val( name );
                        $('#txt_selected_no').val( num );
                }


                function fn_selected()
                {
                    var style_no='<? echo $txt_style_ref_no;?>';
                    var style_id='<? echo $txt_style_ref_id;?>';
                    var style_des='<? echo $txt_style_ref;?>';
                    //alert(style_id);
                    if(style_no!="")
                    {
                        style_no_arr=style_no.split(",");
                        style_id_arr=style_id.split(",");
                        style_des_arr=style_des.split(",");
                        var str_ref="";
                        for(var k=0;k<style_no_arr.length; k++)
                        {
                            str_ref=style_no_arr[k]+'_'+style_id_arr[k]+'_'+style_des_arr[k];
                            js_set_value(str_ref);
                        }
                    }
                }


            </script>
            <link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" />
            </head>
            <body>
            <div align="center">
                <form name="styleRef_form" id="styleRef_form">
                <fieldset>
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                        <thead>
                            <th>Style Ref No</th>
                            <th>Job No</th>
                            <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                        </thead>
                        <tbody>
                            <tr>
                                <td align="center">
                                    <input type="text" style="width:130px" class="text_boxes" name="txt_style_ref_no" id="txt_style_ref_no" />
                                </td>
                                <td align="center">
                                    <input type="text" style="width:130px" class="text_boxes" name="txt_job_no" id="txt_job_no" />
                                </td>
                                <td align="center">
                                    <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+'<? echo $buyer; ?>'+'**'+document.getElementById('txt_style_ref_no').value+'**'+document.getElementById('txt_job_no').value, 'style_refarence_surch_list_view', 'search_div', 'date_wise_yarn_issue_report_for_buyer_controller', 'setFilterGrid(\'list_view\',-1)');fn_selected();" style="width:100px;" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
                    <div style="margin-top:15px" id="search_div"></div>
            </form>
            </div>
            </body>
            <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
            </html>
            <?
            exit();
        }

        //style search------------------------------//
        if($action=="style_refarence_surch_list_view")
        {
            extract($_REQUEST);
            echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
            list($company,$buyer,$style_ref_no,$job_no)=explode('**',$data);

            if($style_ref_no!=""){$search_con=" and style_ref_no like('%$style_ref_no%')";}
            if($job_no!=""){$search_con .=" and job_no like('%$job_no')";}

            $buyer=str_replace("'","",$buyer);
            $company=str_replace("'","",$company);
            $cbo_year=str_replace("'","",$cbo_year);
            if($buyer!=0) $buyer_cond=" and buyer_name=$buyer"; else $buyer_cond="";
            if($cbo_year!=0){ if($db_type==0) $year_cond=" and year(insert_date)='$cbo_year'"; else  $year_cond=" and to_char(insert_date,'YYYY')='$cbo_year'";}else {$year_cond="";}
            //echo $year_cond.jahid;die;
            $sql = "select id,style_ref_no,job_no,job_no_prefix_num,$select_year(insert_date $year_con) as year from wo_po_details_master where company_name=$company $buyer_cond $year_cond  $search_con and is_deleted=0 order by job_no_prefix_num";
            //echo $sql; die;
            echo create_list_view("list_view", "Style Ref No,Job No,Year","160,90,100","400","235",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,year", "","setFilterGrid('list_view',-1)","0","",1) ;
            echo "<input type='hidden' id='txt_selected_id' />";
            echo "<input type='hidden' id='txt_selected' />";
            echo "<input type='hidden' id='txt_selected_no' />";

            exit();
        }



        
        //report generated here--------------------//
        
        if($action=="generate_report")        
        { 
            $started = microtime(true);
            $process = array( &$_POST );             
            extract(check_magic_quote_gpc( $process ));
            
            $con = connect();
            execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id ." and ref_from in(1) and entry_form=7777");
            oci_commit($con);
            disconnect($con);


            $cbo_company_name=str_replace("'","",$cbo_company_name);
            $cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
            $txt_style_ref=str_replace("'","",$txt_style_ref);
            $txt_style_ref_id=str_replace("'","",$txt_style_ref_id);
            $txt_date_from=str_replace("'","",$txt_date_from);
            $txt_date_to=str_replace("'","",$txt_date_to);
            $cbo_dyed_type=str_replace("'","",$cbo_dyed_type);
            $rptType=str_replace("'","",$rptType);             
            $cbo_store_name=str_replace("'","",$cbo_store_name);
            $cbo_year=str_replace("'","",$cbo_year);
            $internal_ref=str_replace("'","",$internal_ref);
            $print_action="";

            $buyer_cond = ($cbo_buyer_name !=0)?"and F.BUYER_NAME = '$cbo_buyer_name'" : "";
            $store_cond = ($cbo_store_name !=0)?"and B.STORE_ID = '$cbo_store_name'" : "";
            $job_no_cond = ($txt_style_ref !=0)?"and f.JOB_NO_PREFIX_NUM = '$txt_style_ref'" : "";
            $internal_ref_cond=($internal_ref !='')?"and e.GROUPING = '$internal_ref'" : "";
            $dyed_cond=($cbo_dyed_type !=0)?"and c.dyed_type = '$cbo_dyed_type'" : "";  

            $txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');            

            $dateCond = ($txt_date_from && $txt_date_to) ?" and a.ISSUE_DATE between '".change_date_format($txt_date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($txt_date_to,'dd-mm-yyyy','-',1)."'"  : ""; 

            $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
            $store_library=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
            $buyer_name_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
            $buyer_short_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
            $color_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
            $supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
            $user_name_arr=return_library_array( "select id, user_name from  user_passwd",'id','user_name');
            $division_arr=return_library_array("select id,division_name from lib_division",'id','division_name');
            $department_arr=return_library_array("select id,department_name from lib_department",'id','department_name');
            $buyer_session_arr=return_library_array("select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0 order by season_name ASC",'id','season_name');
            $comps_arr=return_library_array("select id, COMPOSITION_NAME from LIB_COMPOSITION_ARRAY where status_active =1 and is_deleted=0",'id','COMPOSITION_NAME');
            $yarn_count_arr=return_library_array( "select id, yarn_count from  lib_yarn_count",'id','yarn_count');
           

            $main_sql="select A.ID AS MST_ID,A.GATE_PASS_NO,A.ISSUE_NUMBER,A.INSERTED_BY,A.COMPANY_ID, A.ISSUE_PURPOSE,A.ISSUE_BASIS, A.CHALLAN_NO,A.IS_POSTED_INV_ACCOUNT,B.ID AS TRANS_ID,B.STORE_ID,B.SUPPLIER_ID, b.INSERT_DATE,B.REMARKS, B.TRANSACTION_DATE, A.BOOKING_NO,B.PROD_ID,B.CONS_QUANTITY,B.CONS_AMOUNT,B.CONS_RATE,c.yarn_comp_type1st as yarn_comp_type1st,
			c.yarn_comp_percent1st as yarn_comp_percent1st,c.yarn_comp_type2nd as yarn_comp_type2nd,c.yarn_comp_percent2nd  as yarn_comp_percent2nd,
            C.LOT, C.YARN_COUNT_ID,C.YARN_COMP_TYPE1ST,C.YARN_TYPE,C.COLOR,F.BUYER_NAME, F.STYLE_REF_NO, B.REQUISITION_NO, C.SUPPLIER_ID, e.GROUPING 
            FROM ( ( ( ( (INV_ISSUE_MASTER  a
                     INNER JOIN INV_TRANSACTION b ON a.id = b.mst_id)
                   INNER JOIN PRODUCT_DETAILS_MASTER c ON b.prod_id = c.id)
                 LEFT JOIN ORDER_WISE_PRO_DETAILS d ON b.id = d.trans_id)
               LEFT JOIN WO_PO_BREAK_DOWN e ON d.PO_BREAKDOWN_ID = e.id)
             LEFT JOIN WO_PO_DETAILS_MASTER f ON e.job_id = f.id)
            where A.ITEM_CATEGORY=1 AND B.TRANSACTION_TYPE=2 AND A.STATUS_ACTIVE=1 
            AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 $dateCond AND A.COMPANY_ID=$cbo_company_name $buyer_cond $store_cond $job_no_cond $internal_ref_cond $dyed_cond";        
            
            /*$main_sql="select A.ID AS MST_ID,A.GATE_PASS_NO,A.ISSUE_NUMBER,A.INSERTED_BY,A.COMPANY_ID, A.ISSUE_PURPOSE,A.ISSUE_BASIS, A.CHALLAN_NO,A.IS_POSTED_INV_ACCOUNT,B.ID AS TRANS_ID,B.STORE_ID,
            B.SUPPLIER_ID, b.INSERT_DATE,B.REMARKS, B.TRANSACTION_DATE, A.BOOKING_NO,B.PROD_ID,B.CONS_QUANTITY,B.CONS_AMOUNT,B.CONS_RATE,c.yarn_comp_type1st as yarn_comp_type1st, 
            c.yarn_comp_percent1st as yarn_comp_percent1st,c.yarn_comp_type2nd as yarn_comp_type2nd,c.yarn_comp_percent2nd as yarn_comp_percent2nd, C.LOT, C.YARN_COUNT_ID,C.YARN_COMP_TYPE1ST,
            C.YARN_TYPE,C.COLOR,F.BUYER_NAME, F.STYLE_REF_NO, B.REQUISITION_NO, C.SUPPLIER_ID, e.GROUPING from INV_ISSUE_MASTER a, INV_TRANSACTION b, PRODUCT_DETAILS_MASTER c, 
            ORDER_WISE_PRO_DETAILS d, WO_PO_BREAK_DOWN e, WO_PO_DETAILS_MASTER f where A.ITEM_CATEGORY=1 AND a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and c.id=d.prod_id and 
            d.PO_BREAKDOWN_ID=e.id and e.job_id=f.id AND B.TRANSACTION_TYPE=2 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND 
            C.IS_DELETED=0 and a.ISSUE_DATE between '01-Jan-2023' and '28-Feb-2023' AND A.COMPANY_ID=3"*/
           
            $sql_result=sql_select($main_sql);
            //  echo $main_sql;
            //   echo "<pre>";
            //        print_r($sql_result);
            //    echo"</pre>";
            $result_arr=array();
            $tot_Issue_Qty=0; $tot_Amount=0;
            foreach($sql_result as $row)
             {
                 $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["prod_id"]=$row["PROD_ID"];
                 $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["issue_number"]=$row["ISSUE_NUMBER"];
                 $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["company_id"]=$row["COMPANY_ID"];
                 $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["issue_purpose"]=$row["ISSUE_PURPOSE"];
                 $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["issue_basis"]=$row["ISSUE_BASIS"];
                 $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["challan_no"]=$row["CHALLAN_NO"];
                 $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["trans_id"]=$row["TRANS_ID"];
                 $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["store_id"]=$row["STORE_ID"];
                 $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["supplier_id"]=$row["SUPPLIER_ID"];
                 $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["inserted_by"]=$row["INSERTED_BY"];
                 $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["insert_date"]=$row["INSERT_DATE"];
                 $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["remarks"]=$row["REMARKS"];
                 $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["transaction_date"]=$row["TRANSACTION_DATE"];
                 $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["lot"]=$row["LOT"];
                 $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["yran_count_id"]=$row["YARN_COUNT_ID"];                  
                 $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["yarn_type"]=$row["YARN_TYPE"];
                 $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["color"]=$row["COLOR"];
                 $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["buyer_name"]=$row["BUYER_NAME"];
                 $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["cons_qty"]=$row["CONS_QUANTITY"];
                 $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["cons_amount"]=$row["CONS_AMOUNT"];
                 $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["cons_rate"]=$row["CONS_RATE"];
                 $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["style_ref_no"]=$row["STYLE_REF_NO"];
                 $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["requisition_no"]=$row["REQUISITION_NO"];
                 $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["supplier_id"]=$row["SUPPLIER_ID"];
                 $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["trans_ref"]=$row["ISSUE_NUMBER"];
                 $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["user"]=$row["INSERTED_BY"];
                 $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["booking_no"]=$row["BOOKING_NO"];
                 $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["gate_pass_no"]=$row["GATE_PASS_NO"];
                 $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["account_posting"]=$row["IS_POSTED_INV_ACCOUNT"];
                 $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["internal_ref"]=$row["GROUPING"];

                 
                //  $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["yarn_comp_type1st"]=$comp_arr[$row["YARN_COMP_TYPE1ST"]];
                //  $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["yarn_comp_percent1st"]=$row["YARN_COMP_PERCENT1ST"];
                //  $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["yarn_comp_type2nd"]=$comp_arr[$row["YARN_COMP_TYPE2ND"]];
                //  $result_arr[$row["MST_ID"]][$row["TRANSACTION_DATE"]] [$row["BUYER_NAME"]] ["yarn_comp_percent2nd"]=$row["YARN_COMP_PERCENT2ND"];
                $all_req_no_arr[$row["REQUISITION_NO"]] = $row["REQUISITION_NO"]+$row["CONS_QUANTITY"];
                

                  
             }

            fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 7777, 1,$all_req_no_arr, $empty_arr);

            $requisition_booking_arr=return_library_array( "Select a.REQUISITION_NO,b.booking_no from GBL_TEMP_ENGINE tmp, PPL_YARN_REQUISITION_ENTRY a, PPL_PLANNING_ENTRY_PLAN_DTLS b where a.requisition_no=tmp.ref_val and tmp.user_id = ".$user_name." and tmp.ref_from=2 and tmp.entry_form=7777 and a.knit_id=b.dtls_id", "REQUISITION_NO", "booking_no"  );                                     

            $comp_sql = sql_select("select id as prod_id, YARN_COMP_TYPE1ST, YARN_COMP_PERCENT1ST, YARN_COMP_TYPE2ND, YARN_COMP_PERCENT2ND from PRODUCT_DETAILS_MASTER 
            where COMPANY_ID = $cbo_company_name");

           
            $composition_array = array();
            foreach($comp_sql as $row)
            {
                $composition_array[$row['PROD_ID']]['PROD_ID'] = $row['PROD_ID'];
                $composition_array[$row['PROD_ID']]['YARN_COMP_TYPE1ST'] = $row['YARN_COMP_TYPE1ST'];
                $composition_array[$row['PROD_ID']]['YARN_COMP_PERCENT1ST'] = $row['YARN_COMP_PERCENT1ST'];
                $composition_array[$row['PROD_ID']]['YARN_COMP_TYPE2ND'] = $row['YARN_COMP_TYPE2ND'];
                
                $composition_array[$row['PROD_ID']]['YARN_COMP_PERCENT2ND'] = $row['YARN_COMP_PERCENT2ND'];
            }
            //  echo "<pre>";
            //  print_r($composition_array);
            //  echo"</pre>";//die;
            $table_width=3170;
            $div_width="3190px";
            ob_start();
           ?> 
                  <div style="width:<? echo $div_width; ?>;">
                 <table width="1881px" id="" align="left">
                     <tr class="form_caption" style="border:none;">
                         <td colspan="19" align="center" style="border:none;font-size:16px; font-weight:bold" >Date Wise Yarn Issue Report For Buyer </td>
                     </tr>
                     <tr style="border:none;">
                         <td colspan="19" align="center" style="border:none; font-size:14px;">
                         Company Name : <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>
                         </td>
                     </tr>
                 </table>
                 <br />
                  
                     <table  width="1881px" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left">
                         <thead>
                             <tr>
                                 <th width="30" style="word-wrap: break-word; word-break:break-all; ">SL</th>
                                 <th width="50" style="word-wrap: break-word; word-break:break-all;">Prod. Id</th>
                                 <th width="100" style="word-wrap: break-word; word-break:break-all;">Store Name</th>
                                 <th width="80" style="word-wrap: break-word; word-break:break-all;">Trans. Date</th>
                                 <th width="80" style="word-wrap: break-word; word-break:break-all;">Purpose</th>                                                                          
                                 <th width="80" style="word-wrap: break-word; word-break:break-all;">Basis</th>                                          
                                 <th width="80" style="word-wrap: break-word; word-break:break-all;">Trans. Ref.</th>                                  
                                 <th width="80" style="word-wrap: break-word; word-break:break-all;">Gate Pass No</th>
                                                                                                                  
                                 <th width="80" style="word-wrap: break-word; word-break:break-all;">Challan <br>No</th>                                                                                                                
                                 <th width="80" style="word-wrap: break-word; word-break:break-all;">Buyer</th>
                                <th width="80" style="word-wrap: break-word; word-break:break-all;">Style<br> Ref.No</th>
                                <th width="80" style="word-wrap: break-word; word-break:break-all;">Internal<br> Ref.No</th>
                                <th width="80" style="word-wrap: break-word; word-break:break-all;">Booking No</th>
                                <th width="80" style="word-wrap: break-word; word-break:break-all;">Requisition No</th>
                                 
                                <th width="80" style="word-wrap: break-word; word-break:break-all;">Yarn Lot</th>
                                <th width="80" style="word-wrap: break-word; word-break:break-all;">Yarn Count</th>
                                <th width="80" style="word-wrap: break-word; word-break:break-all;">Composition</th>
                                <th width="80" style="word-wrap: break-word; word-break:break-all;">Yarn Type</th>
                                <th width="80" style="word-wrap: break-word; word-break:break-all;">Color</th>
                                
                                <th width="80" style="word-wrap: break-word; word-break:break-all;">Accounting Posting</th>
                                <th width="80" style="word-wrap: break-word; word-break:break-all;">Issue Qty</th>
                                <th width="80" style="word-wrap: break-word; word-break:break-all;">Rate(TK)</th>
                                <th width="80" style="word-wrap: break-word; word-break:break-all;">Amount(TK)</th>
                                <th  width="80" style="word-wrap: break-word; word-break:break-all;">Remarks</th>                                                    
                             </tr>
                         </thead>
                     </table>
                     <div style="width:<? echo 1890; ?>px; overflow-y: scroll;max-height:290px;float: left;" id="scroll_body">
                         <table  width="1881px" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
                          
                             <tbody>
                             
                                <?
                                        $i=0;
                                        foreach( $result_arr as $row2){
                                            foreach($row2 as $row1){
                                                foreach($row1 as $key=>$row){
                                                    
                                                    $i++;

                                                if($row['issue_basis']==3){
                                                   $booking_no=$requisition_booking_arr[$row['requisition_no']];    
                                                }
                                                else{
                                                    $booking_no=$wo_booking_arr[$row['booking_id']];   
                                                }
                                                 
                                        
                                ?>
                              
                                     <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">

                                         <td width="30" title="<? echo "" ?>" style="word-wrap: break-word; word-break:break-all;"><? echo $i; ?></td>

                                         <td width="50" style="word-wrap: break-word; word-break:break-all;"><? echo $row['prod_id']; ?></td>

                                         <td width="100" style="word-wrap: break-word; word-break:break-all;"><? echo $store_library[$row['store_id']]; ?></td>

                                         <td width="80" align="center" style="word-wrap: break-word; word-break:break-all;"><? echo $row['transaction_date'] ?>&nbsp;</td>
                                         <td width="80" align="center" style="word-wrap: break-word; word-break:break-all;"><? echo $yarn_issue_purpose [$row['issue_purpose']]; ?>&nbsp;</td>
                                         <td width="80" align="center" style="word-wrap: break-word; word-break:break-all;"><? echo $issue_basis[$row['issue_basis']]; ?>&nbsp;</td>
                                         <td width="80" align="center" style="word-wrap: break-word; word-break:break-all;"><? echo $row['trans_ref']; ?>&nbsp;</td>
                                         <td width="80" align="center" style="word-wrap: break-word; word-break:break-all;"><? echo $row['gate_pass_no'];  ?>&nbsp;</td>
                                         <td width="80" align="center" style="word-wrap: break-word; word-break:break-all;"><? echo $row['challan_no']; ?>&nbsp;</td>
                                         <td width="80" align="center" style="word-wrap: break-word; word-break:break-all;"><?  echo $buyer_name_arr[$row['buyer_name']]; ?>&nbsp;</td>
                                         <td width="80" align="center" style="word-wrap: break-word; word-break:break-all;"><? echo $row['style_ref_no']; ?>&nbsp;</td>
                                         <td width="80" align="center" style="word-wrap: break-word; word-break:break-all;"><? echo $row['internal_ref']; ?>&nbsp;</td>
                                         <td width="80" align="center" style="word-wrap: break-word; word-break:break-all;"><? /*echo $booking_no;*/ echo $row['booking_no'];  ?>&nbsp;</td>
                                         <td width="80" align="center" style="word-wrap: break-word; word-break:break-all;"><? echo $row['requisition_no']; ?>&nbsp;</td>
                                          
                                         <td width="80" align="center" style="word-wrap: break-word; word-break:break-all;"><? echo $row['lot']; ?>&nbsp;</td>
                                         <td width="80" align="center" style="word-wrap: break-word; word-break:break-all;"><? echo $yarn_count_arr[$row['yran_count_id']]; ?>&nbsp;</td>
                                         <td width="80" align="center" style="word-wrap: break-word; word-break:break-all;"><?  
                                             if($composition_array[$row['prod_id']]['YARN_COMP_PERCENT1ST']!=0) {$parcent1st=$composition_array[$row['prod_id']]['YARN_COMP_PERCENT1ST']."%";} else {$parcent1st="";}
                                             if($composition_array[$row['prod_id']]['YARN_COMP_PERCENT2ND']!=0 ){ $parcent2nd=$composition_array[$row['prod_id']]['YARN_COMP_PERCENT2ND']."%";} else {$parcent2nd="";}
                                             echo $comps_arr[$composition_array[$row['prod_id']]['YARN_COMP_TYPE1ST']].' '.$parcent1st.' '.$comps_arr[$composition_array[$row['prod_id']]['YARN_COMP_TYPE2ND']].' '.$parcent2nd;
                                         
                                         ?>&nbsp;</td> 

                                 
                                         <td width="80" align="center" style="word-wrap: break-word; word-break:break-all;"><? echo $yarn_type_for_entry[$row['yarn_type']]; ?>&nbsp;</td>
                                         <td width="80" align="center" style="word-wrap: break-word; word-break:break-all;"><? echo $color_arr[$row['color']]; ?>&nbsp;</td>
                                         
                                         <td width="80" align="center" style="word-wrap: break-word; word-break:break-all;"><? 
                                                    if($result_arr['account_posting']==1)
                                                    {
                                                        echo "Yes";
                                                    }
                                                    else{
                                                        echo "No";
                                                    }
                                         ?>&nbsp;</td>
                                         <td width="80" align="center" style="word-wrap: break-word; word-break:break-all;"><?echo $row['cons_qty']; $tot_Issue_Qty=$tot_Issue_Qty+$row['cons_qty'];?></td>
                                         <td width="80" align="center" style="word-wrap: break-word; word-break:break-all;"><? echo $row['cons_rate']; ?>&nbsp;</td>
                                         <td width="80" align="center" style="word-wrap: break-word; word-break:break-all;"><? echo $row['cons_amount']; $tot_Amount=$tot_Amount+$row['cons_amount'];?></td>                                          
                                         <td width="80" align="center" style="word-wrap: break-word; word-break:break-all;"><? echo $row['remarks']; ?>&nbsp;</td>
                                    </tr>
                                    
                                    <?
                                            }
                                          }                                          
                                        }
                                    ?>
                             </tbody>
                         </table>
                         <!--<script language="javascript"> setFilterGrid('table_body',-1)</script> -->
                     <table width="1881px" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
                             <tfoot>
                                 <tr>
                                     <th width="30">&nbsp;</th>
                                     <th width="50">&nbsp;</th>
                                     <th width="100">&nbsp;</th>
                                     <th width="80">&nbsp;</th>
                                     <th width="80">&nbsp;</th>
                                     <th width="80">&nbsp;</th>
                                     <th width="80">&nbsp;</th>
                                     <th width="80">&nbsp;</th>
                                     <th width="80">&nbsp;</th>
                                     <th width="80">&nbsp;</th>
                                     <th width="80">&nbsp;</th>
                                     <th width="80">&nbsp;</th>
                                     <th width="80">&nbsp;</th>                                      
                                     <th width="80">&nbsp;</th>
                                     <th width="80">&nbsp;</th>
                                     <th width="80">&nbsp;</th>
                                     <th width="80">&nbsp;</th>
                                     <th width="80">&nbsp;</th>
                                     <th width="80">&nbsp;</th>
                                     <th width="80">Total:</th>
                                     <th width="80"><? echo $tot_Issue_Qty ?></th>
                                     <th width="80">&nbsp;</th>
                                     <th width="80"><? echo $tot_Amount ?></th>                                      
                                     <th width="80">&nbsp;</th>                                                                                                               
                                 </tr>
                             </tfoot>
                     </table>
                     </div>            
             
           <?
             
        }

        $con = connect();
        execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id ." and ref_from in(1) and entry_form=7777");
        oci_commit($con);
        disconnect($con);

         $html = ob_get_contents();
         ob_clean();

         echo "Execution Time: " . (microtime(true) - $started) . "S";
         foreach (glob($user_id."*.xls") as $filename) {          
            @unlink($filename);
         }
         //---------end------------//
         $name=time();
         $filename=$user_id."_".$name.".xls";
         $create_new_doc = fopen($filename, 'w');
         $is_created = fwrite($create_new_doc,$html);
         echo "$html**$filename";
         disconnect($con);	 
         exit();
?>
