<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="load_drop_down_knitting_com")
{
    $data = explode("**",$data);
    $company_id=$data[1];

    if($data[0]==1)
    {
        echo create_drop_down( "cbo_service_company", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select --", $company_id, "","" );
    }
    else if($data[0]==3)
    {   
        echo create_drop_down( "cbo_service_company", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "" );
    }
    else
    {
        echo create_drop_down( "cbo_service_company", 152, $blank_array,"",1, "-- Select --", 0, "" );
    }
    exit();
}

if ($action=="load_drop_down_buyer")
{
    echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
} 



if($action=="check_booking_no")
{
    $data=explode("**",$data);
    
    $sql= "select a.id,a.booking_no_prefix_num,a.booking_no,a.booking_date,a.company_id,a.buyer_id,c.job_no_prefix_num, a.job_no, a.po_break_down_id, a.process,a.item_category, a.fabric_source, a.supplier_id from wo_booking_mst a ,wo_po_details_master c where $company $buyer $booking_date and  a.booking_type=3 and  a.status_active=1 and a.is_deleted=0 and a.job_no=c.job_no  $booking_cond $job_cond group by a.id,a.booking_no_prefix_num, a.booking_no, a.job_no, a.booking_date, a.company_id, a.buyer_id, a.po_break_down_id, a.item_category, a.fabric_source, c.job_no_prefix_num, a.supplier_id,a.process order by booking_no_prefix_num desc"; 
    
    $sql="select id, booking_no,job_no from wo_booking_mst where booking_no='".trim($data[0])."' and company_id='".$data[1]."' and is_deleted=0 and status_active=1";
    $data_array=sql_select($sql,1);
    if(count($data_array)>0)
    {
        echo $data_array[0][csf('id')]."**".$data_array[0][csf('booking_no')]."**".$data_array[0][csf('job_no')];
    }
    else
    {
        echo "0";
    }
    exit(); 
}

if($action=="fabric_receive_print")
{
    extract($_REQUEST);
    $data=explode('*',$data);
    
    $company_id=$data[0];
    $update_id=$data[1];

    $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
    $supplier_arr = return_library_array("select id, short_name from lib_supplier","id","short_name");
    $buyer_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
    $company_array=return_library_array( "select id, company_name from lib_company", "id", "company_name");

    //print_r($supplier_arr[266]);die;
    $job_array=array();
    $job_sql="select a.buyer_name, a.job_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
    $job_sql_result=sql_select($job_sql);
    foreach($job_sql_result as $row)
    {
        $job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
        $job_array[$row[csf('id')]]['buyer_id']=$row[csf('buyer_name')];
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
    <div>
        <table width="1000" cellspacing="0">
            <tr>
                <td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_array[$data[0]]; ?></strong></td>
            </tr>
            <tr class="form_caption">
                <td  align="left">
                    <?
                    $data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
                    foreach($data_array as $img_row)
                    {
                        ?>
                        <img src='../../../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50' align="middle" />   
                        <? 
                    }
                    ?>
          
                </td>
                <td colspan="6" align="center" style="font-size:14px">  
                    <?
                        $company_array=array();
                        $nameArray=sql_select( "select id, company_name, company_short_name,plot_no,level_no,road_no,block_no,country_id, province,city, zip_code,email,website from lib_company where id=$company_id"); 
                        foreach ($nameArray as $result)
                        { 
                            $company_array['shortname']=$result[csf('company_short_name')];
                            $company_array['name']=$result[csf('company_name')];
                        ?>
                            Plot No: <? echo $result['plot_no']; ?> 
                            Level No: <? echo $result['level_no']?>
                            Road No: <? echo $result['road_no']; ?> 
                            Block No: <? echo $result['block_no'];?> 
                            City No: <? echo $result['city'];?> 
                            Zip Code: <? echo $result['zip_code']; ?> 
                            Province No: <?php echo $result['province'];?> 
                            Country: <? echo $country_arr[$result['country_id']]; ?><br> 
                            Email Address: <? echo $result['email'];?> 
                            Website No: <? echo $result['website'];
                        }
                    ?> 
                </td>  
            </tr>
            <?php 
                 $mst_sql=sql_select( "select id,challan_no, $year_field recv_number_prefix_num, recv_number, dyeing_source, dyeing_company, receive_date from inv_receive_mas_batchroll where id=$update_id and entry_form=206 and status_active=1 and is_deleted=0 and company_id=$company_id $search_field_cond $date_cond order by id"); 
            
            //print_r($mst_sql);
            ?>
            
            <tr>
                <td colspan="6" align="center" style="font-size:18px"><strong><u><? echo $data[2]; ?> Challan</u></strong></td>
            </tr>
            <tr>
                 <td width="100"><strong>Company:</strong> </td>
                 <td width="100"> <? echo $company_array['name'] ;  ?></td>
                <td width="120"><strong>Service Source :</strong></td><td width="160px"><? echo $knitting_source[$mst_sql[0][csf('dyeing_source')]]; ?></td>
                  <td width="125"><strong>Return Number </strong></td><td width="175px"><? echo $mst_sql[0][csf('recv_number')]; ?>
                </td>
               
            </tr>
            <tr>
                <td><strong>Receive Date:</strong></td><td width="175px"><? echo change_date_format($mst_sql[0][csf('receive_date')]); ?></td>
                 <td width="125"><strong>Service Company:</strong></td><td width="175px">
                 <?
                    echo $supplier_arr[$mst_sql[0][csf('dyeing_company')]]; 
                 ?>
                </td>
            </tr>
        </table>
        <br>
        <table cellspacing="0" width="1030"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="90">Body Part</th>
                <th width="130">Const./compo</th>
                <th width="50">Fin Dia/Color</th>
                <th width="70">Process </th>
                <th width="100">Booking No</th>
                <th width="60">Return Qty</th>
               <!-- <th width="50">Wo. Qty</th>
                <th width="50">Cur. Rcv. Qty </th>-->
                <th width="60"> Amount</th>
                <th width="100">Order</th>
                <th width="70">Buyer/Job</th>
            </thead>
            <?
              
            
                $sql="select id,mst_id,outbound_batchname,booking_no,booking_id,body_part_id,febric_description_id,width,color_id, process_id,wo_qty, batch_issue_qty,rate,amount,return_qty,currency_id,exchange_rate,buyer_id, job_no, order_id from pro_grey_batch_dtls where mst_id=$update_id order by id " ;
                //echo $sql;
                $i=1;
                $result=sql_select($sql);
                foreach($result as $row)
                {
                ?>
                    <tr>
                        <td><? echo $i; ?></td>
                        <td><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
                        <td style="word-break:break-all;"><? echo $composition_arr[$row[csf("febric_description_id")]]; ?></td>
                        <td style="word-break:break-all;"><? echo $row[csf('width')]."<hr/>".$color_arr[$row[csf("color_id")]]; ?></td>
                        <td><? echo $conversion_cost_head_array[$row[csf("process_id")]];?></td>
                        <td><? echo $row[csf("booking_no")]; ?></td>
                        <td style="word-break:break-all;" align="right"><? echo  $row[csf("return_qty")] ?></td>
                       <?php /*?> <td style="word-break:break-all;" align="right"><? echo $row[csf("wo_qty")]; ?></td>
                        <td style="word-break:break-all;" align="right"><? echo $row[csf("batch_issue_qty")];?></td><?php */?>
                        <td style="word-break:break-all;" align="right"><? echo  $row[csf("amount")] ?></td>
                        <td style="word-break:break-all;"><? echo $job_array[$row[csf('order_id')]]['po']; ?></td>
                        <td style="word-break:break-all;"><? echo $buyer_array[$row[csf('buyer_id')]]."<hr/>".$row[csf('job_no')]; ?></td>
                    </tr>
                <?
                    $i++;
                    //$total_wo+=$row[csf("wo_qty")];
                    //$total_rece+=$row[csf("batch_issue_qty")];
                    $total_amount+=$row[csf("amount")];
					$total_return_qty+=$row[csf("return_qty")];
					
                }
            ?>
            <tfoot>
                <th width=""></th>
                <th width=""></th>
                <th width=""></th>
                <th width=""></th>
                <th width=""> </th>
                <th width="">Total</th>
                <th width="" align="right"><?php echo $total_return_qty;?></th>
                <?php /*?><th width="" align="right"><?php echo $total_wo;?></th>
                <th width="" align="right"><?php echo $total_rece;?></th><?php */?>
                <th width="" align="right"><?php echo $total_amount;?></th>
                <th width=""></th>
                <th width=""></th>
            
            </tfoot>
        </table>
    </div>
    <? echo signature_table(131, $company_id, "900px"); ?>
   
<?
exit();
}



if ($action=="batch_number_popup")
{
    echo load_html_head_contents("Batch Number Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    //echo $cbo_service_source;die;
?>
    <script>
        function js_set_value(id,batch_no,color_id,withorder,service_source)
        {
            $('#hidden_batch_id').val(id);
            $('#hidden_batch_no').val(batch_no);
            $('#hidden_color_id').val(color_id);
            $('#hidden_booking_withorder').val(withorder);
            $('#hidden_service_source').val(service_source);
            parent.emailwindow.hide();
        }
    </script>
</head>

<body>
<div align="center" style="width:910px;">
    <form name="searchbatchnofrm"  id="searchbatchnofrm">
        <fieldset style="width:910px; margin-left:10px">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="770" class="rpt_table">
                <thead>
                    <th width="240">Batch Date Range</th>
                    <th width="170">Search By</th>
                    <th id="search_by_td_up" width="200">Please Enter Batch No</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                        <input type="hidden" name="hidden_batch_id" id="hidden_batch_id" class="text_boxes" value="">
                        <input type="hidden" name="hidden_batch_no" id="hidden_batch_no" class="text_boxes" value="">
                        <input type="hidden" name="hidden_color_id" id="hidden_color_id" class="text_boxes" value="">
                        <input type="hidden" name="hidden_service_source" id="hidden_service_source" class="text_boxes" value="">
                        <input type="hidden" name="hidden_booking_withorder" id="hidden_booking_withorder" class="text_boxes" value="">
                    </th>
                </thead>
                <tr class="general">
                    <td>
                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px;">To
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px;">
                    </td>
                    <td>
                        <?
                            $search_by_arr=array(0=>"Batch No",1=>"Fabric Booking no.",2=>"Color");
                            $dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
                            echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td align="center" id="search_by_td" width="140px">
                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+<? echo $cbo_service_source; ?>, 'create_batch_search_list_view', 'search_div', 'fabric_service_receive_return_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                    <td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
            </table>
            <div style="width:100%; margin-top:5px" id="search_div" align="left"></div>
        </fieldset>
    </form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}


if($action=="create_batch_search_list_view")
{
    $data = explode("_",$data);
    $search_string="%".trim($data[0])."%";
    $search_by=$data[1];
    $start_date =$data[2];
    $end_date =$data[3];
    $company_id =$data[4];
    $service_source =$data[5];
    
    $po_arr=array();
    $po_data=sql_select("select id, po_number, job_no_mst from wo_po_break_down");  
    foreach($po_data as $row)
    {
        $po_arr[$row[csf('id')]]['po_no']=$row[csf('po_number')];
        $po_arr[$row[csf('id')]]['job_no']=$row[csf('job_no_mst')];
    }
    

    if($start_date!="" && $end_date!="")
    {
        if($db_type==0)
        {
            $date_cond="and batch_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
        }
        else
        {
            $date_cond="and batch_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
        }
    }
    else
    {
        $date_cond="";
    }
    
    if(trim($data[0])!="")
    {
        if($search_by==0)
            $search_field_cond="and batch_no like '$search_string'";
        else if($search_by==1)
            $search_field_cond="and booking_no like '$search_string'";
        else
            $search_field_cond="and color_id in(select id from lib_color where color_name like '$search_string')";
    }
    else
    {
        $search_field_cond="";
    }
    
    
    
    if($db_type==0)
    {
        $order_id_arr=return_library_array( "select mst_id, group_concat(po_id) as po_id from pro_batch_create_dtls where status_active=1 and is_deleted=0 group by mst_id",'mst_id','order_id');
    }
    else
    {
        $order_id_arr=return_library_array( "select mst_id, LISTAGG(cast(po_id as VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY po_id) as po_id from pro_batch_create_dtls where status_active=1 and is_deleted=0 group by mst_id",'mst_id','po_id');
    }
    $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
    
    $sql = "select id, batch_no, extention_no, batch_date, batch_weight, booking_no, color_id, batch_against, booking_without_order, re_dyeing_from from pro_batch_create_mst where entry_form=0 and batch_for=1 and batch_against<>4 and company_id=$company_id and status_active=1 and is_deleted=0 and re_dyeing_from=0 $search_field_cond $date_cond"; 
    
    
    //echo $sql;//die; 
    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table" >
        <thead>
            <th width="40">SL</th>
            <th width="90">Batch No</th>
            <th width="80">Extention No</th>
            <th width="80">Batch Date</th>
            <th width="80">Batch Qnty</th>
            <th width="115">Booking No</th>
            <th width="110">Color</th>
            <th width="130">Batch Source</th>
            <th>Po No</th>
        </thead>
    </table>
    <div style="width:910px; overflow-y:scroll; max-height:250px;" id="buyer_list_view" align="center">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="890" class="rpt_table" id="tbl_list_search" >
        <?
            $i=1;
            $nameArray=sql_select( $sql );
            foreach ($nameArray as $selectResult)
            {
                $po_no=''; $job_array=array();
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                
                $order_id=array_unique(explode(",",$order_id_arr[$selectResult[csf('id')]]));
                foreach($order_id as $value)
                {
                    if($po_no=='') $po_no=$po_arr[$value]['po_no']; else $po_no.=",".$po_arr[$value]['po_no'];
                    $job_no=$po_arr[$value]['job_no'];
                    if(!in_array($job_no,$job_array))
                    {
                        $job_array[]=$job_no;
                    }
                }
                $job_no=implode(",",$job_array);
                
                
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>,'<? echo $selectResult[csf('batch_no')]; ?>',<? echo $selectResult[csf('color_id')]; ?>,<? echo $selectResult[csf('booking_without_order')]; ?>,1)"> 
                    <td width="40" align="center"><? echo $i; ?></td>   
                    <td width="90"><p><? echo $selectResult[csf('batch_no')]; ?></p></td>
                    <td width="80"><p><? if($selectResult[csf('extention_no')]!=0) echo $selectResult[csf('extention_no')]; ?>&nbsp;</p></td>
                    <td width="80" align="center"><? echo change_date_format($selectResult[csf('batch_date')]); ?></td>
                    <td width="80" align="right"><? echo $selectResult[csf('batch_weight')]; ?>&nbsp;</td> 
                    <td width="115"><p><? echo $selectResult[csf('booking_no')]; ?>&nbsp;</p></td>
                    <td width="110"><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
                    <td width="130"><p>In-house</p></td>
                    <td><p><? echo $po_no; ?>&nbsp;</p></td>    
                </tr>
                <?
                $i++;
            }
        
    //============================For Outbound Subcontact=========================================================================================  
            
        if($start_date!="" && $end_date!="")
        {
            if($db_type==0)
            {
                $date_cond_out="and a.receive_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
            }
            else
            {
                $date_cond_out="and a.receive_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
            }
        }
        else
        {
            $date_cond_out="";
        }
        
        if(trim($data[0])!="")
        {
            if($search_by==0)
                $search_field_cond_out="and b.outbound_batchname like '$search_string'";
            else if($search_by==1)
                $search_field_cond_out="and b.booking_no like '$search_string'";
            else
                $search_field_cond_out="and b.color_id in(select id from lib_color where color_name like '$search_string')";
        }
        else
        {
            $search_field_cond_out="";
        }
        
        
        $sql_out = "select a.id as mst_id, b.outbound_batchname as batch_no, 0 as extention_no, a.receive_date as batch_date, b.batch_issue_qty as batch_weight, b.booking_no, b.color_id, null batch_against, 0 as booking_without_order, null re_dyeing_from, b.id, b.order_id 
        from  inv_receive_mas_batchroll a, pro_grey_batch_dtls b where a.id=b.mst_id and a.entry_form=92 and a.dyeing_source=3 and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond_out $date_cond_out";
        $nameArray_out=sql_select( $sql_out );
        foreach ($nameArray_out as $selectResult)
        {
            $po_no=''; $job_array=array();
            if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            
            $po_no=$po_arr[$selectResult[csf("order_id")]]['po_no'];
        
            
            
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>,'<? echo $selectResult[csf('batch_no')]; ?>',<? echo $selectResult[csf('color_id')]; ?>,<? echo $selectResult[csf('booking_without_order')]; ?>,2)"> 
                <td width="40" align="center"><? echo $i; ?></td>   
                <td width="90"><p><? echo $selectResult[csf('batch_no')]; ?></p></td>
                <td width="80"><p><? if($selectResult[csf('extention_no')]!=0) echo $selectResult[csf('extention_no')]; ?>&nbsp;</p></td>
                <td width="80" align="center"><? echo change_date_format($selectResult[csf('batch_date')]); ?></td>
                <td width="80" align="right"><? echo $selectResult[csf('batch_weight')]; ?>&nbsp;</td> 
                <td width="115"><p><? echo $selectResult[csf('booking_no')]; ?>&nbsp;</p></td>
                <td width="110"><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
                <td width="130"><p>Subcon Outbound</p></td>
                <td><p><? echo $po_no; ?>&nbsp;</p></td>    
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






// new  *******************************************************************************************************

if ($action=="service_booking_popup")
{
    echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
    extract($_REQUEST);

    $prebookingNos = "'".implode("','",array_filter(array_unique(explode("_",chop($prebookingNos,"_")))))."'";
?>
     
<script>
    function js_set_value(booking_no)
    {
        document.getElementById('selected_booking').value=booking_no;
        parent.emailwindow.hide();
    }
</script>

</head>
<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
    <table width="1000" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
        <tr>
            <td align="center" width="100%">
                <table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                    <thead>
                            <th  colspan="8">
                              <?
                              echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --",1 );
                              ?>
                            </th>
                    </thead>
                    <thead>                  
                        <th width="150">Company Name</th>
                        <th width="150">Supplier Name</th>
                        <th width="150">Buyer  Name</th>
                        <th width="100">Job  No</th>
                        <th width="100">Style No.</th>
                        <th width="100">Booking No</th>
                        <th width="200">Date Range</th>
                        <th></th>           
                    </thead>
                    <tr>
                        <td> <input type="hidden" id="selected_booking">
                            <? 
                                echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", "".$company_id."", "load_drop_down( 'fabric_service_receive_return_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",1);
                            ?>
                        </td>
                        <td>
                            <?php 

                                if($cbo_service_source==3)
                                {
                                    echo create_drop_down( "cbo_supplier_name", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 and b.party_type in (21,24,25) group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", "".$supplier_id."", "",1 );
                                }
                                else
                                {
                                    echo create_drop_down( "cbo_supplier_name", 152, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name", 1, "-- Select --", "".$supplier_id."", "",1 );
                                }


                                //echo create_drop_down( "cbo_supplier_name", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 and b.party_type in (21,24,25) group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", "".$supplier_id."", "",1 );
                            ?>
                        </td>
                        <td id="buyer_td">
                        <? 
                            echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
                        ?>
                        </td>
                        <td>
                             <input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:70px">
                        </td> 
                        <td>
                             <input name="txt_style" id="txt_style" class="text_boxes" style="width:70px">
                        </td>
                        <td>
                            <input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:70px">
                        </td> 
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px">
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px">
                        </td> 
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_style').value+'_'+<? echo $prebookingNos;?>, 'create_booking_search_list_view', 'search_div', 'fabric_service_receive_return_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
                </tr>
             </table>
          </td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle"><? echo load_month_buttons(1);  ?>
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
    if ($data[0]!=0) $company="  a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
    if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
    if ($data[7]!=0) $supplier=" and a.supplier_id='$data[7]'"; else $supplier="";
    
    if($db_type==0)
    {
        if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
    }
    
    if($db_type==2)
    {
        if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
    }
    //echo $data[8];
    if($data[6]==1)
    {
        if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num='$data[5]'    "; else  $booking_cond="";
        if (str_replace("'","",$data[5])!="") $booking_cond_nonOrder=" and a.wo_no_prefix_num='$data[5]'    "; else  $booking_cond_nonOrder="";
        if (str_replace("'","",$data[5])!="") $booking_cond_nonOrder_knit_dye=" and a.prefix_num='$data[5]'    "; else  $booking_cond_nonOrder_knit_dye="";
        if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num='$data[4]'  "; else  $job_cond=""; 
        if (str_replace("'","",$data[8])!="") $style_cond=" and c.style_ref_no='$data[8]'  "; else  $style_cond=""; 
    }
    if($data[6]==4 || $data[6]==0)
    {
        if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]%'  $booking_year_cond  "; else  $booking_cond="";
        if (str_replace("'","",$data[5])!="") $booking_cond_nonOrder=" and a.wo_no_prefix_num like '%$data[5]%'  $booking_year_cond  "; else  $booking_cond_nonOrder="";
        if (str_replace("'","",$data[5])!="") $booking_cond_nonOrder_knit_dye=" and a.prefix_num like '%$data[5]%'  $booking_year_cond  "; else  $booking_cond_nonOrder_knit_dye="";
        if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '%$data[4]%'  $year_cond  "; else  $job_cond=""; 
        if (str_replace("'","",$data[8])!="") $style_cond=" and c.style_ref_no like '%$data[8]%'  "; else  $style_cond=""; 
    }
    
    if($data[6]==2)
    {
        if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[5]%'  $booking_year_cond  "; else  $booking_cond="";
        if (str_replace("'","",$data[5])!="") $booking_cond_nonOrder=" and a.wo_no_prefix_num like '$data[5]%'  $booking_year_cond  "; else  $booking_cond_nonOrder="";
        if (str_replace("'","",$data[5])!="") $booking_cond_nonOrder_knit_dye=" and a.prefix_num like '$data[5]%'  $booking_year_cond  "; else  $booking_cond_nonOrder_knit_dye="";
        if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '$data[4]%'  $year_cond  "; else  $job_cond=""; 
        if (str_replace("'","",$data[8])!="") $style_cond=" and c.style_ref_no like '$data[8]%'  "; else  $style_cond=""; 
    }
    
    if($data[6]==3)
    {
        if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]'  $booking_year_cond  "; else  $booking_cond="";
        if (str_replace("'","",$data[5])!="") $booking_cond_nonOrder=" and a.wo_no_prefix_num like '%$data[5]'  $booking_year_cond  "; else  $booking_cond_nonOrder="";
        if (str_replace("'","",$data[5])!="") $booking_cond_nonOrder_knit_dye=" and a.prefix_num like '%$data[5]'  $booking_year_cond  "; else  $booking_cond_nonOrder_knit_dye="";
        if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '%$data[4]'  $year_cond  "; else  $job_cond="";
        if (str_replace("'","",$data[8])!="") $style_cond=" and c.style_ref_no like '%$data[8]'  "; else  $style_cond="";  
    } 
     

    if ($data[9]!="")
    {
        foreach(explode(",", $data[9]) as $bok){
            $bookingnos .= "'".$bok."',";
        }
        $bookingnos = chop($bookingnos,",");

        $preBookingNos_1 = " and a.booking_no not in (".$bookingnos.")";
        $preBookingNos_2 = " and a.wo_no not in (".$bookingnos.")";
    }




    $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
    $comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
    $suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
    $po_no=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
    
    $arr=array (2=>$comp,3=>$conversion_cost_head_array,4=>$buyer_arr,7=>$po_no,8=>$item_category,9=>$fabric_source,10=>$suplier);
    
    $sql= "select a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, c.job_no_prefix_num,c.style_ref_no, c.job_no, a.po_break_down_id, a.process, a.item_category, a.fabric_source, a.supplier_id, 1 as type 
    from wo_booking_mst a ,wo_booking_dtls e, wo_po_details_master c ,pro_grey_batch_dtls b,inv_receive_mas_batchroll d
    where $company $buyer $booking_date $style_cond and  a.booking_type=3 and  a.status_active=1 and a.is_deleted=0 and e.job_no=c.job_no and a.booking_no = e.booking_no   $booking_cond $job_cond  $supplier and a.booking_no = b.booking_no and d.id = b.mst_id and d.entry_form = 91 $preBookingNos_1
    group by a.id,a.booking_no_prefix_num, a.booking_no, c.job_no, a.booking_date, a.company_id, a.buyer_id, a.po_break_down_id, a.item_category, a.fabric_source, c.job_no_prefix_num,c.style_ref_no, a.supplier_id,a.process
    union all
    select a.id,a.wo_no_prefix_num as booking_no_prefix_num, a.wo_no as booking_no, a.booking_date, a.company_id, a.buyer_id, null as job_no_prefix_num,null as style_ref_no, null as job_no, null as po_break_down_id, 0 as process, 0 as item_category, a.aop_source as fabric_source, a.supplier_id, 2 as type 
    from wo_non_ord_aop_booking_mst a ,pro_grey_batch_dtls b,inv_receive_mas_batchroll d
    where $company $buyer $booking_date and a.wo_no = b.booking_no and d.id = b.mst_id  and  a.status_active=1 and a.is_deleted=0  $booking_cond_nonOrder  $supplier $preBookingNos_2
     group by  a.id,a.wo_no_prefix_num,a.wo_no,a.booking_date, a.company_id, a.buyer_id,a.aop_source,a.supplier_id
    union all
      select a.id,a.prefix_num as booking_no_prefix_num,a.booking_no,a.booking_date,a.company_id,a.buyer_id,null as job_no_prefix_num,null as style_ref_no,   null as job_no, null as po_break_down_id, 0 as process,0 as item_category, 0 as fabric_source, a.supplier_id, 2 as type  
      from wo_non_ord_knitdye_booking_mst a ,pro_grey_batch_dtls b,inv_receive_mas_batchroll d  
      where $company $buyer $booking_date $booking_cond_nonOrder_knit_dye $preBookingNos_1 and a.booking_no = b.booking_no and d.id = b.mst_id and a.status_active=1 and a.is_deleted=0 $supplier 
      group by a.id,a.prefix_num,  a.booking_no,a.booking_date,a.company_id,a.buyer_id,a.supplier_id
    order by booking_no_prefix_num desc";
    
    //echo $sql; 
    
    echo  create_list_view("list_view", "Booking No,Booking Date,Company,Process,Buyer,Job No.,Style No.,PO number,Fabric Nature,Fabric Source,Supplier", "70,80,80,100,100,70,110,150,80,80","1070","320",0, $sql , "js_set_value", "id,booking_no,job_no,type", "", 1, "0,0,company_id,process,buyer_id,0,0,po_break_down_id,item_category,fabric_source,supplier_id", $arr , "booking_no_prefix_num,booking_date,company_id,process,buyer_id,job_no_prefix_num,style_ref_no,po_break_down_id,item_category,fabric_source,supplier_id", '','','0,3,0,0,0,0,0,0,0,0,0','','');
}

if ($action=="fabric_detls_list_view")
{
    $data=explode("**",$data);
    $booking_no=$data[0];
    $job_no=$data[1];
    $booking_id=$data[2];
    $type=$data[4];
    
    //echo $type;die;
    
    $color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
    $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
    $buyer_id_arr=return_library_array( "select job_no, buyer_name from wo_po_details_master",'job_no','buyer_name');
    $order_name_arr=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
    
    /*$sql_previous_receive=sql_select("select batch_issue_qty,body_part_id ,febric_description_id,color_id ,process_id from pro_grey_batch_dtls  where booking_no ='$booking_no' and status_active=1 and is_deleted=0");
    $previous_receive_arr=array();
    foreach($sql_previous_receive as $row)
    {
        $previous_receive_arr[$row[csf('body_part_id')]][$row[csf('process_id')]][$row[csf('febric_description_id')]][$row[csf('color_id')]]+=$row[csf('batch_issue_qty')];
        
    }*/
    
    $previousReceiveArrNew = array();
    $previousReceiveRes=sql_select("select a.batch_issue_qty,a.booking_no, a.booking_dtls_id,a.id  from pro_grey_batch_dtls a, inv_receive_mas_batchroll b  
        where a.mst_id = b.id and a.booking_no ='$booking_no' and a.status_active=1 and a.is_deleted=0 and b.entry_form = 92");
    foreach($previousReceiveRes as $row2)
    {
        $previousReceiveArrNew[$row2[csf('booking_no')]][$row2[csf('booking_dtls_id')]]+=$row2[csf('batch_issue_qty')];
        
    }
    
    if($type==1)
    {
        
        $wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no='$job_no'");
        foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
        {
            if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
            {
                $fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,lib_yarn_count_deter_id,gsm_weight from  wo_pre_cost_fabric_cost_dtls where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
                list($fabric_description_row)=$fabric_description;
                $fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]['bodypart']=$fabric_description_row[csf("body_part_id")];
                $fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]['fabric_description']=$fabric_description_row[csf("fabric_description")];
                $fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]['lib_yarn_count_deter_id']=$fabric_description_row[csf("lib_yarn_count_deter_id")];
                $fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]['gsm']=$fabric_description_row[csf("gsm_weight")];
            }
            
        }

        $sql_tmp = "select a.pre_cost_fabric_cost_dtls_id, a.po_break_down_id, a.gmts_color_id as fabric_color_id, a.process , a.job_no, a.description, c.rate, c.batch_wgt as workorder_qty, c.batch_issue_qty,c.booking_dtls_id, a.dia_width, a.fin_gsm, b.currency_id, b.exchange_rate , c.id as batch_dtls_id,c.booking_no,c.outbound_batchname
        from wo_booking_dtls a,wo_booking_mst b,pro_grey_batch_dtls c  ,inv_receive_mas_batchroll d
        where a.booking_no=b.booking_no and b.booking_no = c.booking_no and a.id = c.booking_dtls_id and c.mst_id = d.id and d.entry_form = 91 and  a.booking_type=3 and  a.booking_no='$booking_no' and  a.status_active=1 and a.is_deleted=0  and c.status_active = 1 and c.is_deleted = 0
        order by c.id";

        $booking_dtlsChk = array();$tmp_dataArray=array();
        foreach (sql_select($sql_tmp) as $val) 
        {
            if( $booking_dtlsChk[$val[csf("batch_dtls_id")]] == "")
            {
                $booking_dtlsChk[$val[csf("batch_dtls_id")]] = $val[csf("batch_dtls_id")];

                $tmp_dataArray[$val[csf("booking_dtls_id")]]["pre_cost_fabric_cost_dtls_id"] = $val[csf("pre_cost_fabric_cost_dtls_id")];
                $tmp_dataArray[$val[csf("booking_dtls_id")]]["booking_no"] = $val[csf("booking_no")];
                $tmp_dataArray[$val[csf("booking_dtls_id")]]["outbound_batchname"] = $val[csf("outbound_batchname")];
                $tmp_dataArray[$val[csf("booking_dtls_id")]]["po_break_down_id"] = $val[csf("po_break_down_id")];
                $tmp_dataArray[$val[csf("booking_dtls_id")]]["fabric_color_id"] = $val[csf("fabric_color_id")];
                $tmp_dataArray[$val[csf("booking_dtls_id")]]["process"] = $val[csf("process")];
                $tmp_dataArray[$val[csf("booking_dtls_id")]]["job_no"] = $val[csf("job_no")];
                $tmp_dataArray[$val[csf("booking_dtls_id")]]["description"] = $val[csf("description")];
                $tmp_dataArray[$val[csf("booking_dtls_id")]]["rate"] = $val[csf("rate")];
                $tmp_dataArray[$val[csf("booking_dtls_id")]]["workorder_qty"] = $val[csf("workorder_qty")];
                $tmp_dataArray[$val[csf("booking_dtls_id")]]["batch_issue_qty"] += $val[csf("batch_issue_qty")];
                $tmp_dataArray[$val[csf("booking_dtls_id")]]["dia_width"] = $val[csf("dia_width")];
                //$tmp_dataArray[$val[csf("booking_dtls_id")]]["fin_gsm"] = $val[csf("fin_gsm")];
                $tmp_dataArray[$val[csf("booking_dtls_id")]]["currency_id"] = $val[csf("currency_id")];
                $tmp_dataArray[$val[csf("booking_dtls_id")]]["exchange_rate"] = $val[csf("exchange_rate")];
            }
        }

        $i=$data[3] + 1; 
        foreach ($tmp_dataArray as $booking_dtls_id => $row) 
        {
            $previous_receive_qty=0;
            $previous_receive_qty=$previousReceiveArrNew[$row['booking_no']][$booking_dtls_id];

            if($previous_receive_qty < $row['batch_issue_qty'])
            {

                $tble_body .='<tr id="tr_'.$i.'" align="center" valign="middle"><td width="25" id="sl_'.$i.'">'. $i.'</td><td width="70" id="batchNo_'.$i.'" ><input type="text" id="txtBatchNo_'.$i.'" name="txtBatchNo[]"  style=" width:60px" class="text_boxes" value="'.$row['outbound_batchname'].'"/></td><td width="100" id="inHouseBatchNo_'.$i.'" style="display:none"><input type="text" id="txtinHouseBatchNo_'.$i.'" name="txtinHouseBatchNo[]"  style=" width:80px" class="text_boxes"/></td><td style="word-break:break-all;" width="80" id="bodyPart_'.$i.'">'.$body_part[$fabric_description_array[$row['pre_cost_fabric_cost_dtls_id']]['bodypart']].'</td><td style="word-break:break-all;" width="120" id="'.$i.'" align="left">'.$fabric_description_array[$row['pre_cost_fabric_cost_dtls_id']]['fabric_description'].'</td><td style="word-break:break-all;" width="50" id="dia_'.$i.'">'.$row['dia_width'].'</td><td style="word-break:break-all;" width="50" id="gsm_'.$i.'">'.$fabric_description_array[$row['pre_cost_fabric_cost_dtls_id']]['gsm'].'</td><td style="word-break:break-all;" width="70" id="color_'.$i.'">'.$color_library[$row['fabric_color_id']].'</td><td width="120" align="right" id="">'.create_drop_down( "cboProcess_".$i."", 120, $conversion_cost_head_array,"", 1, "-- Select Process --", "".$row['process']."", "",1,"","","","","","","cboProcess[]" ).'</td><td style="word-break:break-all;" width="60" id="woQty_'.$i.'">'.$row['workorder_qty'].'</td><td width="60" align="center" id=""><input type="text" id="totalreceiveQty_'.$i.'" name="totalreceiveQty[]"  style=" width:40px" class="text_boxes_numeric" readonly value="'.$previous_receive_qty.'"/></td><td width="60" align="center" id=""><input type="text" id="txtReceiveQty_'.$i.'" name="txtReceiveQty[]"  style=" width:40px" class="text_boxes_numeric" onKeyUp="calculate_amount('.$i.');" placeholder="'.$row['batch_issue_qty'].'"/> <input type="hidden" id="txtReceiveQtyHidden_'.$i.'" name="txtReceiveQtyHidden[]" value=""/> </td><td width="60" align="center" id=""><input type="text" id="txtRate_'.$i.'" name="txtRate[]"  style=" width:40px" class="text_boxes_numeric" value="'.$row['rate'].'" readonly/></td><td width="70" align="center" id=""><input type="text" id="txtAmount_'.$i.'" name="txtAmount[]"  style=" width:55px" class="text_boxes_numeric"  readonly/></td><td width="70" align="center" id=""><input type="text" id="txtgreyUsed_'.$i.'" name="txtgreyUsed[]"  style=" width:55px" class="text_boxes_numeric" /></td><td style="word-break:break-all;" width="90" id="bookingNo_'.$i.'" align="left">'.$booking_no.'</td><td style="word-break:break-all;" width="60" id="buyer_'.$i.'">'.$buyer_arr[$buyer_id_arr[$row['job_no']]].'</td><td style="word-break:break-all;" width="80" id="job_'.$i.'">'.$row['job_no'].'</td><td style="word-break:break-all;" width="100" id="order_'.$i.'" align="left">'.$order_name_arr[$row['po_break_down_id']].'</td><td style="word-break:break-all;" width="" id="currency_'.$i.'" align="left">'.create_drop_down( "currencyId_".$i."", 70, $currency,"", 1, "Select", "".$row['currency_id']."", "","","","","","","","","currencyId[]" ).'<input type="hidden" name="hiddnJobNo[]" id="hiddnJobNo_'.$i.'" value="'. $row['job_no'].'"/><input type="hidden" name="progBookPiId[]" id="progBookPiId_'.$i.'" value="'.$booking_id.'"/><input type="hidden" name="orderId[]" id="orderId_'.$i.'" value="'.$row['po_break_down_id'].'"/><input type="hidden" name="colorId[]" id="colorId_'.$i.'" value="'.$row['fabric_color_id'].'"/><input type="hidden" name="dtlsId[]" id="dtlsId_'.$i.'"/><input type="hidden" name="bodypartId[]" id="bodypartId_'.$i.'" value="'.$fabric_description_array[$row['pre_cost_fabric_cost_dtls_id']]['bodypart'].'"/><input type="hidden" name="buyerId[]" id="buyerId_'.$i.'" value="'.$buyer_id_arr[$row['job_no']].'"/><input type="hidden" name="determinationId[]" id="determinationId_'.$i.'" value="'.$fabric_description_array[$row['pre_cost_fabric_cost_dtls_id']]['lib_yarn_count_deter_id'].'"/><input type="hidden" name="currencyId[]" id="currencyId_'.$i.'" value="'.$row['currency_id'].'"/><input type="hidden" name="exchangeRate[]" id="exchangeRate_'.$i.'" value="'.$row['exchange_rate'].'"/><input type="hidden" name="txtBookingNo[]" id="txtBookingNo_'.$i.'" value="'.$booking_no.'"/><input type="hidden" name="finDia[]" id="finDia_'.$i.'" value="'.$row['dia_width'].'"/> <input type="hidden" name="finGsm[]" id="finGsm_'.$i.'" value="'.$fabric_description_array[$row['pre_cost_fabric_cost_dtls_id']]['gsm'].'"/><input type="hidden" name="bookWithoutOrder[]" id="bookWithoutOrder_'.$i.'" value="0"/> <input type="hidden" name="bookingDtlsId[]" id="bookingDtlsId_'.$i.'" value="'.$booking_dtls_id.'"/></td></tr>';
                $i++;
            }

        }



        
       /* $sql="select a.pre_cost_fabric_cost_dtls_id, a.po_break_down_id, a.fabric_color_id, a.process , a.job_no, a.description, sum(a.amount)/sum(a.wo_qnty) as rate, sum(wo_qnty) as workorder_qty, a.dia_width, a.fin_gsm, b.currency_id, b.exchange_rate 
        from wo_booking_dtls a,wo_booking_mst b 
        where a.booking_no=b.booking_no and  a.booking_type=3  and  a.booking_no='$booking_no' and  a.status_active=1 and a.is_deleted=0  group by a.pre_cost_fabric_cost_dtls_id,a.po_break_down_id,a.fabric_color_id, a.process , a.job_no,a.description, a.dia_width, a.fin_gsm, a.pre_cost_fabric_cost_dtls_id,b.currency_id, b.exchange_rate";
        
        //echo $sql;
        
        $dataArray=sql_select($sql);
        $i=$data[3]+count($dataArray);
        foreach($dataArray as $row)
        {
            $previous_receive_qty=0;
            $previous_receive_qty=$previous_receive_arr[$fabric_description_array[$row[csf('pre_cost_fabric_cost_dtls_id')]]['bodypart']][$row[csf('process')]][$fabric_description_array[$row[csf('pre_cost_fabric_cost_dtls_id')]]['lib_yarn_count_deter_id']][$row[csf('fabric_color_id')]];
            $tble_body .='<tr id="tr_'.$i.'" align="center" valign="middle"><td width="25" id="sl_'.$i.'">'. $i.'</td><td width="70" id="batchNo_'.$i.'" ><input type="text" id="txtBatchNo_'.$i.'" name="txtBatchNo[]"  style=" width:60px" class="text_boxes"/></td><td width="100" id="inHouseBatchNo_'.$i.'" style="display:none"><input type="text" id="txtinHouseBatchNo_'.$i.'" name="txtinHouseBatchNo[]"  style=" width:80px" class="text_boxes"/></td><td style="word-break:break-all;" width="80" id="bodyPart_'.$i.'">'.$body_part[$fabric_description_array[$row[csf('pre_cost_fabric_cost_dtls_id')]]['bodypart']].'</td><td style="word-break:break-all;" width="120" id="'.$i.'" align="left">'.$fabric_description_array[$row[csf('pre_cost_fabric_cost_dtls_id')]]['fabric_description'].'</td><td style="word-break:break-all;" width="50" id="dia_'.$i.'">'.$row[csf('dia_width')].'</td><td style="word-break:break-all;" width="50" id="gsm_'.$i.'">'.$row[csf('fin_gsm')].'</td><td style="word-break:break-all;" width="70" id="color_'.$i.'">'.$color_library[$row[csf('fabric_color_id')]].'</td><td width="120" align="right" id="">'.create_drop_down( "cboProcess_".$i."", 120, $conversion_cost_head_array,"", 1, "-- Select Process --", "".$row[csf('process')]."", "",1,"","","","","","","cboProcess[]" ).'</td><td style="word-break:break-all;" width="60" id="woQty_'.$i.'">'.$row[csf('workorder_qty')].'</td><td width="60" align="center" id=""><input type="text" id="totalreceiveQty_'.$i.'" name="totalreceiveQty[]"  style=" width:40px" class="text_boxes_numeric" readonly value="'.$previous_receive_qty.'"/></td><td width="60" align="center" id=""><input type="text" id="txtReceiveQty_'.$i.'" name="txtReceiveQty[]"  style=" width:40px" class="text_boxes_numeric" onKeyUp="calculate_amount('.$i.');"/></td><td width="60" align="center" id=""><input type="text" id="txtRate_'.$i.'" name="txtRate[]"  style=" width:40px" class="text_boxes_numeric" value="'.$row[csf('rate')].'" readonly/></td><td width="70" align="center" id=""><input type="text" id="txtAmount_'.$i.'" name="txtAmount[]"  style=" width:55px" class="text_boxes_numeric"  readonly/></td><td width="70" align="center" id=""><input type="text" id="txtgreyUsed_'.$i.'" name="txtgreyUsed[]"  style=" width:55px" class="text_boxes_numeric" /></td><td style="word-break:break-all;" width="90" id="bookingNo_'.$i.'" align="left">'.$booking_no.'</td><td style="word-break:break-all;" width="60" id="buyer_'.$i.'">'.$buyer_arr[$buyer_id_arr[$row[csf('job_no')]]].'</td><td style="word-break:break-all;" width="80" id="job_'.$i.'">'.$row[csf('job_no')].'</td><td style="word-break:break-all;" width="100" id="order_'.$i.'" align="left">'.$order_name_arr[$row[csf('po_break_down_id')]].'</td><td style="word-break:break-all;" width="" id="currency_'.$i.'" align="left">'.create_drop_down( "currencyId_".$i."", 70, $currency,"", 1, "Select", "".$row[csf('currency_id')]."", "","","","","","","","","currencyId[]" ).'<input type="hidden" name="hiddnJobNo[]" id="hiddnJobNo_'.$i.'" value="'. $row[csf('job_no')].'"/><input type="hidden" name="progBookPiId[]" id="progBookPiId_'.$i.'" value="'.$booking_id.'"/><input type="hidden" name="orderId[]" id="orderId_'.$i.'" value="'.$row[csf('po_break_down_id')].'"/><input type="hidden" name="colorId[]" id="colorId_'.$i.'" value="'.$row[csf('fabric_color_id')].'"/><input type="hidden" name="dtlsId[]" id="dtlsId_'.$i.'"/><input type="hidden" name="bodypartId[]" id="bodypartId_'.$i.'" value="'.$fabric_description_array[$row[csf('pre_cost_fabric_cost_dtls_id')]]['bodypart'].'"/><input type="hidden" name="buyerId[]" id="buyerId_'.$i.'" value="'.$buyer_id_arr[$row[csf('job_no')]].'"/><input type="hidden" name="determinationId[]" id="determinationId_'.$i.'" value="'.$fabric_description_array[$row[csf('pre_cost_fabric_cost_dtls_id')]]['lib_yarn_count_deter_id'].'"/><input type="hidden" name="currencyId[]" id="currencyId_'.$i.'" value="'.$row[csf('currency_id')].'"/><input type="hidden" name="exchangeRate[]" id="exchangeRate_'.$i.'" value="'.$row[csf('exchange_rate')].'"/><input type="hidden" name="txtBookingNo[]" id="txtBookingNo_'.$i.'" value="'.$booking_no.'"/><input type="hidden" name="finDia[]" id="finDia_'.$i.'" value="'.$row[csf('dia_width')].'"/> <input type="hidden" name="finGsm[]" id="finGsm_'.$i.'" value="'.$row[csf('fin_gsm')].'"/><input type="hidden" name="bookWithoutOrder[]" id="bookWithoutOrder_'.$i.'" value="0"/></td></tr>';
            
            $i--;
        }*/
    }
    else
    {
        
  
        $sql_tmp = " select a.body_part, a.lib_yarn_count_deter_id, a.fabric_color as fabric_color_id, d.process_id as process, a.fabric_description , null as job_no, null as po_break_down_id, null as description, b.buyer_id,  a.dia_width, null as fin_gsm, b.currency_id, b.exchange_rate, d.id as batch_dtls_id, d.batch_issue_qty, d.batch_wgt as workorder_qty, d.booking_dtls_id, b.wo_no as booking_no,d.rate,d.outbound_batchname
     from wo_non_ord_samp_booking_dtls a, wo_non_ord_aop_booking_mst b, wo_non_ord_aop_booking_dtls c ,pro_grey_batch_dtls d,inv_receive_mas_batchroll e 
     where b.id=c.wo_id and c.fabric_description=a.id and b.wo_no='$booking_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
      and  b.wo_no = d.booking_no and d.mst_id = e.id and e.entry_form = 91
            union all 
        select a.body_part, a.lib_yarn_count_deter_id, a.fabric_color as fabric_color_id, d.process_id as process , a.fabric_description, null as job_no, null as po_break_down_id, null as description,b.buyer_id, a.dia_width, null as fin_gsm, b.currency_id, b.exchange_rate, d.id as batch_dtls_id, d.batch_issue_qty, d.batch_wgt  as workorder_qty, d.booking_dtls_id, b.booking_no,d.rate,d.outbound_batchname
        from wo_non_ord_samp_booking_dtls a, wo_non_ord_knitdye_booking_mst b,wo_non_ord_knitdye_booking_dtl c, pro_grey_batch_dtls d,inv_receive_mas_batchroll e 
        where a.id = b.fab_booking_id and b.id = c.mst_id and c.id = d.booking_dtls_id and  d.mst_id = e.id and e.entry_form = 91 and b.booking_no = d.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and b.booking_no  ='$booking_no'
      order by batch_dtls_id";

     $booking_dtlsChk = array();
        foreach (sql_select($sql_tmp) as $val) 
        {
            if( $booking_dtlsChk[$val[csf("batch_dtls_id")]] == "")
            {
                $booking_dtlsChk[$val[csf("batch_dtls_id")]] = $val[csf("batch_dtls_id")];
                $tmp_dataArray[$val[csf("booking_dtls_id")]]["booking_no"] = $val[csf("booking_no")];
                $tmp_dataArray[$val[csf("booking_dtls_id")]]["outbound_batchname"] = $val[csf("outbound_batchname")];
                $tmp_dataArray[$val[csf("booking_dtls_id")]]["body_part"] = $val[csf("body_part")];
                $tmp_dataArray[$val[csf("booking_dtls_id")]]["lib_yarn_count_deter_id"] = $val[csf("lib_yarn_count_deter_id")];
                $tmp_dataArray[$val[csf("booking_dtls_id")]]["fabric_color_id"] = $val[csf("fabric_color_id")];
                $tmp_dataArray[$val[csf("booking_dtls_id")]]["process"] = $val[csf("process")];
                $tmp_dataArray[$val[csf("booking_dtls_id")]]["fabric_description"] = $val[csf("fabric_description")];
                $tmp_dataArray[$val[csf("booking_dtls_id")]]["job_no"] = $val[csf("job_no")];
                $tmp_dataArray[$val[csf("booking_dtls_id")]]["po_break_down_id"] = $val[csf("po_break_down_id")];
                $tmp_dataArray[$val[csf("booking_dtls_id")]]["description"] = $val[csf("description")];
                $tmp_dataArray[$val[csf("booking_dtls_id")]]["buyer_id"] = $val[csf("buyer_id")];
                $tmp_dataArray[$val[csf("booking_dtls_id")]]["dia_width"] = $val[csf("dia_width")];
                $tmp_dataArray[$val[csf("booking_dtls_id")]]["fin_gsm"] = $val[csf("fin_gsm")];
                $tmp_dataArray[$val[csf("booking_dtls_id")]]["currency_id"] = $val[csf("currency_id")];
                $tmp_dataArray[$val[csf("booking_dtls_id")]]["exchange_rate"] = $val[csf("exchange_rate")];
                $tmp_dataArray[$val[csf("booking_dtls_id")]]["batch_issue_qty"] += $val[csf("batch_issue_qty")];
                $tmp_dataArray[$val[csf("booking_dtls_id")]]["workorder_qty"] = $val[csf("workorder_qty")];
                $tmp_dataArray[$val[csf("booking_dtls_id")]]["rate"] = $val[csf("rate")];
                $tmp_dataArray[$val[csf("booking_dtls_id")]]["booking_dtls_id"] = $val[csf("booking_dtls_id")]; 
            }
            
        }
        $i=$data[3]+1;
        foreach ($tmp_dataArray as $booking_dtls_id => $row) 
        {
            $previous_receive_qty=0;
            $previous_receive_qty=$previousReceiveArrNew[$row['booking_no']][$booking_dtls_id];

            if($previous_receive_qty < $row['batch_issue_qty'])
            {

                $tble_body .='<tr id="tr_'.$i.'" align="center" valign="middle"><td width="25" id="sl_'.$i.'">'. $i.'</td><td width="70" id="batchNo_'.$i.'"><input type="text" id="txtBatchNo_'.$i.'" name="txtBatchNo[]"  style=" width:60px" class="text_boxes" value="'.$row['outbound_batchname'].'"/></td><td width="100" id="inHouseBatchNo_'.$i.'" style="display:none"><input type="text" id="txtinHouseBatchNo_'.$i.'" name="txtinHouseBatchNo[]"  style=" width:80px" class="text_boxes"/></td><td style="word-break:break-all;" width="80" id="bodyPart_'.$i.'">'.$body_part[$row['body_part']].'</td><td style="word-break:break-all;" width="120" id="'.$i.'" align="left">'.$row['fabric_description'].'</td><td style="word-break:break-all;" width="50" id="dia_'.$i.'">'.$row['dia_width'].'</td><td style="word-break:break-all;" width="50" id="gsm_'.$i.'">'.$row['fin_gsm'].'</td><td style="word-break:break-all;" width="70" id="color_'.$i.'">'.$color_library[$row['fabric_color_id']].'</td><td width="120" align="right" id="">'.create_drop_down( "cboProcess_".$i."", 120, $conversion_cost_head_array,"", 1, "-- Select Process --", "".$row['process']."", "",1,"","","","","","","cboProcess[]" ).'</td><td style="word-break:break-all;" width="60" id="woQty_'.$i.'">'.$row['workorder_qty'].'</td><td width="60" align="center" id=""><input type="text" id="totalreceiveQty_'.$i.'" name="totalreceiveQty[]"  style=" width:40px" class="text_boxes_numeric" readonly value="'.$previous_receive_qty.'"/></td><td width="60" align="center" id=""><input type="text" id="txtReceiveQty_'.$i.'" name="txtReceiveQty[]"  style=" width:40px" class="text_boxes_numeric" onKeyUp="calculate_amount('.$i.');" placeholder="'.$row['batch_issue_qty'].'"/> <input type="hidden" id="txtReceiveQtyHidden_'.$i.'" name="txtReceiveQtyHidden[]" value=""/></td><td width="60" align="center" id=""><input type="text" id="txtRate_'.$i.'" name="txtRate[]"  style=" width:40px" class="text_boxes_numeric" value="'.$row['rate'].'" readonly/></td><td width="70" align="center" id=""><input type="text" id="txtAmount_'.$i.'" name="txtAmount[]"  style=" width:55px" class="text_boxes_numeric"  readonly/></td><td width="70" align="center" id=""><input type="text" id="txtgreyUsed_'.$i.'" name="txtgreyUsed[]"  style=" width:55px" class="text_boxes_numeric" /></td><td style="word-break:break-all;" width="90" id="bookingNo_'.$i.'" align="left">'.$booking_no.'</td><td style="word-break:break-all;" width="60" id="buyer_'.$i.'">'.$buyer_arr[$row['buyer_id']].'</td><td style="word-break:break-all;" width="80" id="job_'.$i.'">'.$row['job_no'].'</td><td style="word-break:break-all;" width="100" id="order_'.$i.'" align="left">'.$order_name_arr[$row['po_break_down_id']].'</td><td style="word-break:break-all;" width="" id="currency_'.$i.'" align="left">'.create_drop_down( "currencyId_".$i."", 70, $currency,"", 1, "Select", "".$row['currency_id']."", "","","","","","","","","currencyId[]" ).'<input type="hidden" name="hiddnJobNo[]" id="hiddnJobNo_'.$i.'" value="'. $row['job_no'].'"/><input type="hidden" name="progBookPiId[]" id="progBookPiId_'.$i.'" value="'.$booking_id.'"/><input type="hidden" name="orderId[]" id="orderId_'.$i.'" value="'.$row['po_break_down_id'].'"/><input type="hidden" name="colorId[]" id="colorId_'.$i.'" value="'.$row['fabric_color_id'].'"/><input type="hidden" name="dtlsId[]" id="dtlsId_'.$i.'"/><input type="hidden" name="bodypartId[]" id="bodypartId_'.$i.'" value="'.$row['body_part'].'"/><input type="hidden" name="buyerId[]" id="buyerId_'.$i.'" value="'.$row['buyer_id'].'"/><input type="hidden" name="determinationId[]" id="determinationId_'.$i.'" value="'.$row['lib_yarn_count_deter_id'].'"/><input type="hidden" name="currencyId[]" id="currencyId_'.$i.'" value="'.$row['currency_id'].'"/><input type="hidden" name="exchangeRate[]" id="exchangeRate_'.$i.'" value="'.$row['exchange_rate'].'"/><input type="hidden" name="txtBookingNo[]" id="txtBookingNo_'.$i.'" value="'.$booking_no.'"/><input type="hidden" name="finDia[]" id="finDia_'.$i.'" value="'.$row['dia_width'].'"/> <input type="hidden" name="finGsm[]" id="finGsm_'.$i.'" value="'.$row['fin_gsm'].'"/><input type="hidden" name="bookWithoutOrder[]" id="bookWithoutOrder_'.$i.'" value="1"/> <input type="hidden" name="bookingDtlsId[]" id="bookingDtlsId_'.$i.'" value="'.$booking_dtls_id.'"/></td></tr>';

                $i++;
            }

        }



        
       /* $sql="select a.body_part, a.lib_yarn_count_deter_id, a.fabric_color as fabric_color_id, 35 as process, a.fabric_description , null as job_no, null as description, b.buyer_id, sum(c.amount)/sum(c.wo_qty) as rate, sum(c.wo_qty) as workorder_qty, a.dia_width, null as fin_gsm, b.currency_id, b.exchange_rate 
        from wo_non_ord_samp_booking_dtls a, wo_non_ord_aop_booking_mst b, wo_non_ord_aop_booking_dtls c 
        where b.id=c.wo_id and c.fabric_description=a.id  and  b.wo_no='$booking_no' and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 
        group by a.body_part, a.lib_yarn_count_deter_id, a.fabric_color, a.fabric_description, b.buyer_id, a.dia_width, b.currency_id, b.exchange_rate";
        
        echo $sql."<br>";
        
        
        $dataArray=sql_select($sql);
        $i=$data[3]+count($dataArray);
        foreach($dataArray as $row)
        {
            $previous_receive_qty=0;
            $previous_receive_qty=$previous_receive_arr[$row[csf('body_part')]][$row[csf('process')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('fabric_color_id')]];
            $tble_body .='<tr id="tr_'.$i.'" align="center" valign="middle"><td width="25" id="sl_'.$i.'">'. $i.'</td><td width="70" id="batchNo_'.$i.'"><input type="radio" id="txtBatchNo_'.$i.'" name="txtBatchNo[]"  style=" width:60px" class="text_boxes"/></td><td width="100" id="inHouseBatchNo_'.$i.'" style="display:none"><input type="text" id="txtinHouseBatchNo_'.$i.'" name="txtinHouseBatchNo[]"  style=" width:80px" class="text_boxes"/></td><td style="word-break:break-all;" width="80" id="bodyPart_'.$i.'">'.$body_part[$row[csf('body_part')]].'</td><td style="word-break:break-all;" width="120" id="'.$i.'" align="left">'.$row[csf('fabric_description')].'</td><td style="word-break:break-all;" width="50" id="dia_'.$i.'">'.$row[csf('dia_width')].'</td><td style="word-break:break-all;" width="50" id="gsm_'.$i.'">'.$row[csf('fin_gsm')].'</td><td style="word-break:break-all;" width="70" id="color_'.$i.'">'.$color_library[$row[csf('fabric_color_id')]].'</td><td width="120" align="right" id="">'.create_drop_down( "cboProcess_".$i."", 120, $conversion_cost_head_array,"", 1, "-- Select Process --", "".$row[csf('process')]."", "",1,"","","","","","","cboProcess[]" ).'</td><td style="word-break:break-all;" width="60" id="woQty_'.$i.'">'.$row[csf('workorder_qty')].'</td><td width="60" align="center" id=""><input type="text" id="totalreceiveQty_'.$i.'" name="totalreceiveQty[]"  style=" width:40px" class="text_boxes_numeric" readonly value="'.$previous_receive_qty.'"/></td><td width="60" align="center" id=""><input type="text" id="txtReceiveQty_'.$i.'" name="txtReceiveQty[]"  style=" width:40px" class="text_boxes_numeric" onKeyUp="calculate_amount('.$i.');"/></td><td width="60" align="center" id=""><input type="text" id="txtRate_'.$i.'" name="txtRate[]"  style=" width:40px" class="text_boxes_numeric" value="'.$row[csf('rate')].'" readonly/></td><td width="70" align="center" id=""><input type="text" id="txtAmount_'.$i.'" name="txtAmount[]"  style=" width:55px" class="text_boxes_numeric"  readonly/></td><td width="70" align="center" id=""><input type="text" id="txtgreyUsed_'.$i.'" name="txtgreyUsed[]"  style=" width:55px" class="text_boxes_numeric" /></td><td style="word-break:break-all;" width="90" id="bookingNo_'.$i.'" align="left">'.$booking_no.'</td><td style="word-break:break-all;" width="60" id="buyer_'.$i.'">'.$buyer_arr[$row[csf('buyer_id')]].'</td><td style="word-break:break-all;" width="80" id="job_'.$i.'">'.$row[csf('job_no')].'</td><td style="word-break:break-all;" width="100" id="order_'.$i.'" align="left">'.$order_name_arr[$row[csf('po_break_down_id')]].'</td><td style="word-break:break-all;" width="" id="currency_'.$i.'" align="left">'.create_drop_down( "currencyId_".$i."", 70, $currency,"", 1, "Select", "".$row[csf('currency_id')]."", "","","","","","","","","currencyId[]" ).'<input type="hidden" name="hiddnJobNo[]" id="hiddnJobNo_'.$i.'" value="'. $row[csf('job_no')].'"/><input type="hidden" name="progBookPiId[]" id="progBookPiId_'.$i.'" value="'.$booking_id.'"/><input type="hidden" name="orderId[]" id="orderId_'.$i.'" value="'.$row[csf('po_break_down_id')].'"/><input type="hidden" name="colorId[]" id="colorId_'.$i.'" value="'.$row[csf('fabric_color_id')].'"/><input type="hidden" name="dtlsId[]" id="dtlsId_'.$i.'"/><input type="hidden" name="bodypartId[]" id="bodypartId_'.$i.'" value="'.$row[csf('body_part')].'"/><input type="hidden" name="buyerId[]" id="buyerId_'.$i.'" value="'.$row[csf('buyer_id')].'"/><input type="hidden" name="determinationId[]" id="determinationId_'.$i.'" value="'.$row[csf('lib_yarn_count_deter_id')].'"/><input type="hidden" name="currencyId[]" id="currencyId_'.$i.'" value="'.$row[csf('currency_id')].'"/><input type="hidden" name="exchangeRate[]" id="exchangeRate_'.$i.'" value="'.$row[csf('exchange_rate')].'"/><input type="hidden" name="txtBookingNo[]" id="txtBookingNo_'.$i.'" value="'.$booking_no.'"/><input type="hidden" name="finDia[]" id="finDia_'.$i.'" value="'.$row[csf('dia_width')].'"/> <input type="hidden" name="finGsm[]" id="finGsm_'.$i.'" value="'.$row[csf('fin_gsm')].'"/><input type="hidden" name="bookWithoutOrder[]" id="bookWithoutOrder_'.$i.'" value="1"/></td></tr>';
            
            $i--;
        }*/
        
    }
    
    //echo $sql;die;
    
    
    echo $tble_body;
}

if($action=="grey_item_details_update")
{
    $data=explode("_",$data);
	//print_r($data);
    $color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
    $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
    $buyer_id_arr=return_library_array( "select job_no, buyer_name from wo_po_details_master",'job_no','buyer_name');
    $order_name_arr=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
    
   /* $sql_previous_receive=sql_select("select batch_issue_qty,body_part_id ,febric_description_id,color_id,booking_id,process_id  from pro_grey_batch_dtls  where  status_active=1 and is_deleted=0");
    $previous_receive_arr=array();
    foreach($sql_previous_receive as $row)
    {
        $previous_receive_arr[$row[csf('booking_id')]][$row[csf('process_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('color_id')]]+=$row[csf('batch_issue_qty')];
    }*/

    $previous_receive_arr = array();
    $sql_previous_receive=sql_select("select a.batch_issue_qty,a.booking_no, a.booking_dtls_id,a.id  from pro_grey_batch_dtls a, inv_receive_mas_batchroll b  
        where a.mst_id = b.id and a.status_active=1 and a.is_deleted=0 and b.entry_form = 92");
    foreach($sql_previous_receive as $row)
    {
        $previous_receive_arr[$row[csf('booking_no')]][$row[csf('booking_dtls_id')]]+=$row[csf('batch_issue_qty')];
        
    }
    
    $previousIssueArrNew = array();
    $previousIssueRes=sql_select("select a.batch_issue_qty,a.booking_no, a.booking_dtls_id,a.id  from pro_grey_batch_dtls a, inv_receive_mas_batchroll b  
        where a.mst_id = b.id and a.status_active=1 and a.is_deleted=0 and b.entry_form = 91");
    foreach($previousIssueRes as $row2)
    {
        $previousIssueArrNew[$row2[csf('booking_no')]][$row2[csf('booking_dtls_id')]]+=$row2[csf('batch_issue_qty')];
        
    }


    $composition_arr=array(); $constructtion_arr=array();
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
    
    //$sql=sql_select("select id, mst_id, outbound_batchname, booking_no, booking_id, body_part_id, febric_description_id, width, color_id, process_id, wo_qty, batch_issue_qty, rate, amount, currency_id, exchange_rate, buyer_id, job_no, order_id, fin_dia, fin_gsm,grey_used,  booking_without_order from pro_grey_batch_dtls where mst_id=$data[0] order by id "  );
	if($data[3]!= "" || $data[4]=='returnSys'){
		
    $sql=sql_select("select a.id, a.mst_id, a.outbound_batchname, a.booking_no, a.booking_id, a.body_part_id, a.febric_description_id, a.width, a.color_id, a.process_id, a.wo_qty, a.batch_issue_qty, a.return_qty,a.rate, a.amount, a.currency_id, a.exchange_rate, a.buyer_id, a.job_no, a.order_id, a.fin_dia, a.fin_gsm,a.grey_used,  a.booking_without_order, a.booking_dtls_id from pro_grey_batch_dtls a, inv_receive_mas_batchroll b where a.mst_id = b.id and a.mst_id=$data[0] and b.entry_form = 206 order by a.id"  );

  
	}
	else
	{
		
		 $sql=sql_select("select a.id, a.mst_id, a.outbound_batchname, a.booking_no, a.booking_id, a.body_part_id, a.febric_description_id, a.width, a.color_id, a.process_id, a.wo_qty, a.batch_issue_qty, a.rate, a.amount, a.currency_id, a.exchange_rate, a.buyer_id, a.job_no, a.order_id, a.fin_dia, a.fin_gsm,a.grey_used,  a.booking_without_order, a.booking_dtls_id from pro_grey_batch_dtls a, inv_receive_mas_batchroll b where a.mst_id = b.id and a.mst_id=$data[0] and b.entry_form = 92 order by a.id"  );

	}
	
	/*echo "select a.id, a.mst_id, a.outbound_batchname, a.booking_no, a.booking_id, a.body_part_id, a.febric_description_id, a.width, a.color_id, a.process_id, a.wo_qty, a.batch_issue_qty, a.rate, a.amount, a.currency_id, a.exchange_rate, a.buyer_id, a.job_no, a.order_id, a.fin_dia, a.fin_gsm,a.grey_used,  a.booking_without_order, a.booking_dtls_id from pro_grey_batch_dtls a, inv_receive_mas_batchroll b where a.mst_id = b.id and a.mst_id=$data[0] and B.entry_form = 92 order by a.id" ;*/
    
        $total_row=count($sql);
        $current_row_array=array();
        $i=$total_row;
        foreach($sql as $row)
        {
            $previous_receive_qty=0;$tot_issue_qty=0;
            //$previous_receive_qty=$previous_receive_arr[$row[csf('booking_id')]][$row[csf('process_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('color_id')]]-$row[csf('batch_issue_qty')];

            $previous_receive_qty= $previous_receive_arr[$row[csf('booking_no')]][$row[csf('booking_dtls_id')]];
            $tot_issue_qty= $previousIssueArrNew[$row[csf('booking_no')]][$row[csf('booking_dtls_id')]];

            
            $tble_body .='<tr id="tr_'.$i.'" align="center" valign="middle"><td width="25" id="sl_'.$i.'">'. $i.'</td><td width="70" id="batchNo_'.$i.'"><input type="text" id="txtBatchNo_'.$i.'" name="txtBatchNo[]"  style=" width:60px" readonly class="text_boxes" value="'.$row[csf('outbound_batchname')].'"/></td><td width="100" id="inHouseBatchNo_'.$i.'" style="display:none"><input type="text" id="txtinHouseBatchNo_'.$i.'" name="txtinHouseBatchNo[]"  style=" width:80px" class="text_boxes"/></td><td style="word-break:break-all;" width="80" id="bodyPart_'.$i.'">'.$body_part[$row[csf('body_part_id')]].'</td><td style="word-break:break-all;" width="120" id="'.$i.'" align="left">'.$composition_arr[$row[csf('febric_description_id')]].'</td><td style="word-break:break-all;" width="50" id="dia_'.$i.'">'.$row[csf('width')].'</td><td style="word-break:break-all;" width="50" id="gsm_'.$i.'">'.$row[csf('fin_gsm')].'</td><td style="word-break:break-all;" width="70" id="color_'.$i.'">'.$color_library[$row[csf('color_id')]].'</td><td width="80" align="right" id="">'.create_drop_down( "cboProcess_".$i."", 80, $conversion_cost_head_array,"", 1, "-- Select Process --", "".$row[csf('process_id')]."", "",1,"","","","","","","cboProcess[]" ).'</td><td style="word-break:break-all;" width="60" id="woQty_'.$i.'">'.$row[csf('wo_qty')].'</td><td width="60" align="center" id=""><input type="text" id="totalreceiveQty_'.$i.'" name="totalreceiveQty[]"  style=" width:40px" class="text_boxes_numeric" readonly value="'.$previous_receive_qty.'"/></td><td width="60" align="center" id=""><input type="text" id="txtReceiveQty_'.$i.'" name="txtReceiveQty[]"  style=" width:40px"  disabled class="text_boxes_numeric" onKeyUp="calculate_amount('.$i.');" value="'.$row[csf('batch_issue_qty')].'" placeholder="'.$tot_issue_qty.'"/> <input type="hidden" id="txtReceiveQtyHidden_'.$i.'" name="txtReceiveQtyHidden[]" value="'.$row[csf('batch_issue_qty')].'"/></td><td width="60" align="center" id=""><input type="text" id="txtReturnQty_'.$i.'" name="txtReturnQty[]"  style=" width:40px" onKeyUp="calculate_total_retn('.$i.');" class="text_boxes_numeric" value="'.$row[csf('return_qty')].'" /></td><td width="60" align="center" id=""><input type="text" id="txtRate_'.$i.'" name="txtRate[]"  style=" width:40px" class="text_boxes_numeric" value="'.$row[csf('rate')].'" disabled/></td><td width="70" align="center" id=""><input type="text" id="txtAmount_'.$i.'" name="txtAmount[]"  style=" width:55px" class="text_boxes_numeric" value="'.$row[csf('amount')].'"  readonly/></td><td width="70" align="center" id=""><input type="text" id="txtgreyUsed_'.$i.'" name="txtgreyUsed[]"  style=" width:55px" readonly class="text_boxes_numeric" value="'.$row[csf('grey_used')].'" /></td><td style="word-break:break-all;" width="90" id="bookingNo_'.$i.'" align="left">'.$row[csf('booking_no')].'</td><td style="word-break:break-all;" width="60" id="buyer_'.$i.'">'.$buyer_arr[$row[csf('buyer_id')]].'</td><td style="word-break:break-all;" width="80" id="job_'.$i.'">'.$row[csf('job_no')].'</td><td style="word-break:break-all;" width="80" id="order_'.$i.'" align="left">'.$order_name_arr[$row[csf('order_id')]].'</td><td style="word-break:break-all;" id="currency_'.$i.'" align="left">'.create_drop_down( "currencyId_".$i."", 65, $currency,"", 1, "Select", "".$row[csf('currency_id')]."", "",1,"","","","","","","currencyId[]" ).'<input type="hidden" name="hiddnJobNo[]" id="hiddnJobNo_'.$i.'" value="'. $row[csf('job_no')].'"/><input type="hidden" name="progBookPiId[]" id="progBookPiId_'.$i.'" value="'.$row[csf('booking_id')].'"/><input type="hidden" name="orderId[]" id="orderId_'.$i.'" value="'.$row[csf('order_id')].'"/><input type="hidden" name="colorId[]" id="colorId_'.$i.'" value="'.$row[csf('color_id')].'"/><input type="hidden" name="dtlsId[]" id="dtlsId_'.$i.'" value="'.$row[csf('id')].'"/><input type="hidden" name="bodypartId[]" id="bodypartId_'.$i.'" value="'.$row[csf('body_part_id')].'"/><input type="hidden" name="buyerId[]" id="buyerId_'.$i.'" value="'.$row[csf('buyer_id')].'"/><input type="hidden" name="determinationId[]" id="determinationId_'.$i.'" value="'.$row[csf('febric_description_id')].'"/><input type="hidden" name="currencyId[]" id="currencyId_'.$i.'" value="'.$row[csf('currency_id')].'"/><input type="hidden" name="exchangeRate[]" id="exchangeRate_'.$i.'" value="'.$row[csf('exchange_rate')].'"/><input type="hidden" name="txtBookingNo[]" id="txtBookingNo_'.$i.'" value="'.$row[csf('booking_no')].'"/><input type="hidden" name="finDia[]" id="finDia_'.$i.'" value="'.$row[csf('width')].'"/> <input type="hidden" name="finGsm[]" id="finGsm_'.$i.'" value="'.$row[csf('fin_gsm')].'"/><input type="hidden" name="bookWithoutOrder[]" id="bookWithoutOrder_'.$i.'" value="'.$row[csf('booking_without_order')].'"/> <input type="hidden" name="bookingDtlsId[]" id="bookingDtlsId_'.$i.'" value="'.$row[csf('booking_dtls_id')].'"/></td></tr>';
        $i--;
    }
    echo $tble_body;
exit();
}


if($action=="receive_and_return_popup")
{
    echo load_html_head_contents("Issue Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);

?> 

<script>

    function js_set_value(id,order_type,popup_type)
    {
        $('#hidden_system_id').val(id);
        $('#hidden_order_type').val(order_type);
		$('#hidden_popup_type').val(popup_type);
        parent.emailwindow.hide();
    }

</script>
 
</head>

<body>
<div align="center" style="width:760px;">
    <form name="searchwofrm"  id="searchwofrm">
        <fieldset style="width:760px; margin-left:2px">
            <table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table">
                <thead>
                <!--id="search_by_td_up"-->
                    <th><? if($popupType=='returnSys'){echo "Return Date Range";} else{echo "Issue Date Range";}?></th>
                    <th>Search By</th>
                    <th  width="180"><? if($popupType=='returnSys'){echo "Please Enter Return No";} else{echo "Please Enter Issue No";}?></th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="hidden_system_id" id="hidden_system_id">  
                        <input type="hidden" name="hidden_order_type" id="hidden_order_type"> 
                        <input type="hidden" name="hidden_popup_type" id="hidden_popup_type"> 
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">
                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                    </td>
                    <td align="center"> 
                        <?
						  	if($popupType=='returnSys'){echo $issueType="Return No";} else{echo $issueType="Issue No";}
                            $search_by_arr=array(1=>"$issueType");
                            $dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";                         
                            echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );
                        ?>
                    </td>     
                    <td align="center" id="search_by_td">               
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />   
                    </td> 

                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $cbo_company_id; ?>'+'_<? echo $popupType; ?>', 'create_challan_search_list_view', 'search_div', 'fabric_service_receive_return_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                     </td>
                </tr>
                <tr>
                    <td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
           </table>
           <div style="width:100%; margin-top:5px;" id="search_div" align="center"></div>
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
    $popupType =$data[5];


    if($start_date!="" && $end_date!="")
    {
        if($db_type==0)
        {
            $date_cond="and a.receive_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
        }
        else
        {
            $date_cond="and a.receive_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
        }
    }
    else
    {
        $date_cond="";
    }
    
    $search_field_cond="";
    if(trim($data[0])!="")
    {
        if($search_by==1) $search_field_cond="and a.recv_number like '$search_string'";
    }
    
    if($db_type==0) 
    {
        $year_field="YEAR(a.insert_date) as year,";
    }
    else if($db_type==2) 
    {
        $year_field="to_char(a.insert_date,'YYYY') as year,";
    }
    else $year_field="";//defined Later
    if($popupType=='returnSys')
	{
	 $sql = "select a.id, a.challan_no, $year_field a.recv_number_prefix_num, a.recv_number, a.dyeing_source, a.dyeing_company, a.receive_date, b.booking_without_order 
    from inv_receive_mas_batchroll a, pro_grey_batch_dtls b
    where a.id=b.mst_id and a.entry_form=206 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $search_field_cond $date_cond
    group by a.id, a.challan_no, a.insert_date, a.recv_number_prefix_num, a.recv_number, a.dyeing_source, a.dyeing_company, a.receive_date, b.booking_without_order
    order by id"; 
	}
	else
	{
    $sql = "select a.id, a.challan_no, $year_field a.recv_number_prefix_num, a.recv_number, a.dyeing_source, a.dyeing_company, a.receive_date, b.booking_without_order 
    from inv_receive_mas_batchroll a, pro_grey_batch_dtls b
    where a.id=b.mst_id and a.entry_form=92 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $search_field_cond $date_cond
    group by a.id, a.challan_no, a.insert_date, a.recv_number_prefix_num, a.recv_number, a.dyeing_source, a.dyeing_company, a.receive_date, b.booking_without_order
    order by id"; 
	}
    
    $result = sql_select($sql);

    $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
    $batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="70">Issue No</th>
            <th width="60">Year</th>
            <th width="140">Service Source</th>
            <th width="160">Service Company</th>
            <th width="100">Challan No</th>
            <th>Receive date</th>
        </thead>
    </table>
    <div style="width:720px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="">    
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";   
                 
                $dye_comp="&nbsp;";
                if($row[csf('dyeing_source')]==1)
                    $dye_comp=$company_arr[$row[csf('dyeing_company')]]; 
                else
                    $dye_comp=$supllier_arr[$row[csf('dyeing_company')]];
            ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]; ?>','<? echo $row[csf('booking_without_order')]; ?>','<? echo $popupType; ?>');"> 
                    <td width="40"><? echo $i; ?></td>
                    <td width="70"><p>&nbsp;<? echo $row[csf('recv_number_prefix_num')]; ?></p></td>
                    <td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="140"><p><? echo $knitting_source[$row[csf('dyeing_source')]]; ?>&nbsp;</p></td>
                    <td width="160"><p><? echo $dye_comp; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
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


if($action=="populate_data_from_data")
{
	$data=explode("_",$data);
	if($data[1]=="returnSys")
	{
    	$sql = "select id, company_id, recv_number, dyeing_source, dyeing_company, receive_date,challan_no,service_recv_id from inv_receive_mas_batchroll where id=$data[0] and entry_form=206";

         $previous_recv_num_arr=return_library_array( "select id, recv_number from inv_receive_mas_batchroll where entry_form=92",'id','recv_number');
         //print_r($previous_recv_num_arr);
	}
	else
	{
		$sql = "select id, company_id, recv_number, dyeing_source, dyeing_company, receive_date,challan_no from inv_receive_mas_batchroll where id=$data[0] and entry_form=92";
	}
    $res = sql_select($sql);    
    foreach($res as $row)
    {       
       
        if($data[1]=="returnSys")
        {
            echo "$('#txt_return_sys_no').val('".$row[csf("recv_number")]."');\n";
            echo "$('#txt_receive_no').val('".$previous_recv_num_arr[$row[csf("service_recv_id")]]."');\n";  
            //.val(".$row[csf("id")].");\n";  

        }
        else
        {
           echo "$('#txt_receive_no').val('".$row[csf("recv_number")]."');\n";    
        }
        echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
        echo "$('#cbo_company_id').attr('disabled','true')".";\n";
        
        echo "$('#txt_receive_date').val('".change_date_format($row[csf("receive_date")])."');\n";
        echo "$('#cbo_service_source').val(".$row[csf("dyeing_source")].");\n";
        echo "load_drop_down( 'requires/fabric_service_receive_return_controller', ".$row[csf("dyeing_source")]."+'**'+".$row[csf("company_id")].", 'load_drop_down_knitting_com', 'dyeing_company_td' );\n";
        echo "$('#cbo_service_company').val(".$row[csf("dyeing_company")].");\n";
        echo "$('#txt_receive_challan').val('".$row[csf("challan_no")]."');\n";
		if($data[1]=="returnSys")
		{
			echo "$('#txt_receive_no_id').val(".$row[csf("service_recv_id")].");\n";
		}
		else
		{
			echo "$('#txt_receive_no_id').val(".$row[csf("id")].");\n";
		}
        echo "$('#update_id').val(".$row[csf("id")].");\n";
    }
    exit(); 
}

if ($action=="save_update_delete")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
    //echo $operation;die;
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
        
       // $new_mrr_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'FSR', date("Y",time()), 5, "select recv_number_prefix,recv_number_prefix_num from inv_receive_mas_batchroll where company_id=$cbo_company_id and entry_form=92 and $year_cond=".date('Y',time())." order by id desc","recv_number_prefix","recv_number_prefix_num"));
      $id= return_next_id_by_sequence("INV_RCV_MAS_BATC_PK_SEQ", "inv_receive_mas_batchroll", $con);
	   $new_mrr_number = explode("*", return_next_id_by_sequence("INV_RCV_MAS_BATC_PK_SEQ", "inv_receive_mas_batchroll",$con,1,$cbo_company_id,'FSRR',206,date("Y",time()) ));
	    //$id=return_next_id( "id", "inv_receive_mas_batchroll", 1 ) ;
                 
        $field_array="id,recv_number_prefix,recv_number_prefix_num,recv_number,entry_form, company_id, dyeing_source,batch_id, dyeing_company, receive_date,challan_no,service_recv_id, inserted_by, insert_date";
        if(str_replace("'","",$txt_receive_challan)=="") $txt_receive_challan=$new_mrr_number[0];
        $data_array="(".$id.",'".$new_mrr_number[1]."',".$new_mrr_number[2].",'".$new_mrr_number[0]."',206,".$cbo_company_id.",".$cbo_service_source.","."0".",".$cbo_service_company.",".$txt_receive_date.",'".str_replace("'","",$txt_receive_challan)."',".$txt_receive_no_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
        
      //  $dtls_id=return_next_id("id", "pro_grey_batch_dtls", 1);
        $field_array_dtls="id, mst_id, outbound_batchname, booking_no, booking_id, booking_without_order, body_part_id, febric_description_id, width, color_id, process_id, wo_qty, batch_issue_qty, rate, amount, currency_id, exchange_rate, buyer_id, job_no, order_id, fin_dia, fin_gsm,grey_used,booking_dtls_id,return_qty,recv_dtls_id,  inserted_by, insert_date";     
        $all_detailsId='';
        for($j=1;$j<=$tot_row;$j++)
        {   
            $dtls_id = return_next_id_by_sequence("PRO_GREY_BATCH_DTLS_PK_SEQ", "pro_grey_batch_dtls", $con);
			$process_id="cboProcess_".$j;
            $deterId="determinationId_".$j;
            $buyerId="buyerId_".$j;
            $orderId="orderId_".$j;
            $batchName="txtBatchNo_".$j;
            $jobNo="jobNo_".$j;
            $colorId="colorId_".$j;
            //echo $$txtInhouseBatchNo="txtInhouseBatchNo".$j;die;

            $dia="dia_".$j;
            $txtRate="txtRate_".$j;
            $txtAmount="txtAmount_".$j;
			
            $bodyparyId="bodypartId_".$j;
            $receiveQty="txtReceiveQty_".$j;
            $txtAmount="txtAmount_".$j;
            $txtRate="txtRate_".$j;
            $currencyId="currencyId_".$j;
           // echo $$currencyId;die;
            $exchangeRate="exchangeRate_".$j;
            $bookingId="bookingId_".$j;
            $bookingNo="bookingNo_".$j;
            $workorderNo="workorderNo_".$j;
            $finDia="finDia_".$j;
            $finGsm="finGsm_".$j;
			$greyUsed="greyUsed_".$j;
            $bookWithoutOrder="bookWithoutOrder_".$j;
            $bookingDtlsId="bookingDtlsId_".$j;
			$txtReturnQty="txtReturnQty_".$j;
			$recvDtlsId="dtlsId_".$j;
			

            $trId="tr_".$j;
        
            if($$receiveQty!="")
            {
                if($data_array_dtls!="") $data_array_dtls.=",";
                $data_array_dtls.="(".$dtls_id.",".$id.",'".$$batchName."','".$$bookingNo."','".$$bookingId."','".$$bookWithoutOrder."','".$$bodyparyId."','".$$deterId."','".$$dia."','".$$colorId."','".$$process_id."','".$$workorderNo."','".$$receiveQty."','".$$txtRate."','".$$txtAmount."','".$$currencyId."','".$$exchangeRate."','".$$buyerId."','".$$jobNo."','".$$orderId."','".$$finDia."','".$$finGsm."','".$$greyUsed."','".$$bookingDtlsId."','".$$txtReturnQty."',".$$recvDtlsId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
                $all_detailsId.=$$trId."__".$dtls_id.",";
               // $dtls_id = $dtls_id+1;
            }
            
        }
        
        //echo "10**insert into pro_grey_batch_dtls($field_array_dtls) values".$data_array_dtls;die;
        
        $rID=sql_insert("inv_receive_mas_batchroll",$field_array,$data_array,0);
        $rID2=sql_insert("pro_grey_batch_dtls",$field_array_dtls,$data_array_dtls,0);
        //echo "10**$rID && $rID2";die;
        if($db_type==0)
        {
            if($rID && $rID2)
            {
                mysql_query("COMMIT");  
                echo "0**".$id."**".$new_mrr_number[0]."**".substr($all_detailsId,0,-1);
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
                echo "0**".$id."**".$new_mrr_number[0]."**".substr($all_detailsId,0,-1);
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
        
        $field_array="dyeing_source*dyeing_company*receive_date*challan_no*service_recv_id*updated_by*update_date"; 
        $data_array=$cbo_service_source."*".$cbo_service_company."*".$txt_receive_date."*".$txt_receive_challan."*".$txt_receive_no_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
        
      //  $dtls_id=return_next_id("id", "pro_grey_batch_dtls", 1);
	   
        $field_array_dtls="id, mst_id, outbound_batchname, booking_no, booking_id, booking_without_order, body_part_id, febric_description_id, width, color_id, process_id,  batch_issue_qty, rate, amount, currency_id, exchange_rate, buyer_id, job_no, order_id, fin_dia, fin_gsm, grey_used,  inserted_by, insert_date";    
        $field_array_updatedtls="outbound_batchname*batch_issue_qty*rate*amount*grey_used*return_qty*recv_dtls_id*updated_by*update_date";        
        
        $all_detailsId='';
        for($j=1;$j<=$tot_row;$j++)
        {   
            $dtls_id = return_next_id_by_sequence("PRO_GREY_BATCH_DTLS_PK_SEQ", "pro_grey_batch_dtls", $con);
		    $process_id="cboProcess_".$j;
            $deterId="determinationId_".$j;
            $buyerId="buyerId_".$j;
            $orderId="orderId_".$j;
            $batchName="txtBatchNo_".$j;
            $jobNo="jobNo_".$j;
            $colorId="colorId_".$j;
            
            $dia="dia_".$j;
            $txtRate="txtRate_".$j;
            $txtAmount="txtAmount_".$j;
            $bodyparyId="bodypartId_".$j;
            $receiveQty="txtReceiveQty_".$j;
            $txtAmount="txtAmount_".$j;
            $txtRate="txtRate_".$j;
            $currencyId="currencyId_".$j;
            $exchangeRate="exchangeRate_".$j;
            $bookingId="bookingId_".$j;
            $bookingNo="bookingNo_".$j;
            $update_dtls="dtlsId_".$j;
            $finDia="finDia_".$j;
            $finGsm="finGsm_".$j;
			$greyUsed="greyUsed_".$j;
            $bookWithoutOrder="bookWithoutOrder_".$j;
			$greyUsed="greyUsed_".$j;
			$txtReturnQty="txtReturnQty_".$j;
			$recvDtlsId="dtlsId_".$j;
            //echo $$receiveQty."**";
            $trId="tr_".$j;
            if($$receiveQty!="")
            {
                if($$update_dtls!="")
                {
                    $dtlsId_arr[]=str_replace("'","",$$update_dtls);
                    $data_array_update_dtls[str_replace("'","",$$update_dtls)]=explode("*",("'".$$batchName."'*'".$$receiveQty."'*'".$$txtRate."'*'".$$txtAmount."'*'".$$greyUsed."'*'".$$txtReturnQty."'*".$$recvDtlsId."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
                    $all_detailsId.=$$trId."__".str_replace("'","",$$update_dtls).",";
                }
                else
                {
                    if($data_array_dtls!="") $data_array_dtls.=",";
                    $data_array_dtls.="(".$dtls_id.",".$update_id.",'".$$batchName."','".$$bookingNo."','".$$bookingId."','".$$bookWithoutOrder."','".$$bodyparyId."','".$$deterId."','".$$dia."','".$$colorId."','".$$process_id."','".$$receiveQty."','".$$txtRate."','".$$txtAmount."','".$$currencyId."','".$$exchangeRate."','".$$buyerId."','".$$jobNo."','".$$orderId."','".$$finDia."','".$$finGsm."','".$$greyUsed."','".$$txtReturnQty."',".$$recvDtlsId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
                    $all_detailsId.=$$trId."__".$dtls_id.",";
                   // $dtls_id = $dtls_id+1;
                }
            }
        }
        
    
        
        $rID=sql_update("inv_receive_mas_batchroll",$field_array,$data_array,"id",$update_id,0);
        $rID2=true; $rID3=true;
        if($data_array_dtls!="")
        {
            $rID2=sql_insert("pro_grey_batch_dtls",$field_array_dtls,$data_array_dtls,0);
        }
        
        if(count($data_array_update_dtls)>0)
        {
            //echo bulk_update_sql_statement( "pro_grey_batch_dtls", "id", $field_array_updatedtls, $data_array_update_dtls, $dtlsId_arr );die;
            $rID3=execute_query(bulk_update_sql_statement( "pro_grey_batch_dtls", "id", $field_array_updatedtls, $data_array_update_dtls, $dtlsId_arr ));
        }

        if($db_type==0)
        {
            if($rID && $rID2 && $rID3)
            {
                mysql_query("COMMIT");  
                echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_receive_no)."**".substr($all_detailsId,0,-1);
            }
            else
            {
                mysql_query("ROLLBACK"); 
                echo "6**".str_replace("'", '', $update_id)."**";
            }
        }
        else if($db_type==2 || $db_type==1 )
        {
            if($rID && $rID2 && $rID3)
            {
                oci_commit($con);  
                echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_receive_no)."**".substr($all_detailsId,0,-1);
            }
            else
            {
                oci_rollback($con);
                echo "6**".str_replace("'", '', $update_id)."**";
            }
        }
        disconnect($con);
        die;
    }
}

?>


