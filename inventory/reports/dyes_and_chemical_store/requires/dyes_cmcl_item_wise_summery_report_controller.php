<?
    header('Content-type:text/html; charset=utf-8');
    session_start();
    include('../../../../includes/common.php');

    $user_id = $_SESSION['logic_erp']["user_id"];
    if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
    $permission=$_SESSION['page_permission'];
    $data=$_REQUEST['data'];
    $action=$_REQUEST['action'];

    if ($action=="item_account_popup")
	{
		echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
		extract($_REQUEST);
		$data=explode('_',$data);
		//print_r ($data);  
		?>	
	    <script>
		 var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
		 
		function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
			
		function js_set_value(id)
		{
			var str=id.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			var strdt=str[2];
			str=str[1];
		
			if( jQuery.inArray(  str , selected_id ) == -1 ) {
				selected_id.push( str );
				selected_name.push( strdt );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str  ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i,1 );
			}
			var id = '';
			var ddd='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ddd = ddd.substr( 0, ddd.length - 1 );
			$('#item_account_id').val( id );
			$('#item_account_val').val( ddd );
		} 
		</script>
	     <input type="hidden" id="item_account_id" />
	     <input type="hidden" id="item_account_val" />
	 	<?
			$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");
			$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
			if ($data[2]==0) $item_name =""; else $item_name =" and item_group_id in($data[2])";
			
			$sql="SELECT id,item_account,item_category_id,item_group_id,item_description,supplier_id from  product_details_master where company_id in($data[0]) and item_category_id in($data[1]) $item_name and  status_active=1 and is_deleted=0"; 
			$arr=array(1=>$item_category,2=>$itemgroupArr,4=>$supplierArr);
			echo  create_list_view("list_view", "Item Account,Item Category,Item Group,Item Description,Supplier,Product ID", "70,110,150,150,100,70","780","400",0, $sql , "js_set_value", "id,item_description", "", 0, "0,item_category_id,item_group_id,0,supplier_id,0", $arr , "item_account,item_category_id,item_group_id,item_description,supplier_id,id", "",'setFilterGrid("list_view",-1);','0,0,0,0,0,0','',1) ;
			exit();
	}


    if($action=="generate_report_receive")
    {
        $process = array(&$_POST);
        extract(check_magic_quote_gpc($process));
        $report_title=str_replace("'","",$report_title);
        $cbo_company_name=str_replace("'","",$cbo_company_name);
        $txt_date_from=str_replace("'","",$txt_date_from);
        $cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
        $item_group_id=str_replace("'","",$item_group_id);
        $txt_date_to=str_replace("'","",$txt_date_to);
        $txt_item_account_id=str_replace("'","",$txt_item_account_id);
        // $txt_item_acc=str_replace("'","",$txt_item_acc);

        // echo $txt_item_account_id."__".$txt_item_acc;die;
        if($db_type==2)
        {
            $txt_date_from=change_date_format($txt_date_from,'','',1);
            $txt_date_to=change_date_format($txt_date_to,'','',1);
        }
        if($db_type==0)
        {
            $txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
            $txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
        }

        if ($cbo_item_category_id !="") $category_cond= " and item_category in($cbo_item_category_id)"; else $category_cond=" and item_category in(5,6,7,19,20,22,23,39)";

        $rcv_sql="select prod_id, cons_quantity from inv_transaction where status_active=1 and company_id in($cbo_company_name) and transaction_type=1 and transaction_date between '$txt_date_from' and '$txt_date_to' $category_cond ";
        //echo $rcv_sql;//die;
        $rcv_result=sql_select($rcv_sql);
        $rcv_data=array();
        foreach($rcv_result as $row)
        {
            $rcv_data[$row[csf("prod_id")]]+=$row[csf("cons_quantity")];
        }


        $sql_cond="";

        if ($cbo_item_category_id !="") $sql_cond= " and b.item_category_id in($cbo_item_category_id)"; else $sql_cond.=" and b.item_category_id in(5,6,7,19,20,22,23,39)";
        if ($item_group_id !="") $sql_cond.=" and b.item_group_id='$item_group_id'";
        if ($txt_date_from !="" && $txt_date_to) $sql_cond.="  and a.transaction_date BETWEEN '$txt_date_from' and '$txt_date_to'";
        if ($txt_item_account_id !="") $sql_cond.=" and b.id in($txt_item_account_id)"; 


        $sql="select a.id, a.prod_id, a.item_category, a.receive_basis, a.cons_amount, a.transaction_date, b.item_group_id, b.item_description, c.id as lib_item_group_id,
        c.item_name, a.transaction_type,
        (case when a.transaction_date between'".$txt_date_from."' and '".$txt_date_to."' and a.transaction_type in(1,3) then
        a.cons_quantity else 0 end) as receive_qty,
		(case when a.transaction_type in(1,3) then a.cons_quantity else 0 end) as cons_quantity
	 	from inv_transaction a, product_details_master b, lib_item_group c
	 	where a.prod_id=b.id and b.item_group_id=c.id and a.company_id in($cbo_company_name) and a.status_active=1 and a.is_deleted=0
	 	and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond order by a.prod_id";

        // echo $sql;//die;
        $result = sql_select($sql);

        $all_data=array();
        $date_range=$txt_date_from."*".$txt_date_to;
        $company_arr = sql_select("select id, company_name from lib_company where id in($cbo_company_name)");
        foreach($result as $row)
        {

            if($row[csf("transaction_type")]==1)
            {

                $all_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
                $all_data[$row[csf("prod_id")]]["item_category"]=$row[csf("item_category")];
                $all_data[$row[csf("prod_id")]]["receive_basis"].=$row[csf("receive_basis")].",";
                $all_data[$row[csf("prod_id")]]["item_group_id"]=$row[csf("item_group_id")];
                $all_data[$row[csf("prod_id")]]["item_description"]=$row[csf("item_description")];
                $all_data[$row[csf("prod_id")]]["item_group_name"]=$row[csf("item_name")];
                $all_data[$row[csf("prod_id")]]["cons_amount"]+=$row[csf("cons_amount")];
                $all_data[$row[csf("prod_id")]]["tot_cons_quantity"]+=$row[csf("cons_quantity")];

            }
        }

        $i=1;
        ob_start();
        ?>
        <div align="center" style="height:auto; margin:0 auto; padding:0; width:1150px">
            <table width="1130" cellpadding="0" cellspacing="0" id="caption" align="left">
                <thead>
                <tr style="border:none;">
                    <td colspan="10" align="center" class="form_caption" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td>
                </tr>
                <tr style="border:none;">
                    <td colspan="10" class="form_caption" align="center" style="border:none; font-size:14px;">
                        <b>Company Name :
                            <?
                                foreach ($company_arr as $company){
                                    echo chop($company[csf("company_name")].', ',",");
                                }
                            ?></b>
                    </td>
                </tr>
                <tr style="border:none;">
                    <td colspan="10" align="center" class="form_caption" style="border:none;font-size:14px; font-weight:bold">
                        <? if($txt_date_from!="" && $txt_date_to !="") echo "Report Date From".": ".change_date_format($txt_date_from,'dd-mm-yyyy')." To ". change_date_format($txt_date_to,'dd-mm-yyyy');?>
                    </td>
                </tr>
                </thead>
            </table>
            <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="1130" rules="all" id="rpt_table_header" align="left">
                <thead>
                <tr>
                    <th width="50">SL</th>
                    <th width="60">Prod. ID</th>
                    <th width="100">Basis</th>
                    <th width="150">Item Category</th>
                    <th width="150">Item Group</th>
                    <th width="200">Item Description</th>
                    <th width="120">Receive Qnty</th>
                    <th width="110">Receive Avg Rate</th>
                    <th>Rcv Amount</th>
                </tr>
                </thead>
            </table>
            <div style="width:1150px; max-height:250px; overflow-y:scroll; overflow-x:hidden;" id="scroll_body">
                <table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="1130" rules="all" align="left">
                    <?

                        foreach($all_data as $row)
                        {
                            if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";

                            $tot_rcv_qnty+=$row[("tot_cons_quantity")];
                            $amount+=$row[("cons_amount")];
                            $avg_rate=$row[("cons_amount")]/$row[("tot_cons_quantity")];

                            $rcv_basis_arr=array_unique(explode(",",chop($row["receive_basis"],',')));
                            $rcv_basis_data="";
                            foreach($rcv_basis_arr as $basis_id)
                            {
                                $rcv_basis_data.=$receive_basis_arr[$basis_id].",";
                            }
                            $rcv_basis_data=chop($rcv_basis_data,",");

                            ?>
                            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="50" align="center"><? echo $i; ?>&nbsp;</td>
                                <td width="60" align="center"><? echo $row["prod_id"]; ?>&nbsp;</td>
                                <td width="100"><? echo $rcv_basis_data; ?>&nbsp;</td>
                                <td width="150"><? echo $item_category[$row[("item_category")]]; ?></td>
                                <td width="150"><? echo $row[("item_group_name")]; ?></td>
                                <td width="200"><? echo $row[("item_description")]; ?></td>
                                <td width="120" align="right" title="">
                                    <a href="##"  onClick="receive_qnty_dtls(<? echo $row["prod_id"]; ?>, '<? echo $date_range?>',
                                            'receive_quantity_dtls_popup')
                                            "><? echo number_format($row[("tot_cons_quantity")],2)
                                        ; ?></a></td>
                                <td width="110" align="right"><? echo number_format($avg_rate,4); ?> </td>
                                <td align="right"><? echo number_format($row["cons_amount"],2); ?></td>
                            </tr>
                            <?
                            $i++;
                        }
                    ?>
                </table>
            </div>
            <table width="1130" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
                <tfoot>
                <tr>
                    <th width="50">&nbsp; </th>
                    <th width="60">&nbsp; </th>
                    <th width="100">&nbsp; </th>
                    <th width="150">&nbsp; </th>
                    <th width="150"></th>
                    <th width="200"  style="text-align: right" >Total: &nbsp;</th>
                    <th width="120" style="text-align: right" id="value_tot_rcv_qty"><? echo number_format($tot_rcv_qnty,2); ?>&nbsp;</th>
                    <th width="110"></th>
                    <th id="value_tot_amount_qty" style="text-align: right"><? echo number_format($amount,2); ?>&nbsp;</th>
                </tr>
                </tfoot>
            </table>
        </div>
        <?
        $html = ob_get_contents();
        ob_clean();
        //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
        foreach (glob("*.xls") as $filename) {
            //if( @filemtime($filename) < (time()-$seconds_old) )
            @unlink($filename);
        }
        //---------end------------//
        $name=time();
        $filename=$user_id."_".$name.".xls";
        $create_new_doc = fopen($filename, 'w');
        $is_created = fwrite($create_new_doc, $html);
        echo "$html**$filename**$report_type";
        exit();
    }

    if($action == "generate_report_issue")
    {
        $process = array(&$_POST);
        extract(check_magic_quote_gpc($process));
        $report_title=str_replace("'","",$report_title);
        $cbo_company_name=str_replace("'","",$cbo_company_name);
        $txt_date_from=str_replace("'","",$txt_date_from);
        $cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
        $item_group_id=str_replace("'","",$item_group_id);
        $txt_date_to=str_replace("'","",$txt_date_to);
        $txt_item_account_id=str_replace("'","",$txt_item_account_id);
        if($db_type==2)
        {
            $txt_date_from=change_date_format($txt_date_from,'','',1);
            $txt_date_to=change_date_format($txt_date_to,'','',1);
        }
        if($db_type==0)
        {
            $txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
            $txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
        }

        if ($cbo_item_category_id !="") $category_cond= " and item_category in($cbo_item_category_id)"; else $category_cond=" and item_category in(5,6,7,19,20,22,23,39)";


        $sql_cond="";

        if ($cbo_item_category_id !="") $sql_cond= " and c.item_category_id in($cbo_item_category_id)"; else $sql_cond.=" and c.item_category_id in(5,6,7,19,20,22,23,39)";
        if ($item_group_id !="") $sql_cond.=" and c.item_group_id='$item_group_id'";
        if ($txt_date_from !="" && $txt_date_to) $sql_cond.="  and b.transaction_date BETWEEN '$txt_date_from' and '$txt_date_to'";
        if ($txt_item_account_id !="") $sql_cond.=" and c.id in($txt_item_account_id)"; 

        $sql_issue = "select a.id as issue_mst_id, a.issue_basis, a.issue_purpose, b.item_category, b.prod_id, b.cons_amount, b.transaction_date, b.transaction_type,
        c.id, c.item_category_id, c.product_name_details as item_description, c.item_group_id, d.id, d.item_name,
        (case when b.transaction_date between'".$txt_date_from."' and '".$txt_date_to."' and b.transaction_type in(2) then
        b.cons_quantity else 0 end) as issue_qty,
		(case when b.transaction_type=2 then b.cons_quantity else 0 end) as cons_quantity
        from inv_issue_master a, inv_transaction b, product_details_master c, lib_item_group d
        where a.id=b.mst_id and b.prod_id=c.id and c.item_group_id=d.id and b.company_id in($cbo_company_name) and a.status_active=1 and a.is_deleted=0
	 	and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond order by b.prod_id";

        // echo $sql_issue;//die;
        $result = sql_select($sql_issue);

        $all_data=array();
        $date_range=$txt_date_from."*".$txt_date_to;
        $company_arr = sql_select("select id, company_name from lib_company where id in($cbo_company_name)");
        foreach($result as $row)
        {

            if($row[csf("transaction_type")]==2)
            {

                $all_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
                $all_data[$row[csf("prod_id")]]["item_category"]=$row[csf("item_category")];
                $all_data[$row[csf("prod_id")]]["issue_basis"].=$row[csf("issue_basis")].",";
                $all_data[$row[csf("prod_id")]]["issue_purpose"].=$row[csf("issue_purpose")].",";
                $all_data[$row[csf("prod_id")]]["item_group_id"]=$row[csf("item_group_id")];
                $all_data[$row[csf("prod_id")]]["item_description"]=$row[csf("item_description")];
                $all_data[$row[csf("prod_id")]]["item_group_name"]=$row[csf("item_name")];
                $all_data[$row[csf("prod_id")]]["cons_amount"]+=$row[csf("cons_amount")];
                $all_data[$row[csf("prod_id")]]["tot_cons_quantity"]+=$row[csf("cons_quantity")];

            }
        }


        $i=1;
        ob_start();
        ?>
        <div align="center" style="height:auto; margin:0 auto; padding:0; width:1150px">
            <table width="1130" cellpadding="0" cellspacing="0" id="caption" align="left">
                <thead>
                <tr style="border:none;">
                    <td colspan="10" align="center" class="form_caption" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td>
                </tr>
                <tr style="border:none;">
                    <td colspan="10" class="form_caption" align="center" style="border:none; font-size:14px;">
                        <b>Company Name :
                            <?
                                foreach ($company_arr as $company){
                                    echo chop($company[csf("company_name")].', ',",");
                                }

                            ?></b>
                    </td>
                </tr>
                <tr style="border:none;">
                    <td colspan="10" align="center" class="form_caption" style="border:none;font-size:14px; font-weight:bold">
                    <? if($txt_date_from!="" && $txt_date_to !="") echo "Report Date From".": ".change_date_format($txt_date_from,'dd-mm-yyyy')." To ". change_date_format($txt_date_to,'dd-mm-yyyy');?>
                    </td>
                </tr>
                </thead>
            </table>
            <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="1130" rules="all" id="rpt_table_header" align="left">
                <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="60">Prod. ID</th>
                    <th width="140">Basis</th>
                    <th width="140">Purpose</th>
                    <th width="120">Item Category</th>
                    <th width="120">Item Group</th>
                    <th width="180">Item Description</th>
                    <th width="100">Issue Qnty</th>
                    <th width="110">Issue Avg Rate</th>
                    <th>Issue Amount(Tk)</th>
                </tr>
                </thead>
            </table>
            <div style="width:1150px; max-height:250px; overflow-y:scroll; overflow-x:hidden;" id="scroll_body">
                <table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="1130" rules="all" align="left">
                    <?

                        foreach($all_data as $row)
                        {
                            if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";

                            $tot_rcv_qnty+=$row[("tot_cons_quantity")];
                            $amount+=$row[("cons_amount")];

                            //$porduct_data_all=$item_category[$row[("item_category")]]."*".$row[("item_group_name")]."*".$row[("item_description")];

                            $avg_rate=$row[("cons_amount")]/$row[("tot_cons_quantity")];

                            $iss_basis_arr=array_unique(explode(",",chop($row["issue_basis"],',')));
                            $iss_basis_data="";
                            foreach($iss_basis_arr as $basis_id)
                            {
                                // $iss_basis_data.=$issue_basis[$basis_id].",";
                                $iss_basis_data.=$receive_basis_arr[$basis_id].",";
                            }
                            $iss_basis_data=chop($iss_basis_data,",");

                            $iss_purpose_arr=array_unique(explode(",",chop($row["issue_purpose"],',')));
                            $iss_purpose_data="";
                            foreach($iss_purpose_arr as $purpose_id)
                            {
                                if($purpose_id)
                                {
                                    // $iss_purpose_data.=$general_issue_purpose[$purpose_id].",";
                                    $iss_purpose_data.=$yarn_issue_purpose[$purpose_id].",";
                                }

                            }
                            $iss_purpose_data=chop($iss_purpose_data,",");


                            ?>
                            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="30" align="center"><? echo $i; ?>&nbsp;</td>
                                <td width="60" align="center"><? echo $row["prod_id"]; ?>&nbsp;</td>
                                <td width="140"><? echo $iss_basis_data; ?>&nbsp;</td>
                                <td width="140"><? echo $iss_purpose_data; ?>&nbsp;</td>
                                <td width="120"><? echo $item_category[$row[("item_category")]]; ?></td>
                                <td width="120"><? echo $row[("item_group_name")]; ?></td>
                                <td width="180"><? echo $row[("item_description")]; ?></td>
                                <td width="100" align="right" title="">
                                    <a href="##"  onClick="issue_qnty_dtls(<? echo $row["prod_id"]; ?>, '<? echo $date_range?>',
                                            'issue_quantity_dtls_popup')
                                            "><? echo number_format($row[("tot_cons_quantity")],4)
                                        ; ?></a></td>
                                <td width="110" align="right"><? echo number_format($avg_rate,4); ?> </td>
                                <td align="right"><? echo number_format($row["cons_amount"],2); ?></td>
                            </tr>
                            <?
                            $i++;
                        }
                    ?>
                </table>
            </div>
            <table width="1130" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
                <tfoot>
                <tr>
                    <th width="30">&nbsp; </th>
                    <th width="60">&nbsp; </th>
                    <th width="140">&nbsp; </th>
                    <th width="140">&nbsp; </th>
                    <th width="120">&nbsp; </th>
                    <th width="120"></th>
                    <th width="180"  style="text-align: right" >Total: &nbsp;</th>
                    <th width="100" style="text-align: right" id="value_tot_issue_qty"><? echo number_format($tot_rcv_qnty,2); ?>&nbsp;</th>
                    <th width="110"></th>
                    <th id="value_tot_amnt_qty" style="text-align: right"><? echo number_format($amount,2); ?>&nbsp;</th>
                </tr>
                </tfoot>
            </table>
        </div>
        <?
        $html = ob_get_contents();
        ob_clean();
        //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
        foreach (glob("*.xls") as $filename) {
            //if( @filemtime($filename) < (time()-$seconds_old) )
            @unlink($filename);
        }
        //---------end------------//
        $name=time();
        $filename=$user_id."_".$name.".xls";
        $create_new_doc = fopen($filename, 'w');
        $is_created = fwrite($create_new_doc, $html);
        echo "$html**$filename**$report_type";
        exit();
    }

    if($action == "generate_report_all")
    {
        $process = array(&$_POST);
        extract(check_magic_quote_gpc($process));
        $report_title=str_replace("'","",$report_title);
        $cbo_company_name=str_replace("'","",$cbo_company_name);
        $txt_date_from=str_replace("'","",$txt_date_from);
        $cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
        $item_group_id=str_replace("'","",$item_group_id);
        $txt_date_to=str_replace("'","",$txt_date_to);
        $txt_item_account_id=str_replace("'","",$txt_item_account_id);
        if($db_type==2)
        {
            $txt_date_from=change_date_format($txt_date_from,'','',1);
            $txt_date_to=change_date_format($txt_date_to,'','',1);
        }
        if($db_type==0)
        {
            $txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
            $txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
        }

        if ($cbo_item_category_id !="") $category_cond= " and item_category in($cbo_item_category_id)"; else $category_cond=" and item_category in(5,6,7,19,20,22,23,39)";


        $sql_cond="";

        if ($cbo_item_category_id !="") $sql_cond= " and b.item_category_id in($cbo_item_category_id)"; else $sql_cond.=" and b.item_category_id in(5,6,7,19,20,22,23,39)";
        if ($item_group_id !="") $sql_cond.=" and b.item_group_id='$item_group_id'";
        //if ($txt_date_from !="" && $txt_date_to) $sql_cond.="  and a.transaction_date BETWEEN '$txt_date_from' and '$txt_date_to'";
        if ($txt_item_account_id !="") $sql_cond.=" and b.id in($txt_item_account_id)"; 

        $sql = "select a.prod_id, a.item_category, a.cons_amount, a.transaction_date, a.transaction_type,
        b.id as product_id, b.item_category_id, b.product_name_details as item_description, b.item_group_id, c.id as group_id, c.item_name,
        (case when a.transaction_date between'".$txt_date_from."' and '".$txt_date_to."' and a.transaction_type in(1,3) then
        a.cons_quantity else 0 end) as rcv_qty,

		(case when a.transaction_date between'".$txt_date_from."' and '".$txt_date_to."' and a.transaction_type in(1,3) then
        a.cons_amount else 0 end) as rcv_amount,

        (case when a.transaction_date between'".$txt_date_from."' and '".$txt_date_to."' and a.transaction_type in(2) then
        a.cons_quantity else 0 end) as issue_qty,

		(case when a.transaction_date between'".$txt_date_from."' and '".$txt_date_to."' and a.transaction_type in(2) then
		a.cons_amount else 0 end) as issue_cons_amount

        from  inv_transaction a, product_details_master b, lib_item_group c
        where a.prod_id=b.id and b.item_group_id=c.id and a.company_id in($cbo_company_name)
        and a.transaction_type in(1,2,3)  and a.status_active=1 and a.is_deleted=0
	 	and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond order by a.prod_id";

        // echo $sql;//die;
        $result = sql_select($sql);

        $all_data=array();
        $date_range=$txt_date_from."*".$txt_date_to;
        $company_arr = sql_select("select id, company_name from lib_company where id in($cbo_company_name)");
        foreach($result as $row)
        {

            if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==4)
            {

                $all_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
                $all_data[$row[csf("prod_id")]]["item_category"]=$row[csf("item_category")];
                $all_data[$row[csf("prod_id")]]["item_group_id"]=$row[csf("item_group_id")];
                $all_data[$row[csf("prod_id")]]["item_description"]=$row[csf("item_description")];
                $all_data[$row[csf("prod_id")]]["item_group_name"]=$row[csf("item_name")];
                $all_data[$row[csf("prod_id")]]["rcv_qty"]+=$row[csf("rcv_qty")];
                $all_data[$row[csf("prod_id")]]["rcv_amount"]+=$row[csf("rcv_amount")];
                $all_data[$row[csf("prod_id")]]["issue_qty"]+=$row[csf("issue_qty")];
                $all_data[$row[csf("prod_id")]]["issue_cons_amount"]+=$row[csf("issue_cons_amount")];

            }
        }


        $i=1;
        ob_start();
        ?>
        <div align="center" style="height:auto; margin:0 auto; padding:0; width:1250px">
            <table width="1230" cellpadding="0" cellspacing="0" id="caption" align="left">
                <thead>
                <tr style="border:none;">
                    <td colspan="10" align="center" class="form_caption" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td>
                </tr>
                <tr style="border:none;">
                    <td colspan="10" class="form_caption" align="center" style="border:none; font-size:14px;">
                        <b>Company Name :
                            <?
                                foreach ($company_arr as $company){
                                    echo chop($company[csf("company_name")].', ',",");
                                }

                            ?></b>
                    </td>
                </tr>
                <tr style="border:none;">
                    <td colspan="10" align="center" class="form_caption" style="border:none;font-size:14px; font-weight:bold">
                    <? if($txt_date_from!="" && $txt_date_to !="") echo "Report Date From".": ".change_date_format($txt_date_from,'dd-mm-yyyy')." To ". change_date_format($txt_date_to,'dd-mm-yyyy');?>
                    </td>
                </tr>
                </thead>
            </table>
            <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="1230" rules="all" id="rpt_table_header" align="left">
                <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="60">Prod. ID</th>
                    <th width="120">Item Category</th>
                    <th width="120">Item Group</th>
                    <th width="180">Item Description</th>
                    <th width="100">Receive Qnty</th>
                    <th width="140">Rcv Avg Rate</th>
                    <th width="140">Rcv Amount(Tk)</th>
                    <th width="100">Issue Qnty</th>
                    <th width="110">Issue Avg Rate</th>
                    <th>Issue Amount(Tk)</th>
                </tr>
                </thead>
            </table>
            <div style="width:1250px; max-height:250px; overflow-y:scroll; overflow-x:hidden;" id="scroll_body">
                <table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="1230" rules="all" align="left">
                    <?

                        foreach($all_data as $row)
                        {
                            if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";

                            $tot_rcv_qnty+=$row[("rcv_qty")];
                            $tot_issue_qnty+=$row[("issue_qty")];
                            $rcv_amount+=$row[("rcv_amount")];
                            $issue_amount+=$row[("issue_cons_amount")];

                            $rcv_avg_rate=$row[("rcv_amount")]/$row[("rcv_qty")];
                            $issue_avg_rate=$row[("issue_cons_amount")]/$row[("issue_qty")];


                            ?>
                            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="30" align="center"><? echo $i; ?>&nbsp;</td>
                                <td width="60" align="center"><? echo $row["prod_id"]; ?>&nbsp;</td>
                                <td width="120"><? echo $item_category[$row[("item_category")]]; ?></td>
                                <td width="120"><? echo $row[("item_group_name")]; ?></td>
                                <td width="180"><? echo $row[("item_description")]; ?></td>
                                <td width="100" align="right" title="">
                                    <a href="##"  onClick="receive_qnty_dtls(<? echo $row["prod_id"]; ?>, '<? echo $date_range?>',
                                            'receive_quantity_dtls_popup')
                                            "><? echo number_format($row[("rcv_qty")],2)
                                        ; ?></a></td>
                                <td width="140" align="right"><? echo number_format($rcv_avg_rate,4); ?> </td>
                                <td width="140" align="right"><? echo number_format($row["rcv_amount"],2); ?></td>
                                <td width="100" align="right"><a href="##"  onClick="issue_qnty_dtls(<? echo $row["prod_id"]; ?>, '<? echo
                                    $date_range?>',
                                            'issue_quantity_dtls_popup')
                                            "><? echo number_format($row["issue_qty"],4);  ?>&nbsp;</a></td>
                                <td width="110" align="right"><? echo number_format($issue_avg_rate,4); ?>&nbsp;</td>
                                <td align="right"><? echo number_format($row["issue_cons_amount"],2);  ?>&nbsp;</td>
                            </tr>
                            <?
                            $i++;
                        }
                    ?>
                </table>
            </div>
            <table width="1230" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
                <tfoot>
                <tr>
                    <th width="30">&nbsp; </th>
                    <th width="60">&nbsp; </th>
                    <th width="120">&nbsp; </th>
                    <th width="120"></th>
                    <th width="180"  style="text-align: right" >Total: &nbsp;</th>
                    <th width="100" id="value_tot_rcv_qty">&nbsp;<? echo number_format($tot_rcv_qnty,2); ?>&nbsp; </th>
                    <th width="140">&nbsp; </th>
                    <th width="140" id="value_tot_rcv_amnt_qty"><? echo number_format($rcv_amount,2); ?>&nbsp; </th>
                    <th width="100" style="text-align: right" id="value_tot_issue_qty"><? echo number_format($tot_issue_qnty,2); ?>&nbsp;</th>
                    <th width="110"></th>
                    <th id="value_tot_issue_amnt_qty" style="text-align: right"><? echo number_format($issue_amount,2); ?>&nbsp;</th>
                </tr>
                </tfoot>
            </table>
        </div>
        <?
        $html = ob_get_contents();
        ob_clean();
        //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
        foreach (glob("*.xls") as $filename) {
            //if( @filemtime($filename) < (time()-$seconds_old) )
            @unlink($filename);
        }
        //---------end------------//
        $name=time();
        $filename=$user_id."_".$name.".xls";
        $create_new_doc = fopen($filename, 'w');
        $is_created = fwrite($create_new_doc, $html);
        echo "$html**$filename**$report_type";
        exit();
    }


    if ($action=="receive_quantity_dtls_popup")
    {
        echo load_html_head_contents("Receive Quantity Details", "../../../../", 1, 1,$unicode,'','');
        extract($_REQUEST);
        $transaction_date=explode("*",$transaction_date);
        $transaction_date_from= "'$transaction_date[0]'";
        $transaction_date_to  = "'$transaction_date[1]'";


        $sql_rcv_dtls="select a.id, a.receive_date, a.recv_number_prefix_num as mrr_num, b.mst_id, b.order_qnty, b.prod_id,
                      b.transaction_type, b.cons_quantity as rcv_qnty
		from inv_receive_master a, inv_transaction b
		where a.id=b.mst_id and b.prod_id=$prod_id and b.transaction_type in(1,3) and b.transaction_date between $transaction_date_from and
		$transaction_date_to and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 order by b.mst_id";

        $data_array=sql_select($sql_rcv_dtls);
        ?>
        <div style="width:430px;">
            <table align="center" cellspacing="0" width="430" border="1" rules="all" class="rpt_table" >
                <thead>
                <tr>
                    <th width="50">SL</th>
                    <th width="150" >Receive Date</th>
                    <th width="150" >MRR Number</th>
                    <th>Receive Quantity</th>
                </tr>
                </thead>
                <tbody>
                <?
                    $i=1;
                    foreach($data_array as $row)
                    {
                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor;?>">
                            <td align="center"><? echo $i; ?></td>
                            <td align="right"><? echo $row[csf("receive_date")]; ?></td>
                            <td align="right"><? echo $row[csf("mrr_num")]; ?></td>
                            <td align="right"><?  echo $row[csf("rcv_qnty")]; ?></td>
                        </tr>
                        <?
                        $i++;
                    }
                ?>
                </tbody>
            </table>
        </div>
        <?
        exit();
    }

    if ($action=="issue_quantity_dtls_popup")
    {
        echo load_html_head_contents("Receive Quantity Details", "../../../../", 1, 1,$unicode,'','');
        extract($_REQUEST);
        //echo $porduct_data_all."==".$prod_id;die;
        $transaction_date=explode("*",$transaction_date);
        $transaction_date_from= "'$transaction_date[0]'";
        $transaction_date_to  = "'$transaction_date[1]'";


        $sql_issue_dtls="select a.id, a.issue_date, a.issue_number_prefix_num as issue_num, b.mst_id, b.order_qnty, b.prod_id,
                      b.transaction_type, b.cons_quantity as issue_qnty
		from inv_issue_master a, inv_transaction b
		where a.id=b.mst_id and b.prod_id=$prod_id and b.transaction_type=2 and b.transaction_date between $transaction_date_from and
		$transaction_date_to and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 order by b.mst_id";
        //echo $sql_issue_dtls;//die;
        $data_array=sql_select($sql_issue_dtls);
        ?>
        <div style="width:430px;">
            <table align="center" cellspacing="0" width="430" border="1" rules="all" class="rpt_table" >
                <thead>
                <tr>
                    <th width="50">SL</th>
                    <th width="150" >Issue Date</th>
                    <th width="150" >Issue No</th>
                    <th>Issue Quantity</th>
                </tr>
                </thead>
                <tbody>
                <?
                    $i=1;
                    foreach($data_array as $row)
                    {
                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor;?>">
                            <td align="center"><? echo $i; ?></td>
                            <td align="right"><? echo $row[csf("issue_date")]; ?></td>
                            <td align="right"><? echo $row[csf("issue_num")]; ?></td>
                            <td align="right"><?  echo $row[csf("issue_qnty")]; ?></td>
                        </tr>
                        <?
                        $i++;
                    }
                ?>
                </tbody>
            </table>
        </div>
        <?
        exit();
    }

    if ($action=="item_group_popup")
    {
        echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
        extract($_REQUEST);
        $data=explode('_',$data);
        //print_r ($data);
        ?>
        <script>
            function js_set_value(id)
            {
                document.getElementById('item_name_id').value=id;
                parent.emailwindow.hide();
            }
        </script>
        <input type="hidden" id="item_name_id" />
        <?
        if ($data[1]==0) $item_category =""; else $item_category =" and item_category in($data[1])";
        // $item_category;
        $sql="SELECT id,item_name from  lib_item_group where status_active=1 and is_deleted=0 $item_category"; //id=$data[1] and

        echo  create_list_view("list_view", "Item Name", "350","500","330",0, $sql , "js_set_value", "id,item_name", "", 1, "0", $arr , "item_name", 		"periodical_purchase_report_controller",'setFilterGrid("list_view",-1);','0') ;
        exit();
    }
?>
