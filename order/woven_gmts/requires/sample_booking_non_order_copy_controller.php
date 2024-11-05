<?

header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data'  $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/sample_booking_non_order_copy_controller', this.value, 'cbo_sample_type', 'sampletd' )" );
	exit();	
} 
if ($action=="load_drop_down_buyer_pop")
{
	echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data'  $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();	
}

if ($action=="fabric_booking_popup")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
?>
     
	<script>
	 
	function js_set_value(booking_no,company,buyer,style)
	{
		document.getElementById('selected_booking').value=booking_no;
                document.getElementById('selected_company').value=company;
                document.getElementById('selected_buyer').value=buyer;
                document.getElementById('selected_style').value=style;
		parent.emailwindow.hide();
	}
	
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="830" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                   <thead>
                           <th colspan="2"> </th>
                        	<th  >
                              <?
                               echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" );
                              ?>
                            </th>
                            <th colspan="3"></th>
                     </thead>
                    <thead>                	 
                        <th width="150" class="must_entry_caption">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="100">Booking No</th>
                        <th width="80">Style Desc.</th>
                        <th width="200">Date Range</th><th></th>           
                    </thead>
        			<tr>
                    	<td> <input type="hidden" id="selected_booking"><input type="hidden" id="selected_company"><input type="hidden" id="selected_buyer"><input type="hidden" id="selected_style">
							<? 
								//if($_SESSION['logic_erp']['company_id'])$company_cond=" and id in(".$_SESSION['logic_erp']['company_id'].")"; else $company_cond="";
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'sample_booking_non_order_copy_controller', this.value, 'load_drop_down_buyer_pop', 'buyer_td' );");
							?>
                        </td>
                   	<td id="buyer_td">
                     <? 
						echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --" );
					?>	</td>
                    
                    <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
                    <td><input name="txt_style_desc" id="txt_style_desc" class="text_boxes" style="width:80px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td> 
            		 <td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style_desc').value, 'create_booking_search_list_view', 'search_div', 'sample_booking_non_order_copy_controller','setFilterGrid(\'table_body\',1)')" style="width:100px;" /></td>
        		</tr>
             </table>
          </td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle">
            <? 
			echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );		
			?>
			<? echo load_month_buttons();  ?>
            </td>
            </tr>
        <tr>
            <td align="center"valign="top" id="search_div"> 
	
            </td>
        </tr>
    </table>    
    
    </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	$style_desc=$data[7];
	if ($data[0]!=0) $company="  a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	
	if ($data[1]!=0){$buyer=" and a.buyer_id='$data[1]'";}
	else{$buyer="";}
	
	if($db_type==0)
	 {
		  // $booking_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[4]";
		  $booking_year_cond=" and YEAR(a.insert_date)=$data[4]";
		  if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' 
		  and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date =""; 
     }
	if($db_type==2)
	 {
		  $booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]";
		  if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."'
		  and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	 }
if($data[6]==4 || $data[6]==0)
		{
			if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]%'  $booking_year_cond  "; else $booking_cond="";
			if (str_replace("'","",$data[7])!="") $style_des_cond=" and b.style_des like '%$data[7]%' "; else $style_des_cond="";
		}
    if($data[6]==1)
		{
			if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num ='$data[5]'   "; else $booking_cond="";
			if (str_replace("'","",$data[7])!="") $style_des_cond=" and b.style_des='$data[7]' "; else $style_des_cond="";
		}
   if($data[6]==2)
		{
			if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[5]%'  $booking_year_cond  "; else $booking_cond="";
			if (str_replace("'","",$data[7])!="") $style_des_cond=" and b.style_des like '$data[7]%' "; else $style_des_cond="";
		}
	if($data[6]==3)
		{
			if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]'  $booking_year_cond  "; else $booking_cond="";
			if (str_replace("'","",$data[7])!="") $style_des_cond=" and b.style_des like '%$data[7]' "; else $style_des_cond="";
		}
	
	


	$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
    $approved=array(0=>"No",1=>"Yes");
    $is_ready=array(0=>"No",1=>"Yes",2=>"No"); 
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,4=>$item_category,5=>$fabric_source,6=>$suplier,7=>$style_library,9=>$approved,10=>$is_ready);
	 $sql= "select a.booking_no_prefix_num, a.booking_no,a.booking_date,a.company_id,a.buyer_id,a.item_category,a.fabric_source,a.supplier_id,a.is_approved,a.ready_to_approved,a.pay_mode,b.style_id,b.style_des from wo_non_ord_samp_booking_mst  a left join wo_non_ord_samp_booking_dtls b on a.booking_no=b.booking_no  and b.status_active=1 and b.is_deleted=0  where   $company". set_user_lavel_filtering(' and a.buyer_id','buyer_id')." $buyer $booking_date $booking_cond $style_des_cond and a.booking_type=4 and  a.status_active=1 and a.is_deleted=0  order by booking_no"; 
	//echo $sql;
	//echo create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Fabric Nature,Fabric Source,Supplier,Style,Style Desc.,Approved,Is-Ready", "100,80,100,100,80,80,80,50,80,50","950","320",0, $sql , "js_set_value", "booking_no", "", 1, "0,0,company_id,buyer_id,item_category,fabric_source,supplier_id,style_id,0,is_approved,ready_to_approved", $arr , "booking_no_prefix_num,booking_date,company_id,buyer_id,item_category,fabric_source,supplier_id,style_id,style_des,is_approved,ready_to_approved", '','','0,3,0,0,0,0,0,0,0,0,0,0','','');
	?>
   <table class="rpt_table scroll" width="1080" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
   <thead>
        <th width="50">Sl</th> 
        <th width="100">Booking No</th>  
        <th width="80">Booking Date</th>           	 
        <th width="100">Company</th>
        <th width="100">Buyer</th>
        <th width="80">Fabric Nature</th>
        <th width="80">Fabric Source</th>
        <th width="80">Pay Mode</th>
        <th width="80">Supplier</th>
        <th width="50">Style</th>
        <th width="80">Style Desc.</th>
        <th width="50">Approved</th>
        <th width="50">Is-Ready</th>
        </thead>
        <tbody>
        <? 
		$i=1;
		$sql_data=sql_select($sql);
		foreach($sql_data as $row){
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";    
		?>
        <tr bgcolor="<? echo $bgcolor;?>" onClick="js_set_value('<? echo $row[csf('booking_no')]  ?>','<? echo $row[csf('company_id')];?>','<? echo $row[csf('buyer_id')];?>','<? echo $style_library[$row[csf('style_id')]];?>')" style="cursor:pointer">
        <td width="50"><? echo $i;?></td> 
        <td width="100"><? echo $row[csf('booking_no_prefix_num')];?></td>  
        <td width="80"><? echo date("d-m-Y",strtotime($row[csf('booking_date')]));?></td>           	 
        <td width="100"><? echo $comp[$row[csf('company_id')]];?></td>
        <td width="100"><? echo $buyer_arr[$row[csf('buyer_id')]];?></td>
        <td width="80"><? echo $item_category[$row[csf('item_category')]];?></td>
        <td width="80"><? echo $fabric_source[$row[csf('fabric_source')]];?></td>
        <td width="80">
        <? echo $pay_mode[$row[csf('pay_mode')]];?>
        </td>
        <td width="80">
		<? 
		if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5){
			echo $comp[$row[csf('supplier_id')]];
		}
		else{
			echo $suplier[$row[csf('supplier_id')]];
		}
		?>
        </td>
        <td width="50" style="word-wrap: break-word;word-break: break-all;"><? echo $style_library[$row[csf('style_id')]];?></td>
        <td width="80" style="word-wrap: break-word;word-break: break-all;"><? echo $row[csf('style_des')];?></td>
        <td width="50"><? echo $approved[$row[csf('is_approved')]];?></td>
        <td width="50"><? echo $is_ready[$row[csf('ready_to_approved')]];?></td>
        </tr>
        <?
		$i++;
         }
        ?>
        </tbody>
    </table>
    <?
}



if($action == "save_update_delete_copy_booking")
{

    $process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if($db_type==0)
		{
		$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SMN', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_non_ord_samp_booking_mst where company_id=$cbo_company_name and booking_type=4 and YEAR(insert_date)=".date('Y',time())." order by booking_no_prefix_num desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		}
		if($db_type==2)
		{
		$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SMN', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_non_ord_samp_booking_mst where company_id=$cbo_company_name and booking_type=4 and to_char(insert_date,'YYYY')=".date('Y',time())." order by booking_no_prefix_num desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		}
    
                $id=return_next_id( "id", "wo_non_ord_samp_booking_mst", 1 ) ;
                
                $book_data = sql_select("select buyer_id,item_category,fabric_source,currency_id,exchange_rate,pay_mode,source,booking_date,delivery_date,supplier_id,attention,ready_to_approved,team_leader,dealing_marchant from wo_non_ord_samp_booking_mst where booking_no = $txt_booking_no");
				
				 $crnt_booking_date=date('d-M-Y');//set 'booking date' as 'current date' when copy booking
                
		$field_array="id,booking_type,booking_no_prefix,booking_no_prefix_num,booking_no,company_id,buyer_id,item_category,fabric_source,currency_id,exchange_rate,pay_mode,source,booking_date,delivery_date,supplier_id,attention,ready_to_approved,team_leader,dealing_marchant,copy_from,inserted_by,insert_date"; 
                $data_array ="(".$id.",4,'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",'".$book_data[0][csf('buyer_id')]."','".$book_data[0][csf('item_category')]."','".$book_data[0][csf('fabric_source')]."','".$book_data[0][csf('currency_id')]."','".$book_data[0][csf('exchange_rate')]."','".$book_data[0][csf('pay_mode')]."','".$book_data[0][csf('source')]."','".$crnt_booking_date."','".$book_data[0][csf('delivery_date')]."','".$book_data[0][csf('supplier_id')]."','".$book_data[0][csf('attention')]."','".$book_data[0][csf('ready_to_approved')]."','".$book_data[0][csf('team_leader')]."','".$book_data[0][csf('dealing_marchant')]."',".$txt_booking_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
               // echo "10** insert into wo_non_ord_samp_booking_mst ($field_array) values $data_array";die;
                $rID=sql_insert("wo_non_ord_samp_booking_mst",$field_array,$data_array,0);

    
                $dtls_id=return_next_id( "id", "wo_non_ord_samp_booking_dtls", 1 ) ;
                $add_comma=0;
                if($db_type==0)
                {
                $field_array2="id,booking_no,style_id,style_des,sample_type,body_part,body_type_id,item_qty,knitting_charge,color_type_id,lib_yarn_count_deter_id,construction,composition, fabric_description,gsm_weight,gmts_color,fabric_color,gmts_size,item_size,dia_width,finish_fabric,process_loss,grey_fabric,rate,amount,bh_qty,rf_qty,yarn_breack_down,process_loss_method,article_no,yarn_details,remarks,fabric_source,dtls_id,inserted_by,insert_date,delivery_date";
                }
                if($db_type==2)
                {
                $field_array2="id,booking_no,style_id,style_des,sample_type,body_part,body_type_id,item_qty,knitting_charge,color_type_id,lib_yarn_count_deter_id,construction,composition, fabric_description,gsm_weight,gmts_color,fabric_color,gmts_size,item_size,dia_width,finish_fabric,process_loss,grey_fabric,rate,amount,bh_qty,rf_qty,yarn_breack_down,process_loss_method,article_no,yarn_details,remarks,fabric_source,dtls_id,inserted_by,insert_date,delivery_date";
                }

                $dtls_data = sql_select("select style_id,style_des,sample_type,body_part,body_type_id,item_qty,knitting_charge,color_type_id,lib_yarn_count_deter_id,construction,composition, fabric_description,gsm_weight,gmts_color,fabric_color,gmts_size,item_size,dia_width,finish_fabric,process_loss,grey_fabric,rate,amount,bh_qty,rf_qty,yarn_breack_down,process_loss_method,article_no,yarn_details,remarks,fabric_source,dtls_id from wo_non_ord_samp_booking_dtls where booking_no = $txt_booking_no and status_active = 1 and is_deleted = 0");
                
                //echo "select style_id,style_des,sample_type,body_part,body_type_id,item_qty,knitting_charge,color_type_id,lib_yarn_count_deter_id,construction,composition, fabric_description,gsm_weight,gmts_color,fabric_color,gmts_size,item_size,dia_width,finish_fabric,process_loss,grey_fabric,rate,amount,bh_qty,rf_qty,yarn_breack_down,process_loss_method,article_no,yarn_details,remarks,fabric_source,dtls_id from wo_non_ord_samp_booking_dtls where booking_no = $txt_booking_no";die;
                //print_r($dtls_data);die;

                 
                foreach($dtls_data as $row)
                {
                     if ($add_comma!=0) 
                    {
                        $data_array2 .=",";

                    }
                    $data_array2.="(".$dtls_id.",'".$new_booking_no[0]."','".$row[csf('style_id')]."','".$row[csf('style_des')]."','".$row[csf('sample_type')]."','".$row[csf('body_part')]."','".$row[csf('body_type_id')]."','".$row[csf('item_qty')]."','".$row[csf('knitting_charge')]."','".$row[csf('color_type_id')]."','".$row[csf('lib_yarn_count_deter_id')]."','".$row[csf('construction')]."','".$row[csf('composition')]."','".$row[csf('fabric_description')]."','".$row[csf('gsm_weight')]."','".$row[csf('gmts_color')]."','".$row[csf('fabric_color')]."','".$row[csf('gmts_size')]."','".$row[csf('item_size')]."','".$row[csf('dia_width')]."','".$row[csf('finish_fabric')]."','".$row[csf('process_loss')]."','".$row[csf('grey_fabric')]."','".$row[csf('rate')]."','".$row[csf('amount')]."','".$row[csf('bh_qty')]."','".$row[csf('rf_qty')]."','".$row[csf('yarn_breack_down')]."','".$row[csf('process_loss_method')]."','".$row[csf('article_no')]."','".$row[csf('yarn_details')]."','".$row[csf('remarks')]."','".$row[csf('fabric_source')]."','".$row[csf('dtls_id')]."','".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."','".$row[csf('delivery_date')]."')";
                    
                    $dtls_id=$dtls_id+1;
                    $add_comma++;
                }
                $rID_in2 = true;
                 if ($data_array2!="")
		 {
			$rID_in2=sql_insert("wo_non_ord_samp_booking_dtls",$field_array2,$data_array2,0);
		 }
               
                 
                $field_array3="id,wo_non_ord_samp_book_dtls_id,booking_no,count_id,copm_one_id,percent_one,type_id,cons_ratio,cons_qnty,inserted_by,insert_date";
                $wo_non_ord_samp_yarn_dtls_id=return_next_id( "id", "wo_non_ord_samp_yarn_dtls", 1 ) ;
                $add_comma_yarn=0;
                $yarn_dtls_data = sql_select("select wo_non_ord_samp_book_dtls_id, booking_no,count_id,copm_one_id,percent_one,type_id,cons_ratio,cons_qnty from wo_non_ord_samp_yarn_dtls where booking_no = $txt_booking_no  and status_active = 1 and is_deleted = 0");
                foreach ($yarn_dtls_data as $row)
                {
                    if ($add_comma_yarn!=0) 
                    {
                        $data_array3 .=",";

                    }
                    $data_array3 .="(".$wo_non_ord_samp_yarn_dtls_id.",'".$row[csf('wo_non_ord_samp_book_dtls_id')]."','".$new_booking_no[0]."','".$row[csf('count_id')]."','".$row[csf('copm_one_id')]."','".$row[csf('percent_one')]."','".$row[csf('type_id')]."','".$row[csf('cons_ratio')]."','".$row[csf('cons_qnty')]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
                    $wo_non_ord_samp_yarn_dtls_id=$wo_non_ord_samp_yarn_dtls_id+1;
                    $add_comma_yarn++;
                }
                
                $rID_in3=true;
                if ($data_array3!="")
                {
                       $rID_in3=sql_insert("wo_non_ord_samp_yarn_dtls",$field_array3,$data_array3,0);
                }
                
                //echo "10**insert into wo_non_ord_samp_booking_dtls (".$field_array2.") Values ".$data_array2."";die;
                
                $acc_id=return_next_id( "id", "wo_non_ord_booking_acc_dtls", 1 ) ;
		$field_array4="id,booking_no,item_group_id,description,uom,qty,remarks";
                 

                $acc_dtls_data = sql_select("select booking_no,item_group_id,description,uom,qty,remarks from wo_non_ord_booking_acc_dtls where booking_no = $txt_booking_no ");
               
               $acc_comma = 0; 
                foreach ($acc_dtls_data as $row)
		 {
                        if($acc_comma != 0)
                        {
                            $data_array4 .= ","; 
                        }
			$data_array4.="(".$acc_id.",'".$new_booking_no[0]."','".$row[csf('item_group_id')]."','".$row[csf('description')]."','".$row[csf('uom')]."','".$row[csf('qty')]."','".$row[csf('remarks')]."')";
			$acc_id=$acc_id+1;
                        $acc_comma++;
		 }
                
                $rID_in4 = true;
                if($data_array4 != ""){
                    $rID_in4=sql_insert("wo_non_ord_booking_acc_dtls",$field_array4,$data_array4,1);
                }
                
                $term_id=return_next_id( "id", "wo_booking_terms_condition", 1 ) ;
		$field_array5="id,booking_no,terms";
                $term_data = sql_select("select terms from wo_booking_terms_condition where booking_no = $txt_booking_no");
                $term_comma = 0;
                foreach ($term_data as $row)
                {
                    if ($term_comma!=0) $data_array5 .=",";
                    $data_array5 .="(".$term_id.",'".$new_booking_no[0]."','".$row[csf('terms')]."')";
                    $term_id=$term_id+1;
                }
                $rID_in5 = true;
                if($data_array5 != ""){
                    $rID_in5=sql_insert("wo_booking_terms_condition",$field_array5,$data_array5,1);
                }
		if($db_type==0)
		{
			if($rID && $rID_in2 && $rID_in3 && $rID_in4 && $rID_in5){
				mysql_query("COMMIT");  
				echo "0**".$new_booking_no[0];
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_booking_no[0];
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_in2 && $rID_in3 && $rID_in4 && $rID_in5){
				oci_commit($con);  
				echo "0**".$new_booking_no[0];
			}
			else{
				oci_rollback($con);   
				echo "10**".$new_booking_no[0];
			}
		}
		disconnect($con);
		die;
    
   

}

?>
