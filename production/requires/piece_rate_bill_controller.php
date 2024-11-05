<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');
extract($_REQUEST);

if ($action=="load_drop_down_location")
{
    echo create_drop_down( "cbo_location", 160, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "","","","","","",3 );          
    exit();
}


if ($action=="load_drop_down_working_company")
{
    $data=explode("**", $data);
    if($data[0]==1)
    {
        echo create_drop_down("cbo_working_company", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  order by comp.company_name","id,company_name", 1,"-- Select Company --", $selected,"","","");
    }else{
        $sql="SELECT a.id,a.supplier_name FROM lib_supplier a,lib_supplier_party_type b,lib_supplier_tag_company c WHERE a.id=b.supplier_id and a.id=c.supplier_id and b.party_type  in(22,36) and c.tag_company =$data[1]  and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name";
       // echo $sql;
        echo create_drop_down( "cbo_working_company", 160, $sql,"id,supplier_name", 1, "-- Select Company --", $selected, "","","","","","",3 ); 
    }
             
    exit();
}
if ($action=='load_variable_settings') 
{
	//Piece Rate Work Order & Bill
	$qty_rate_source = sql_select("SELECT id,company_name,variable_list,qty_source_sample,process_wise_rate_source,inserted_by,insert_date,status_active  FROM variable_settings_production where company_name=$data and variable_list=82 and status_active=1 and is_deleted= 0 order by id");
	$qty_source = $qty_rate_source[0]['QTY_SOURCE_SAMPLE'];
    $rate_source = $qty_rate_source[0]['PROCESS_WISE_RATE_SOURCE'];
    if (!$qty_source) {
        $rate_source = $qty_source  = 1;
    }
	
	if ($qty_source == $rate_source && $rate_source) 
	{
		echo "$('#qty_source').val('$qty_source');\n";
		echo "$('#rate_source').val('$rate_source');\n";

		if($qty_source == 2 )  //PO WISE
		{ 
            echo "$('#txtavgrate_1').attr('onDblClick','openmypage_avg_rate(1)');\n"; 
			echo "$('#txtavgrate_1').attr('placeholder','Double click to search');\n";
            echo "$('#txtavgrate_1').attr('readonly','readonly');\n"; 
		}
        else //COLOR AND SIZE WISE 
        { 
            echo "$('#txtavgrate_1').removeAttr('onDblClick');\n"; 
            echo "$('#txtavgrate_1').removeAttr('placeholder');\n";
            echo "$('#txtavgrate_1').removeAttr('readonly');\n";
			
        }
		
	} else //BY DEFAULT COLOR AND SIZE WISE 
	{
		echo "$('#txtavgrate_1').removeAttr('onDblClick');\n"; 
        echo "$('#txtavgrate_1').removeAttr('placeholder');\n";
        echo "$('#txtavgrate_1').removeAttr('readonly');\n";

	}

    echo "changeVar($qty_source);\n"; //Func call For variable wise th,td entry
	
}

function toRound($number)
{
    
    
    return number_format($number, 2,'.','');
}



//$service_provider_arr=return_library_array("SELECT a.id,a.supplier_name FROM lib_supplier a,lib_supplier_party_type b,lib_supplier_tag_company c WHERE a.id=b.supplier_id and a.id=c.supplier_id and b.party_type =36 and c.tag_company=$cbo_company_id order by supplier_name",'id','supplier_name');

$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0 order by supplier_name",'id','supplier_name');
$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 order by buyer_name",'id','buyer_name');

$subcon_buyer_arr=return_library_array( "select id,cust_buyer from subcon_ord_dtls where status_active=1 and is_deleted=0 order by cust_buyer",'id','cust_buyer');

//$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
//$size_arr = return_library_array("select id, size_name from lib_size","id","size_name");
$company_arr = return_library_array("select id, company_name from lib_company order by company_name","id","company_name");





if($action=="load_details_entry")
{ 

    list($job_str,$company_id,$serial,$rate_bill_var)=explode("**",$data); 
    $mstArr=array(); $dtlsArr=array(); $mstArr2=array(); $dtlsArr2=array(); $dtlsArr3=array();
    foreach(explode("__",$job_str) as $job_item_po){
        list($mst_id,$dtls_id,$type)=explode("*",$job_item_po);
        if($type==1)
        {
            $mstArr[$mst_id]=$mst_id;
            $dtlsArr[$dtls_id]=$dtls_id;
        }
        else{
            $mstArr2[$mst_id]=$mst_id;
            $dtlsArr2[$dtls_id]=$dtls_id;
        }
        $dtlsArr3[$dtls_id]=$dtls_id;
    }
    // echo $rate_bill_var; die;
    if(count($mstArr)>0 && count($mstArr2)>0){
        $sql="SELECT a.id,a.sys_number,a.sys_number_prefix_num , a.company_id, a.wo_date,b.buyer_id,b.avg_rate,b.amount,b.wo_qty,b.po_qty,b.id as dtls_id,c.po_number,b.po_id,b.job_id,d.job_no,b.item_id,b.style_ref,b.uom,b.color_type,b.client_id, 1 as type,null as prod_reso_allo,null as line_id,d.product_dept,null as rate_history from piece_rate_wo_urmi_mst a,piece_rate_wo_urmi_dtls b ,wo_po_break_down c,wo_po_details_master d where a.id=b.mst_id and b.po_id=c.id and  b.job_id=d.id and b.status_active=1 and a.status_active=1 and a.id in(".implode(',',$mstArr).") and b.id in(".implode(',',$dtlsArr).")

        union all
    
        SELECT a.id,a.sys_number,a.sys_number_prefix_num , a.company_id, a.wo_date,b.buyer_id,b.avg_rate,b.amount,b.wo_qty,null as po_qty,b.id as dtls_id,c.po_number,b.order_id,b.job_id,d.job_no,b.item_id,b.style_ref,b.uom,b.color_type, null as client_id, 2 as type,a.prod_reso_allo,a.line_id,d.product_dept,b.rate_history
        from piece_rate_wo_mst a,piece_rate_wo_dtls b ,wo_po_break_down c,wo_po_details_master d 
        where a.id=b.mst_id and b.order_id=c.id and  b.job_id=d.id and b.status_active=1 and a.status_active=1 and a.id in(".implode(',',$mstArr2).") and b.id in(".implode(',',$dtlsArr2).")";
    }

    else if(count($mstArr)>0){
        $sql="SELECT a.id,a.sys_number,a.sys_number_prefix_num , a.company_id, a.wo_date,b.buyer_id,b.avg_rate,b.amount,b.wo_qty,b.po_qty,b.id as dtls_id,c.po_number,b.po_id,b.job_id,d.job_no,b.item_id,b.style_ref,b.uom,b.color_type,b.client_id, 1 as type,d.product_dept from piece_rate_wo_urmi_mst a,piece_rate_wo_urmi_dtls b ,wo_po_break_down c,wo_po_details_master d where a.id=b.mst_id and b.po_id=c.id and  b.job_id=d.id and b.status_active=1 and a.status_active=1 and a.id in(".implode(',',$mstArr).") and b.id in(".implode(',',$dtlsArr).")";
    }

    else if(count($mstArr2)>0){
        $sql="SELECT a.id,a.sys_number,a.sys_number_prefix_num , a.company_id, a.wo_date,b.buyer_id,b.avg_rate,b.amount,b.wo_qty,null as po_qty,b.id as dtls_id,c.po_number,b.order_id,b.job_id,d.job_no,b.item_id,b.style_ref,b.uom,b.color_type, null as client_id, 2 as type,a.prod_reso_allo,a.line_id,d.product_dept,rate_history
        from piece_rate_wo_mst a,piece_rate_wo_dtls b ,wo_po_break_down c,wo_po_details_master d 
        where a.id=b.mst_id and b.order_id=c.id and  b.job_id=d.id and b.status_active=1 and a.status_active=1 and a.id in(".implode(',',$mstArr2).") and b.id in(".implode(',',$dtlsArr2).")";
    }

    // echo $sql;die;
    $sql_result = sql_select($sql);

            // SEWING LINE 
    if ($rate_bill_var ==2 ) //PO WISE
    {        
        $prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
        $lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1"); 
        foreach($lineDataArr as $lRow)
        {
            $lineArr[$lRow['ID']]=$lRow['LINE_NAME'];  
        }


        foreach ($sql_result as $v) 
        {
            if($v['PROD_RESO_ALLO']==1)
            {
                $line_name = ""; 
                $sewing_line_id_arr=explode(",",$prod_reso_arr[$v['LINE_ID']]);
                foreach ($sewing_line_id_arr as $r) 
                {					 
                    $line_name .= ($line_name=="") ? $lineArr[$r] : ",". $lineArr[$r];
                } 
            } 
            else
            { 
                $line_name=$lineArr[$v['LINE_ID']];
            }
            $line_name_arr[$v['LINE_ID']] = $line_name;
        }
    }   

    $sql_check="SELECT sum(bill_qty) as bill_qty ,wo_dtls_id from piece_rate_bill_dtls where status_active=1 and wo_dtls_id  in(".implode(',',$dtlsArr3).") group by wo_dtls_id";
    $bill_result=sql_select($sql_check);

    $prev=array();
    foreach ($bill_result as $row) {
       $prev[$row[csf('wo_dtls_id')]]=$row[csf('bill_qty')];
    }

    $client_arr=return_library_array("select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id  group by a.id,a.buyer_name  order by buyer_name",'id','buyer_name');


    $i=($serial+1);
    // echo "<pre>"; print_r($sql_result); die;
    foreach($sql_result as $row)
    {  
        // echo $row['RATE_HISTORY']; die;
        $rate_history = $row['RATE_HISTORY']; 
        $avg_rate =toRound( $row[csf('avg_rate')] );
		$attrCond = "value='$avg_rate' onkeyup='calculate()'";
		if ($rate_bill_var == 2) 
		{
            $row_id = $i -1;
			$product_dept = $row['PRODUCT_DEPT'];
			$item = $row['ITEM_ID']; 
			$attrCond = " value='$avg_rate' onDblClick='openmypage_avg_rate($row_id,$product_dept,$item);' readonly";
		}
        ?>
        <tr>                                    
            <td>
                 <input type="hidden" id="detailsUpdateId_<? echo $i; ?>" name="detailsUpdateId_<? echo $i; ?>" value="" />
                 <input type="text" name="txtwono_<? echo $i; ?>" id="txtwono_<? echo $i; ?>" class="text_boxes" style="width:100px;" value="<?php echo $row[csf('sys_number')]; ?>" onDblClick="openmypage_wo_no(<? echo $i;?>);" />
                 <input type="hidden" name="txtwodtlsid_<? echo $i; ?>" id="txtwodtlsid_<? echo $i; ?>" value="<?php echo $row[csf('dtls_id')];?>">
                <input type="hidden" name="txttype_<? echo $i; ?>" id="txttype_<? echo $i; ?>" value="<?php echo $row['TYPE'];?>">
                <input type="hidden" name="sewingLineId_<? echo $i; ?>" id="sewingLineId_<? echo $i; ?>" value="<?php echo $row['LINE_ID'];?>" />
                <input type="hidden" name="prodResoAllo_<? echo $i; ?>" id="prodResoAllo_<? echo $i; ?>" value="<?php echo $row['PROD_RESO_ALLO'];?>" />
            </td>
           
            <td>
                 <input type="text" name="txtbuyer_<? echo $i; ?>" id="txtbuyer_<? echo $i; ?>" class="text_boxes" style="width:80px;" readonly value="<?php echo $buyer_arr[$row[csf('buyer_id')]];?>" />
                 <input type="hidden" name="txtbuyerid_<? echo $i; ?>" id="txtbuyerid_<? echo $i; ?>" value="<?php echo $row[csf('buyer_id')];?>" />
            </td>
             <td>
                 <input type="text" name="client_<? echo $i; ?>" id="client_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<?php echo $client_arr[$row[csf('client_id')]]; ?>" readonly />
                 <input type="hidden" name="clientid_<? echo $i; ?>" value="<?php echo $row[csf('client_id')]; ?>" id="clientid_<? echo $i; ?>" />
            </td>
            <? 
                if ($rate_bill_var ==2) 
                { 
                    ?>
                        <td>
                            <input type="text" name="sewing_line_<? echo $i; ?>" id="sewing_line_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<?php echo $line_name_arr[$row['LINE_ID']];?>" readonly /> 
                        </td>

                    <?
                }     
            ?>
            <td>
                 <input type="text" name="txtstyle_<? echo $i; ?>" id="txtstyle_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<?php echo $row[csf('style_ref')];?>" readonly />
            </td>
            <td>
                 <input type="text" name="txtjobno_<? echo $i; ?>" id="txtjobno_<? echo $i; ?>" class="text_boxes" style="width:100px;" value="<?php echo $row[csf('job_no')];?>" readonly />
                 <input type="hidden" name="txtjobid_<? echo $i; ?>" id="txtjobid_<? echo $i; ?>">
            </td>
            <td>
                 <input type="text" name="txtitem_<? echo $i; ?>" id="txtitem_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<? echo $garments_item[$row[csf("item_id")]];?>"  readonly />
                 <input type="hidden" name="txtitemid_<? echo $i; ?>" id="txtitemid_<? echo $i; ?>" value="<? echo $row[csf("item_id")];?>" />
            </td>
             <td>
                <input type="text" name="po_<? echo $i; ?>" id="po_<? echo $i; ?>" class="text_boxes_numeric" value="<?php echo $row[csf('po_number')];?>" style="width:80px;" readonly>
                <input type="hidden" name="poid_<? echo $i; ?>" id="poid_<? echo $i; ?>" value="<?php echo $row[csf('po_id')];?>"  />
            </td>
            <td>
                <input type="text" name="poqty_<? echo $i; ?>" id="poqty_<? echo $i; ?>" class="text_boxes_numeric" value="<?php echo toRound($row[csf('po_qty')]);?>" style="width:80px;" readonly>
            </td>
            <td>
                 
                 <? 
                echo create_drop_down( "colortype_".$i, 114, $color_type,"",1, "--Select--", $row[csf('color_type')],"",1,"" ); 
                ?>                                    
            </td>
           
            <td>
                <input type="text" name="txtwoqty_<? echo $i; ?>" id="txtwoqty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px;" value="<?php echo toRound($row[csf('wo_qty')]);?>"  readonly />
                
            </td>
             <td>
                <input type="text" name="txtbillqty_<? echo $i; ?>" id="txtbillqty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px;" value="<?php echo toRound(($row[csf('wo_qty')]-$prev[$row[csf('dtls_id')]]));?>" onkeyup="calculate()"  />
                <input type="hidden" name="txtbillremain_<? echo $i; ?>" id="txtbillremain_<? echo $i; ?>" value="<?php echo toRound($row[csf('wo_qty')]-$prev[$row[csf('dtls_id')]]);?>">
                
            </td>
            <td>
                <? 
                echo create_drop_down( "cbodtlsuom_".$i, 80, $unit_of_measurement,"",1, "--Select--", $row[csf('uom')],"",1,"1,2" ); 
                ?>                                    
            </td>
            <td>
                 <input type="text" name="txtavgrate_<? echo $i; ?>" id="txtavgrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px;" <?= $attrCond ?>  />
                 <input type="hidden" name="txtRate_<?= $i; ?>" id="txtRate_<?= $i; ?>" value="<?= $rate_history ?>" />
            </td>
           
            <td>
                 <input type="text" name="txtdtlamount_<? echo $i; ?>" id="txtdtlamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px;"  />
            </td>
            <td>
                <input type="text" name="txtremarks_<? echo $i; ?>" id="txtremarks_<? echo $i; ?>" class="text_boxes" style="width:80px;" />
            </td>
             <td align="center">
                <input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:27px" class="formbuttonplasminus" value="+" onClick="fn_addRow(<? echo $i; ?>)"/>
                <input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:27px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);"/>
            </td>
        </tr>
                                
     

        <?
        $i++;
    }
//---------------------------
    exit();
}


if ($action=="wo_no_popup")
{
    echo load_html_head_contents("Wo No Info", "../../", 1, 1,'','','');
    extract($_REQUEST);
    ?>
    <script>
        

    function toggle( x, origColor ) {
        
        var newColor = 'yellow';
        if ( x.style ) {
            x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
        }
    }

    
    var selected_id = new Array;
    function check_all_data()
    {
        var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
        tbl_row_count = tbl_row_count;

        for( var i = 1; i <= tbl_row_count; i++ )
        {
            $('#tr_'+i).trigger('click'); 
            //alert(i);
        }
    }
    
    function js_set_value(str,id)
    {       
         toggle( document.getElementById( 'tr_' + id ), '#FFFFCC' );

         if(document.getElementById( 'tr_' + id ).style.display!="none")
         {
            if(selected_id.includes(str) )
            {
                selected_id.splice( selected_id.indexOf(str), 1 );
            }
            else
            {
                selected_id.push(str);
            } 
         }
        //  console.log(selected_id.join("__"));
        $('#txt_selected_id').val( selected_id.join("__") );   
    }        
        
    function close_popup()
    {
         parent.emailwindow.hide();
    
    }
        
    function fnc_close_popup_reponse()
    {
        if(http.readyState == 4) 
        {
            var reponse=http.responseText;
            if(reponse==0){parent.emailwindow.hide();}
            else{alert(reponse+" Item Found in this Job");}
        }
    }
        
        
    </script>
    </head>

    <body>
    <div align="center" style="width:1040px;">
        <form name="searchsystemidfrm"  id="searchsystemidfrm">
            <fieldset style="width:1030px;">
            <legend>Enter search words</legend>
                <table cellpadding="0" cellspacing="0" width="1000" border="1" rules="all" class="rpt_table">
                    <thead>
                        <th>Year</th>
                        <th>Buyer Name</th>
                        <th>Style</th>
                        <th>Wo No</th>
                        <th>Job</th>
                        <th>Gmts Item</th>
                        <th>Po</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                            <input type="hidden" name="txt_company_id" id="txt_company_id" value="<? echo $cbo_company_id; ?>">
                            <input type="hidden" name="cbo_working_company" id="cbo_working_company" value="<? echo $cbo_working_company; ?>">
                            <input type="hidden" name="rate_bill_var" id="rate_bill_var" value="<? echo $rate_bill_var; ?>">
                            
                        </th>
                    </thead>
                    <tr>
                         <td align="center">
                            <?
                                echo create_drop_down( "cbo_year", 80, $year,"", 1, "-- Select --", date("Y",time()+2100), "",0 );
                            ?>
                        </td>
                        <td align="center">
                            <?
                                
                                echo create_drop_down( "cbo_buyer_id", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$cbo_company_id $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0 );
                                
                                
                            ?>
                        </td>
                       
                        <td align="center">
                            <input type="text" style="width:100px;" class="text_boxes"  name="txt_wo_no" id="txt_wo_no" />
                        </td>
                         <td align="center">
                            <input type="text" style="width:100px;" class="text_boxes"  name="txt_style_no" id="txt_style_no" />
                        </td>
                         <td align="center">
                            <input type="text" style="width:100px;" class="text_boxes"  name="txt_job_no" id="txt_job_no" />
                        </td>
                        <td>
                            <?php  echo create_drop_down( "gmts_item_id", 130, $garments_item,"", 1, "-- Select --", 0, "",0 ); ?>
                        </td>
                        <td align="center">
                            <input type="text" style="width:100px;" class="text_boxes"  name="txt_buyer_order" id="txt_buyer_order" />
                        </td>
                        <td align="center">
                            
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('txt_style_no').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('gmts_item_id').value+'_'+document.getElementById('txt_wo_no').value+'_'+document.getElementById('txt_buyer_order').value+'_'+document.getElementById('cbo_working_company').value+'_'+document.getElementById('rate_bill_var').value, 'create_wo_no_list_view', 'search_div', 'piece_rate_bill_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                            
                        </td>
                    </tr>
                </table>
                <table width="100%" style="margin-top:5px;">
                    <tr>
                        <td colspan="5">
                            <div style="width:100%; margin-top:10px; margin-left:3px;" id="search_div" align="left"></div>
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
}

if ($action=="avg_rate_popup")
{
	echo load_html_head_contents("Avg Rate", "../../", 1, 1,'','','');
	extract($_REQUEST);
	$process_sql = "SELECT id,operation_name from lib_sewing_operation_entry where gmt_item_id=$item and product_dept=$product_dept and status_active=1 and is_deleted=0";
	// echo $process_sql; die;
	$data_arr = sql_select($process_sql);
    $rate_column = 'txtRate_'.$row_id;
	$rate_data = str_replace("'","",$$rate_column) ;
	$prv_rate_arr = explode('@@',$rate_data);
	$prve_rate_array = array(); 
	foreach ($prv_rate_arr as $old_rate) 
	{
		$pair = explode ('#', $old_rate);
		$prve_rate_array[$pair[0]] = $pair [1]; 
	}
	// echo $process_sql ; die;
	// echo "<pre>"; print_r($prve_rate_array); die;
	$width = 260; 
    ?>
    <script>
    	function js_set_value(row_id)
    	{
    		
    		let process_str = '';
    		let total_row=$('#process_body tbody tr').length;

    		for(i=1; i<=total_row; i++)
    		{ 
				process_val = ''
				process_id 	= $("#hidden_process_id_"+i).val();
				qty			= $("#process_qty_"+i).val();
				process_val = process_id +"#"+qty;	
				if(i==1){
					process_str = process_val;
				}
				else
				{
					process_str += '@@'+process_val; 
				}
    		}
			let total_qty = $('#total_process_qty').val(); 
			$('#hidden_process_str').val(process_str); 
    		parent.emailwindow.hide();
    	}
    	function fn_get_total()
    	{
    		let total_qty=0;
    		let total_row=$('#process_body tbody tr').length; 
    		for(i=1; i<=total_row; i++)
    		{
    			process_qty = $('#process_qty_'+i).val()*1;
				qty = isNaN(process_qty) ? 0 : process_qty;
				// console.log(qty);
    			total_qty += qty; 
    		}
			
			$('#total_process_qty').val(total_qty);
    	}

	
    </script>
    </head>

    <body> 
		<form name="searchbatchnofrm"  id="searchbatchnofrm">
				<fieldset style="width:280px;">
					<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">  
						<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
							<thead class="form_caption" >	  
								<tr>
									<th width="120">Process</th>
									<th width="120">Rate </th> 
								</tr>
							</thead>
						</table>
						<div style="width:<?= $width+20;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
							<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="process_body" width="<?= $width; ?>" rules="all" align="left">
								<tbody>
									<?
									$i = 0 ; 
									$total_rate = 0;
									foreach ($data_arr as $v) 
									{   $i++;
										$prev_rate = $prve_rate_array[$v['ID']];
										$total_rate += $prev_rate ;
										if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";
										?>
											<tr bgcolor="<?= $bgcolor; ?>" id="tr_1nd<?= $i; ?>">
												<td width="120"> <strong><?= $v['OPERATION_NAME'] ?> </strong> </td>
												<td width="120" align="right">  
													<input onchange="fn_get_total()"  style="width: 110px;" class="text_boxes_numeric" type="text" id="process_qty_<?= $i; ?>" value="<?= $prev_rate ?>" > 
													<input type="hidden" name="hidden_process_id_<?= $i; ?>" id="hidden_process_id_<?= $i; ?>" value="<?= $v['ID'] ?>">
												 </td> 
											</tr> 
										<? 
									}
									?>
								</tbody> 
							</table> 
						</div>
						<div style="width:<?= $width+20;?>px;float:left;">
							<table style="float:left;" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" width="<?= $width;?>">
								<tfoot>
									<tr>
										<th width="120" align="right">Total</th>
										<th width="120"> 
											<input style="width: 110px;" class="text_boxes_numeric" id="total_process_qty"  type="text" readonly value="<?= $total_rate ?>">
											<input type="hidden" name="hidden_process_str" id="hidden_process_str" value="<?= $rate_data ?>">
										
										</th> 
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</fieldset> 
				<div style="text-align: center;">
					<input type="button" value="Close" class="formbutton" onClick="js_set_value(<?=$row_id?>)">  
				</div>
		</form> 
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}

if($action=="create_wo_no_list_view")
{
    //echo $data;die;
    $data=explode("_",$data);
    //print_r($data);
    $wo_no=$data[0];
    $buyer_id=$data[1];
    $company_id=$data[2];
    $cob_year=$data[3];
    $job_no=$data[4];
    $item_id=$data[5];
    $style_no=$data[6];
    $buyer_order=$data[7];
    $working_company=$data[8];
    $rate_bill_var=$data[9];
   // list($style_no,$buyer_id,$company_id,$cob_year,$job_no,$item_id,$wo_no)=explode("_",$data);   

    if($buyer_id==0)$buyer_id=" "; else $buyer_id=" and b.buyer_id =$buyer_id ";    
    
    if($buyer_order=='')$buyer_order=" "; else $buyer_order=" and c.po_number like('%".trim($buyer_order)."%') ";   
    if($style_no=='')$style_no=" "; else $style_no=" and b.style_ref like('%".$style_no."%') "; 
    if($job_no=='')$job_no=" "; else $job_no=" and d.job_no_prefix_num='$job_no' ";    
    if($item_id==0)$item_id=" "; else $item_id=" and b.item_id='$item_id' ";    
    if($working_company==0)$working_con=" "; else $working_con=" and a.working_company_id='$working_company' ";    

    if($wo_no=='') $wo_con=" "; else $wo_con=" and a.sys_number_prefix_num=$wo_no "; 
    $rate_bill_cond = ($rate_bill_var==2) ? ' and a.rate_bill_var =2 ' : ' and a.rate_bill_var NOT IN (2) ';
   
    
    
    if($db_type==0)
    {
    
        if($cob_year=='')$cob_year=""; else $cob_year="and year(a.wo_date)='$cob_year'";
        $year=" , year(a.wo_date) as year ";    
    }
    else
    {
        if($cob_year=='')$cob_year=""; else $cob_year="and to_char(a.wo_date,'YYYY')='$cob_year'";  
        $year=" , to_char(a.wo_date,'YYYY') as year ";  
    }

    // $sql="SELECT a.id,a.sys_number,a.sys_number_prefix_num , a.company_id, a.wo_date,b.buyer_id,b.avg_rate,b.amount,b.wo_qty,b.po_qty,b.id as dtls_id,c.po_number,d.job_no,b.item_id,b.style_ref $year,( select sum(l.bill_qty)  from piece_rate_bill_dtls l where b.id = l.wo_dtls_id and l.status_active=1 ) as bill_qty from piece_rate_wo_urmi_mst a,piece_rate_wo_urmi_dtls b ,wo_po_break_down c,wo_po_details_master d where a.id=b.mst_id and b.po_id=c.id and  b.job_id=d.id and b.status_active=1 and a.status_active=1 and a.company_id=$company_id  $buyer_id  $buyer_order  $style_no $cob_year $item_id $job_no $wo_con  $working_con order by a.id,b.id,c.id";

    $sql="SELECT a.id,a.sys_number,a.sys_number_prefix_num , a.company_id, a.wo_date,b.buyer_id,b.avg_rate,b.amount,b.wo_qty,b.po_qty,b.id as dtls_id,c.po_number,d.job_no,b.item_id,b.style_ref $year,( select sum(l.bill_qty)  from piece_rate_bill_dtls l where b.id = l.wo_dtls_id and l.status_active=1 ) as bill_qty, 1 as type from piece_rate_wo_urmi_mst a,piece_rate_wo_urmi_dtls b ,wo_po_break_down c,wo_po_details_master d where a.id=b.mst_id and b.po_id=c.id and  b.job_id=d.id and b.status_active=1 and a.status_active=1 and a.company_id=$company_id  $buyer_id  $buyer_order  $style_no $cob_year $item_id $job_no $wo_con  $working_con
    
    union all

    SELECT a.id,a.sys_number,a.sys_number_prefix_num , a.company_id, a.wo_date,b.buyer_id,b.avg_rate,b.amount,b.wo_qty,null as po_qty,b.id as dtls_id,c.po_number,d.job_no,b.item_id,b.style_ref $year,( select sum(l.bill_qty)  from piece_rate_bill_dtls l where b.id = l.wo_dtls_id and l.status_active=1 ) as bill_qty, 2 as type from piece_rate_wo_mst a,piece_rate_wo_dtls b ,wo_po_break_down c,wo_po_details_master d where a.id=b.mst_id and b.order_id=c.id and  b.job_id=d.id and b.status_active=1 and a.status_active=1 and a.company_id=$company_id $rate_bill_cond $buyer_id  $buyer_order  $style_no $cob_year $item_id $job_no $wo_con 
    ";
    // echo $sql;
    $result = sql_select($sql);

   $buyer_part=return_library_array("select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id group by buy.id,buy.buyer_name order by buyer_name","id","buyer_name");

    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table">
        <thead>
            <th width="50">SL</th>
            <th width="60">Year</th>
            <th width="180">Buyer</th>
            <th width="170">Style</th>
            <th width="80">Wo No</th>
            <th width="80">Job No</th>
            <th width="180">Item</th>
            <th width="140">PO</th>
            <th >Qty</th>



        </thead>
    </table>
    <div style="width:1020px; max-height:330px; overflow-y:scroll" id="list_container_batch" align="left">   
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";  
                if($row[csf('bill_qty')]<$row[csf('wo_qty')])
                {
                       ?>
                        <tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')].'*'.$row[csf('dtls_id')].'*'.$row[csf('type')]; ?>',<? echo $i; ?>);"> 
                            <td width="50" align="center"><? echo $i; ?></td>
                            <td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                            <td width="180"><p><? echo $buyer_arr[$row[csf('buyer_id')]];  ?></p></td>
                            <td width="170"><p><? echo $row[csf('style_ref')]; ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('sys_number_prefix_num')]; ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('job_no')]; ?></p></td>
                            <td width="180"><p><? echo $garments_item[$row[csf('item_id')]]; ?></p></td>
                            <td width="140"><p><? echo $row[csf('po_number')]; ?></p></td>
                            <td  align="right"><p><? echo number_format($row[csf('wo_qty')]); ?> &nbsp;</p></td>
                        </tr>
                    <?
                    $i++;
                }
            }
            ?>
        </table>
    </div>
        <table width="100%">
            <tr>
                <td align="left">
                    Check / Uncheck All <input type="checkbox" name="checkall" onClick="check_all_data()">
                </td>
                <td align="center">
                    <input type="hidden" name="txt_selected_id" id="txt_selected_id" value="">
                   
                    <input type="button" value="Close" class="formbutton" onClick="close_popup();" />
                </td>
            </tr>
        </table>
        
    <?
    exit();
}



if($action=="check_conversion_rate") //Conversion Exchange Rate
{ 
    $data=explode("**",$data);
    if($db_type==0)
    {
        $conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
    }
    else
    {
        $conversion_date=change_date_format($data[1], "d-M-y", "-",1);
    }
    $currency_rate=set_conversion_rate( $data[0], $conversion_date );
    echo "1"."_".$currency_rate;
    exit(); 
}


if ($action=="systemId_popup")
{
    echo load_html_head_contents("System ID Info", "../../", 1, 1,'','','');
    ?>
    <script>
        function js_set_value(id,type)
        { 
            $('#hidden_mst_id').val(id);
            $('#hidden_type_id').val(type);
            parent.emailwindow.hide();
        }
    </script>
    
    
    </head>

    <body>
    <div align="center" style="width:840px;">
        <form name="searchsystemidfrm"  id="searchsystemidfrm">
            <fieldset style="width:830px;">
            <legend>Enter search words</legend>
                <table cellpadding="0" cellspacing="0" width="100%" border="1" rules="all" class="rpt_table">
                    <thead>
                        <th>System/Bill No</th>
                        <th>Buyer</th>
                        <th>WO No</th>
                        
                        <th colspan="2">Bill Date</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                            <input type="hidden" name="txt_company_id" id="txt_company_id" value="<? echo $cbo_company_id; ?>">
                            <input type="hidden" id="hidden_mst_id">
                            <input type="hidden" id="hidden_type_id">
                        </th>
                    </thead>
                    <tr>
                        <td align="center">
                            <input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:100px;" />
                        </td>
                        <td align="center">
                            <?
                                //echo create_drop_down( "cbo_service_provider_id", 150, $service_provider_arr,"", 1, "-- Select --", 0, "",0 );
                                
                            echo create_drop_down( "cbo_buyer_name", 120, "select buyer_name,id from lib_buyer where is_deleted=0  and status_active=1 order by buyer_name","id,buyer_name", 1, "--Select Buyer--", 0, "",0 );                          
                                
                            ?>
                        </td>
                        <td align="center">
                            <input type="text" id="txt_order" name="txt_order" class="text_boxes" style="width:100px;"/>
                        </td>
                        
                        <td align="center">
                            <input type="text" style="width:100px;" class="datepicker"  name="txt_from_date" id="txt_from_date" readonly />   
                        </td>
                        <td align="center">
                            <input type="text" style="width:100px;" class="datepicker"  name="txt_to_date" id="txt_to_date" readonly />   
                        </td>
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_system_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_order').value+'_'+document.getElementById('txt_from_date').value+'_'+document.getElementById('txt_to_date').value+'_'+document.getElementById('txt_company_id').value, 'price_rate_list_view', 'search_div', 'piece_rate_bill_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                        </td>
                    </tr>
                </table>
                <table width="100%" style="margin-top:5px;">
                    <tr>
                        <td colspan="5">
                            <div style="margin-top:10px; margin-left:3px;" id="search_div" align="left"></div>
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


if($action=="price_rate_list_view")
{
    list($sysid,$buyer,$wo_no,$from_date,$to_date,$company_id)=explode("_",$data);  
    $supp_arr = return_library_array("SELECT a.id,a.supplier_name FROM lib_supplier a,lib_supplier_party_type b,lib_supplier_tag_company c WHERE a.id=b.supplier_id and a.id=c.supplier_id and b.party_type  in(22,36) and c.tag_company =$company_id  and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id","supplier_name");

    
    if($sysid=='')$sysid=" "; else $sysid=" and a.sys_number like('%".trim($sysid)."%')";   
    if($buyer==0)$buyer=" "; else $buyer=" and b.buyer_id ='$buyer'";   
   

    if($wo_no=="")$order_con=" "; else $order_con=" and c.sys_number like('%".$wo_no."%') ";    

    if($from_date!='' && $to_date!=''){ 
        if($db_type==0){
            
            $from_date=change_date_format($from_date);
            $to_date=change_date_format($to_date);
        }
        else
        {
            $from_date=change_date_format($from_date,'','',-1);
            $to_date=change_date_format($to_date,'','',-1);
        }
        $date_con=" and a.bill_date BETWEEN '$from_date' and '$to_date'";   
    }
    else
    {
        $date_con="";   
    }
    
    // $sql = "SELECT a.id,a.sys_number, a.working_company_id,a.source, a.bill_date,sum(b.bill_qty) as bill_qty ,a.upcharge,a.discount,a.grand_total,c.sys_number as wo_no 
    // from piece_rate_bill_mst a, piece_rate_bill_dtls b , piece_rate_wo_urmi_mst c,piece_rate_wo_urmi_dtls d 
    // where a.id=b.mst_id and b.wo_dtls_id=d.id and c.id =d.mst_id and   a.company_id=$company_id  $sysid  $buyer  $date_con $order_con and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and d.status_active=1  group by a.id,a.sys_number, a.working_company_id,a.source, a.bill_date,a.upcharge,a.discount,a.grand_total,c.sys_number order by a.id";

    $sql = "SELECT a.id,a.sys_number, a.working_company_id,a.source, a.bill_date,sum(b.bill_qty) as bill_qty ,a.upcharge,a.discount,a.grand_total,c.sys_number as wo_no, b.type
    from piece_rate_bill_mst a, piece_rate_bill_dtls b , piece_rate_wo_urmi_mst c,piece_rate_wo_urmi_dtls d 
    where a.id=b.mst_id and b.wo_dtls_id=d.id and c.id =d.mst_id and a.company_id=$company_id  $sysid  $buyer  $date_con $order_con and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and d.status_active=1 and b.type=1
    group by a.id,a.sys_number, a.working_company_id,a.source, a.bill_date,a.upcharge,a.discount,a.grand_total,c.sys_number, b.type

    union all
    
    SELECT a.id,a.sys_number, a.working_company_id,a.source, a.bill_date,sum(b.bill_qty) as bill_qty ,a.upcharge,a.discount,a.grand_total,c.sys_number as wo_no, b.type
    from piece_rate_bill_mst a, piece_rate_bill_dtls b , piece_rate_wo_mst c,piece_rate_wo_dtls d 
    where a.id=b.mst_id and b.wo_dtls_id=d.id and c.id =d.mst_id and a.company_id=$company_id  $sysid  $buyer  $date_con $order_con and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and d.status_active=1 and b.type=2
    group by a.id,a.sys_number, a.working_company_id,a.source, a.bill_date,a.upcharge,a.discount,a.grand_total,c.sys_number, b.type
    ";
    // echo $sql;
    $result = sql_select($sql);

    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table">
        <thead>
            <th width="50">SL</th>
            <th width="150">System/Bill No</th>
            <th width="150">Working Company</th>
            <th width="150">Wo No</th>
            <th >Bill Qty</th>
        </thead>
    </table>
    <div style="width:815px; max-height:220px; overflow-y:scroll" id="list_container_batch" align="left">    
        <table cellspacing="0" cellpadding="0" border="1" rules="all"  width="797" class="rpt_table" id="tbl_list_search">  
        <?
            
            
            $i=1;
            foreach ($result as $row)
            {  
                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";  
            
            ?>
                <tr id="tr_<? echo $row[csf('id')]; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onClick="js_set_value(<? echo $row[csf('id')]; ?>,<? echo $row[csf('type')]; ?>)" > 
                    <td width="50" align="center"><? echo $i; ?></td>
                    <td width="150" align="center"><p><? echo $row[csf('sys_number')]; ?></p></td>
                    <td width="150">
                        <p>
                        <?php 

                            if($row[csf('source')]==1)
                            {
                                echo $company_arr[$row[csf('working_company_id')]];
                            }else{
                                echo $supp_arr[$row[csf('working_company_id')]];
                            }

                         ?>
                      
                            
                        </p>
                    </td>
                    <td width="150" align="center"><? echo $row[csf('wo_no')]; ?></td>
                    <td  align="right"><p><? echo number_format($row[csf('bill_qty')],2,'.',''); ?></p></td>
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




if($action=="check_unique")
{
    
    $operation_arr=explode("__",$operation);
    $flag=0;
    foreach($operation_arr as $operation_values)
    { 
    list($id,$job_no,$buyer_id,$buyer_name,$style_ref_no,$gmts_item_id,$gmts_item,$po_id,$po_number)=explode("**",$operation_values);
    
    $is_duplicate = is_duplicate_field( "id", "piece_rate_wo_dtls", "mst_id='$mst_id' and job_id='$id' and order_id='$po_id' and item_id='$gmts_item_id'" );//
    
    if($is_duplicate==1){
        if($items=='')$items=$gmts_item; else $items.=' and '.$gmts_item;
        $flag=1;
        }
        else
        {
        $flag=0;
        }
    }
    
    if($flag==1){echo $items;}else{echo 0;}

    exit();
}





if($action=="show_price_rate_wo_listview___off")
{


    if($db_type==0)
    {
        $sql = "select a.id,a.company_id,a.service_provider_id,group_concat(b.item_id) as item_id,group_concat(b.buyer_id) as buyer_id,group_concat(b.order_id) as order_id,b.order_source,b.job_id from  piece_rate_wo_mst a,piece_rate_wo_dtls b where a.id=b.mst_id and a.id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.job_id,b.order_source,a.company_id,a.service_provider_id,a.id"; 
    }
    else
    {
         $sql = "select a.id,a.company_id,a.service_provider_id,LISTAGG(CAST(b.item_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.item_id) as item_id,LISTAGG(CAST(b.buyer_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.buyer_id) as buyer_id,LISTAGG(CAST(b.order_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.order_id) as order_id,b.order_source,b.job_id from  piece_rate_wo_mst a,piece_rate_wo_dtls b where a.id=b.mst_id and a.id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.job_id,b.order_source,a.company_id,a.service_provider_id,a.id"; 
    }
    
       // echo $sql; 
    $result = sql_select($sql);
    foreach ($result as $row)
    {  
        $poIdArr[$row[csf('order_source')]][]=$row[csf('order_id')];
        //$jobIdArr[$row[csf('order_source')]]=$row[csf('job_id')];
    }


   $sql="select id,job_no_mst,po_number from wo_po_break_down where status_active = 1 and is_deleted = 0 ";
    $p=1;
    
    $po_id_chunk_arr=array_chunk(array_unique(explode(',',implode(',',$poIdArr[1]))),999);
    foreach($po_id_chunk_arr as $jobIdArr)
    {
        if($p==1) $sql .="  and ( id in(".implode(",",$jobIdArr).")"; 
        else  $sql .=" or id in(".implode(",",$jobIdArr).")";
        
        $p++;
    }
    $sql .=")";


    $po_sql_result = sql_select($sql);
    foreach($po_sql_result as $row)
    {
        $job_arr[$row[csf('id')]]=$row[csf('job_no_mst')];
        $po_number_arr[$row[csf('id')]]=$row[csf('po_number')];
        
    }


    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table">
        <thead>
            <th width="50">SL</th>
            <th width="100">Job Number</th>
            <th width="120">Company</th>
            <th width="120">Service Provider</th>
            <th width="200">Order No</th>
            <th width="150">Buyer</th>
            <th>Item</th>
        </thead>
        
        
    </table>
    <div style="width:900px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">    
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
             
                if($row[csf('order_source')]==1)
                {
                    $job_arrs=$job_arr; $po_number_arrs=$po_number_arr; $buyer_arrs=$buyer_arr;
                }
                else
                {
                    $job_arrs=$subcon_job_arr; $po_number_arrs=$subcon_po_number_arr;$buyer_arrs=$subcon_buyer_arr;
                }       
              
             
             
              $item_conca='';
              $items=array_unique(explode(",",$row[csf('item_id')]));
              foreach($items as $item_id)
              {
                if($item_conca=='')$item_conca=$garments_item[$item_id]; else $item_conca.=','.$garments_item[$item_id];  
              }
                
              $order_conca='';
              $orders=array_unique(explode(",",$row[csf('order_id')]));
              foreach($orders as $order_id)
              {
                if($order_conca=='')$order_conca=$po_number_arrs[$order_id]; else $order_conca.=','.$po_number_arrs[$order_id]; 
                $job_no=$job_arrs[$order_id];
              }
                
                
              $buyer_conca='';
              $buyers=array_unique(explode(",",$row[csf('buyer_id')]));
              foreach($buyers as $buyer)
              {
                if($buyer_conca=='')$buyer_conca=$buyer_arrs[$buyer]; else $buyer_conca.=','.$buyer_arrs[$buyer];  
              }
                
              $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";    
                
            ?>
                <tr id="tr_<? echo $row[csf('id')]; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="show_list_view('<? echo $row[csf('id')].'_'.$row[csf('job_id')]; ?>', 'populate_price_rat_dtls_form_data', 'details_entry_list_view', 'requires/piece_rate_bill_controller', '');set_button_status(1, '<? echo $_SESSION['page_permission']; ?>', 'fnc_prices_rate_wo',1)" > 
                    <td width="50" align="center"><? echo $i; ?></td>
                    <td width="100" align="center"><? echo $job_no; ?></td>
                    <td width="120" align="center"><? echo $company_arr[$row[csf('company_id')]]; ?></td>
                    <td width="120" align="center"><p><? echo $supplier_arr[$row[csf('service_provider_id')]]; ?></p></td>
                    <td width="200"><p><? echo $order_conca; ?></p></td>
                    <td width="150"><p><? echo $buyer_conca; ?></p></td>
                    <td><p><? echo $item_conca; ?></p></td>
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



if($action=='populate_price_rat_dtls_form_data')
{
    list($mst_id,$type)=explode('__',$data);
    // echo "Hello-". $data; die;
    $rate_bill_var = return_field_value("rate_bill_var","piece_rate_bill_mst","id=$mst_id and status_active=1 and is_deleted=0");
    $sql = "SELECT id, mst_id, wo_dtls_id,bill_qty,amount,avg_rate,rate_history,remarks,type from piece_rate_bill_dtls where mst_id=$mst_id and type=$type and status_active=1 and is_deleted=0";
    $i=1;
    $data_array=sql_select($sql);

    $dtlsArr=array();
    $typeArr = array();
    $dtsArr=array();
    foreach ($data_array as $row) {
        array_push($dtlsArr, $row[csf('wo_dtls_id')]);
        array_push($typeArr, $row[csf('type')]);
        $dtsArr[$row[csf('wo_dtls_id')]]['id']=$row[csf('id')];
        $dtsArr[$row[csf('wo_dtls_id')]]['bill_qty']=$row[csf('bill_qty')];
        $dtsArr[$row[csf('wo_dtls_id')]]['amount']=$row[csf('amount')];
        $dtsArr[$row[csf('wo_dtls_id')]]['avg_rate']=$row[csf('avg_rate')];
        $dtsArr[$row[csf('wo_dtls_id')]]['rate_history']=$row[csf('rate_history')];
        $dtsArr[$row[csf('wo_dtls_id')]]['remarks']=$row[csf('remarks')];
    }
    $type = implode(',', array_unique($typeArr));

    if($type == 1)
    {
        $sql_main="SELECT a.id,a.sys_number,a.sys_number_prefix_num , a.company_id, a.wo_date,b.buyer_id,b.avg_rate,b.amount,b.wo_qty,b.po_qty,b.id as dtls_id,c.po_number,b.po_id,b.job_id,d.job_no,b.item_id,b.style_ref,b.uom,b.color_type ,b.client_id,d.product_dept,1 as type
        from piece_rate_wo_urmi_mst a,piece_rate_wo_urmi_dtls b ,wo_po_break_down c,wo_po_details_master d 
        where a.id=b.mst_id and b.po_id=c.id and b.job_id=d.id and b.status_active=1 and a.status_active=1  and b.id in(".implode(',',$dtlsArr).") ";
    }
    elseif($type == 2)
    {
        $sql_main="SELECT a.id,a.sys_number,a.sys_number_prefix_num , a.company_id, a.wo_date,b.buyer_id,b.avg_rate,b.amount,b.wo_qty,c.po_quantity as po_qty,b.id as dtls_id,c.po_number,b.order_id as po_id,b.job_id,d.job_no,b.item_id,b.style_ref,b.uom,b.color_type ,null as client_id,2 as type,a.prod_reso_allo,a.line_id,d.product_dept
        from piece_rate_wo_mst a,piece_rate_wo_dtls b ,wo_po_break_down c,wo_po_details_master d 
        where a.id=b.mst_id and b.order_id=c.id and b.job_id=d.id and b.status_active=1 and a.status_active=1  and b.id in(".implode(',',$dtlsArr).") ";
        // null as po_qty
    }

    
    // echo $sql_main;die;
    $sql_result = sql_select($sql_main);

    $client_arr=return_library_array("select a.id,a.buyer_name from lib_buyer a where a.status_active =1 and a.is_deleted=0 group by a.id,a.buyer_name  order by a.id",'id','buyer_name');

    // SEWING LINE 
    if ($rate_bill_var ==2 ) //PO WISE
    {        
        $prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
        $lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1"); 
        foreach($lineDataArr as $lRow)
        {
            $lineArr[$lRow['ID']]=$lRow['LINE_NAME'];  
        }


        foreach ($sql_result as $v) 
        {
            if($v['PROD_RESO_ALLO']==1)
            {
                $line_name = ""; 
                $sewing_line_id_arr=explode(",",$prod_reso_arr[$v['LINE_ID']]);
                foreach ($sewing_line_id_arr as $r) 
                {					 
                    $line_name .= ($line_name=="") ? $lineArr[$r] : ",". $lineArr[$r];
                } 
            } 
            else
            { 
                $line_name=$lineArr[$v['LINE_ID']];
            }
            $line_name_arr[$v['LINE_ID']] = $line_name;
        }
    } 

    $i=($serial+1);
    foreach($sql_result as $row)
    {
        $rate_history = $dtsArr[$row[csf('dtls_id')]]['rate_history']; 
        $avg_rate =toRound( $dtsArr[$row[csf('dtls_id')]]['avg_rate']);
		$attrCond = "value='$avg_rate' onkeyup='calculate()'";
		if ($rate_bill_var == 2)  //PO WISE
		{
            $row_id = $i;  //-1
			$product_dept = $row['PRODUCT_DEPT'];
			$item = $row['ITEM_ID']; 
			$attrCond = " value='$avg_rate' onDblClick='openmypage_avg_rate($row_id,$product_dept,$item);' readonly";
		}
        // echo $rate_bill_var ; die;
        ?>
        <tr>                               
            <td>
                 <input type="hidden" id="detailsUpdateId_<? echo $i; ?>" name="detailsUpdateId_<? echo $i; ?>" value="<?php echo $dtsArr[$row[csf('dtls_id')]]['id'];?>" />
                 <input type="text" name="txtwono_<? echo $i; ?>" id="txtwono_<? echo $i; ?>" class="text_boxes" style="width:100px;" value="<?php echo $row[csf('sys_number')]; ?>" onDblClick="openmypage_wo_no(<? echo $i;?>);" />
                 <input type="hidden" name="txtwodtlsid_<? echo $i; ?>" id="txtwodtlsid_<? echo $i; ?>" value="<?php echo $row[csf('dtls_id')];?>">
                 <input type="hidden" name="txttype_<? echo $i; ?>" id="txttype_<? echo $i; ?>" value="<?php echo $row['TYPE'];?>">
                <input type="hidden" name="sewingLineId_<? echo $i; ?>" id="sewingLineId_<? echo $i; ?>" value="<?php echo $row['LINE_ID'];?>" />
                <input type="hidden" name="prodResoAllo_<? echo $i; ?>" id="prodResoAllo_<? echo $i; ?>" value="<?php echo $row['PROD_RESO_ALLO'];?>" />
            </td>
           
            <td>
                 <input type="text" name="txtbuyer_<? echo $i; ?>" id="txtbuyer_<? echo $i; ?>" class="text_boxes" style="width:80px;" readonly value="<?php echo $buyer_arr[$row[csf('buyer_id')]];?>" />
                 <input type="hidden" name="txtbuyerid_<? echo $i; ?>" id="txtbuyerid_<? echo $i; ?>" value="<?php echo $row[csf('buyer_id')];?>" />
            </td>
             <td>
                 <input type="text" name="client_<? echo $i; ?>" id="client_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<?php echo $client_arr[$row[csf('client_id')]]; ?>" readonly />
                 <input type="hidden" name="clientid_<? echo $i; ?>" value="<?php echo $row[csf('client_id')]; ?>" id="clientid_<? echo $i; ?>" />
            </td>
            <? 
                if ($rate_bill_var ==2) 
                { 
                    ?>
                        <td>
                            <input type="text" name="sewing_line_<? echo $i; ?>" id="sewing_line_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<?php echo $line_name_arr[$row['LINE_ID']];?>" readonly /> 
                        </td>

                    <?
                }     
            ?>
            <td>
                 <input type="text" name="txtstyle_<? echo $i; ?>" id="txtstyle_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<?php echo $row[csf('style_ref')];?>" readonly />
            </td>
            <td>
                 <input type="text" name="txtjobno_<? echo $i; ?>" id="txtjobno_<? echo $i; ?>" class="text_boxes" style="width:100px;" value="<?php echo $row[csf('job_no')];?>" readonly />
                 <input type="hidden" name="txtjobid_<? echo $i; ?>" id="txtjobid_<? echo $i; ?>">
            </td>
            <td>
                 <input type="text" name="txtitem_<? echo $i; ?>" id="txtitem_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<? echo $garments_item[$row[csf("item_id")]];?>"  readonly />
                 <input type="hidden" name="txtitemid_<? echo $i; ?>" id="txtitemid_<? echo $i; ?>" value="<? echo $row[csf("item_id")];?>" />
            </td>
             <td>
                <input type="text" name="po_<? echo $i; ?>" id="po_<? echo $i; ?>" class="text_boxes_numeric" value="<?php echo $row[csf('po_number')];?>" style="width:80px;" readonly>
                <input type="hidden" name="poid_<? echo $i; ?>" id="poid_<? echo $i; ?>" value="<?php echo $row[csf('po_id')];?>"  />
            </td>
            <td>
                <input type="text" name="poqty_<? echo $i; ?>" id="poqty_<? echo $i; ?>" class="text_boxes_numeric" value="<?php echo number_format($row[csf('po_qty')],2,'.','');?>" style="width:80px;" readonly>
            </td>
            <td>
                 <? 
                echo create_drop_down( "colortype_".$i, 114, $color_type,"",1, "--Select--", $row[csf('color_type')],"",1,"" ); 
                ?>                                    
            </td>
           
            <td>
                <input type="text" name="txtwoqty_<? echo $i; ?>" id="txtwoqty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px;" value="<?php echo number_format($row[csf('wo_qty')],2,'.','');?>"  readonly />
            </td>
             <td>
                <input type="text" name="txtbillqty_<? echo $i; ?>" id="txtbillqty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px;" value="<?php echo number_format($dtsArr[$row[csf('dtls_id')]]['bill_qty'],2,'.','');?>" onkeyup="calculate()" />
            </td>
            <td>
                <? 
                echo create_drop_down( "cbodtlsuom_".$i, 80, $unit_of_measurement,"",1, "--Select--", $row[csf('uom')],"",1,"1,2" ); 
                ?>                                    
            </td>
            <td>
                <?php
                     $rate=$dtsArr[$row[csf('dtls_id')]]['avg_rate'];

                 ?>
                 <input type="text" name="txtavgrate_<? echo $i; ?>" id="txtavgrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px;"  <?=$attrCond ?> />
                 <input type="hidden" name="txtRate_<?= $i; ?>" id="txtRate_<?= $i; ?>" value="<?= $rate_history ?>" />
                 
            </td>
           
            <td>
                 <input type="text" name="txtdtlamount_<? echo $i; ?>" id="txtdtlamount_<? echo $i; ?>" class="text_boxes_numeric" value="<?php echo number_format($dtsArr[$row[csf('dtls_id')]]['amount'],2,'.',''); ?>" style="width:80px;"  />
            </td>
            <td>
                <input type="text" name="txtremarks_<? echo $i; ?>" id="txtremarks_<? echo $i; ?>" class="text_boxes" style="width:80px;"  value="<?php echo $dtsArr[$row[csf('dtls_id')]]['remarks'];?>" />
            </td>
             <td align="center">
                <input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:27px" class="formbuttonplasminus" value="+" onClick="fn_addRow(<? echo $i; ?>)"/>
                <input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:27px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);"/>
            </td>
        </tr>
                                
     

        <?
        $i++;
    }
    exit();
}







if($action=='populate_price_rat_mst_form_data')
{
    
    
    $sql = "select id,sys_number, company_id,source, working_company_id, bill_date, currency, exchange_rate,location, remarks,upcharge,discount,grand_total,manual_bill from piece_rate_bill_mst where id=$data and status_active=1 and is_deleted=0"; 
    
    $data_array=sql_select($sql);
    foreach ($data_array as $row)
    { 
        echo "document.getElementById('update_id').value                    = '".$row[csf("id")]."';\n";
        echo "document.getElementById('txt_system_id').value                = '".$row[csf("sys_number")]."';\n";
        echo "document.getElementById('cbo_company_id').value               = '".$row[csf("company_id")]."';\n";
        echo "document.getElementById('cbo_source').value                 = '".$row[csf("source")]."';\n";
        echo "load_drop_down( 'requires/piece_rate_bill_controller', document.getElementById('cbo_source').value+'**'+document.getElementById('cbo_company_id').value, 'load_drop_down_working_company', 'working_company_td' );\n";
        echo "document.getElementById('cbo_working_company').value      = '".$row[csf("working_company_id")]."';\n";
       // echo "load_drop_down( 'requires/piece_rate_bill_controller',$row[csf('source')]+'**'+$row[csf('company_id')],'load_drop_down_working_company','working_company_td' )";
        
        echo "document.getElementById('upcharge').value                 = '".$row[csf("upcharge")]."';\n";
        echo "document.getElementById('discount').value             = '".$row[csf("discount")]."';\n";
        echo "document.getElementById('cbo_currency').value                 = '".$row[csf("currency")]."';\n";
        echo "document.getElementById('txt_exchange_rate').value            = '".$row[csf("exchange_rate")]."';\n";
        echo "document.getElementById('txt_remarks_mst').value              = '".$row[csf("remarks")]."';\n";
        echo "document.getElementById('cbo_location').value                 = '".$row[csf("location")]."';\n";
       
        echo "document.getElementById('txt_manual_bill').value                 = '".$row[csf("manual_bill")]."';\n";
        echo "document.getElementById('grand_total').value                 = '".$row[csf("grand_total")]."';\n";
        echo "document.getElementById('txt_bill_date').value                  = '".change_date_format($row[csf("bill_date")])."';\n";

        echo "$('#cbo_company_id').attr('disabled','disabled');\n";
        
        exit();
    }
}







if($action=="price_rate_wo_print")
{
    extract($_REQUEST);

    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");

    $sql = "select working_company_id,sys_number,bill_date,company_id,currency,upcharge,grand_total,discount,source,manual_bill from piece_rate_bill_mst where id='$data' and status_active=1 and is_deleted=0"; 
    //echo $sql;
    $data_array=sql_select($sql);
    $company_id=$data_array[0][csf("company_id")];
    $upcharge=$data_array[0][csf("upcharge")];
    $sys_number=$data_array[0][csf("sys_number")];
    $currency=$data_array[0][csf("currency")];
    $discount=$data_array[0][csf("discount")];
    $grand_total=$data_array[0][csf("grand_total")];
    $source=$data_array[0][csf("source")];
    $comp_info=sql_select("select a.*,b.country_name from lib_company a,lib_country b where a.country_id=b.id and a.id='$company_id'");
     

    $data_arr=sql_select("SELECT a.id,a.supplier_name FROM lib_supplier a,lib_supplier_party_type b,lib_supplier_tag_company c WHERE a.id=b.supplier_id and a.id=c.supplier_id   and c.tag_company =$company_id");
        foreach ($data_arr as $row)
        { 
           $sp_arr[$row[csf("id")]]=$row[csf("supplier_name")];
        }


    ?>
    <table cellspacing="5" cellpadding="5" border="1" rules="all"  >
        <tr>
                <p align="left">
                   <?
                           $data_row=sql_select("select image_location  from common_photo_library  where master_tble_id='$company_id' and form_name='company_details' and is_deleted=0 and file_type=1");
                            ?>
                            <td  align="left" colspan="3">
                            <?
                            foreach($data_row as $img_row)
                            {
                                ?>
                                <img src='../../<? echo $img_row[csf('image_location')]; ?>' height='70' align="middle" />  
                                <? 
                            }
                            ?>
                 </p>
      
       
                <td colspan="10" align="center"><b style="font-size:36px; font-weight:bold;">
                    <? echo $company_library[$data_array[0][csf("company_id")]];//$comp_info[0][csf("company_name")]; ?></b><br>
                    <? echo $comp_info[0][csf("plot_no")];?>,
                    <? echo $comp_info[0][csf("level_no")];?>,
                    <? echo $comp_info[0][csf("road_no")];?>,
                    <? echo $comp_info[0][csf("block_no")];?>,
                    <? echo $comp_info[0][csf("city")];?>,
                    <? echo $comp_info[0][csf("zip_code")];?>,
                    <? echo $comp_info[0][csf("province")];?>,
                    <? echo $comp_info[0][csf("country_name")];?><br>
                    <? echo $comp_info[0][csf("email")];?>,
                    <? echo $comp_info[0][csf("website")];?><br>
                    
                </td>
            
       </tr> 
       <tr>
           <td colspan="12" align="center">
               Piece Rate Bill
           </td>
       </tr>
       <tr>
            <td colspan="12" align="center">Bill No.: <b><? echo $data_array[0][csf("sys_number")];?></b></td>
       </tr>
       <tr>
            <td colspan="6"><b>Work Order To :</b> <? echo $source==1 ? $company_library[$data_array[0][csf("working_company_id")]]: $sp_arr[$data_array[0][csf("working_company_id")]]; ?></td>
            <td colspan="7">Bill Date : <? echo change_date_format($data_array[0][csf("bill_date")]);?></td>
       </tr> 
   </table>
   <br>
   <table cellspacing="5" cellpadding="5" border="1" rules="all">
       
        <tr>
            <th >SL</th>
            <th>Wo No</th>
            <th >Buyer</th>
            <th>Style</th>
            <th >Job No</th>
            <th >Item</th>
            <th>Po</th>
            <th >Color Type</th>
            <th >WO Qty</th>
            <th >Bill Qty</th>
            <th>UOM</th>
            <th >Rate</th>
            <th>Amount</th>
            <th>Remark</th>
        </tr>
        <?


         //$sql = "select id,order_source, job_id, order_id, buyer_id, item_id, color_type, wo_qty,uom, avg_rate,amount from  piece_rate_wo_dtls where mst_id='$data' and status_active=1 and is_deleted=0"; 
        
         $sql = "select a.id,a.bill_qty,a.avg_rate,a.amount,b.id as wo_dtls_id,b.job_id, b.po_id,b.buyer_id,b.item_id,b.color_type,b.wo_qty,b.uom, b.po_qty,b.style_ref, c.sys_number,a.remarks
              from piece_rate_bill_dtls a, piece_rate_wo_urmi_dtls b , piece_rate_wo_urmi_mst c
             where  a.mst_id = $data and a.wo_dtls_id=b.id and c.id=b.mst_id and a.status_active = 1 and b.status_active=1 and c.status_active=1
             "; 
        //echo $sql;
        $data_array=sql_select($sql);
        $po_id_arr[1][0]=0;$po_id_arr[2][0]=0;
        foreach ($data_array as $row)
        { 
            $po_id_arr[$row[csf("po_id")]]=$row[csf("po_id")];
            $po_id_string.=$row[csf('po_id')].",";
        }
        
         $po_id_string=chop( $po_id_string,",");
        
        $order_sql="select id, po_number,job_no_mst, 1 as order_source from wo_po_break_down where id in(".implode(',',$po_id_arr[1]).") and status_active=1 and is_deleted=0";
        $order_sql_result_arr=sql_select($order_sql);
        foreach ($order_sql_result_arr as $row)
        { 
            $jobOrderdataArr['po'][$row[csf('id')]]=$row[csf('po_number')];
            $jobOrderdataArr['job'][$row[csf('id')]]=$row[csf('job_no_mst')];
        }
        
        $sql="select a.id,a.job_no,a.job_no_prefix_num,a.buyer_name,a.style_ref_no,b.gmts_item_id,c.id as po_id,c.po_number,c.po_quantity,a.client_id from wo_po_details_master a,wo_po_details_mas_set_details b,wo_po_break_down c where a.job_no=b.job_no and a.job_no=c.job_no_mst and c.id in(".$po_id_string.")  and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
   // echo $sql;die;
        $sql_result = sql_select($sql);
        $po_wise_data=array();
        foreach ($sql_result as $row) {
            $po_wise_data[$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
            $po_wise_data[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];
            
        }
        
        
        
        
        $sl=1;
        foreach ($data_array as $row)
        { 
        
            
        
        
        ?>
       <tr>
            <td align="center"><? echo $sl;?></td>
            <td><? echo $row[csf("sys_number")];?></td>
            <td><? echo $buyer_arr[$row[csf("buyer_id")]];?></td>
            <td><? echo $row[csf("style_ref")];?></td>
            <td><? echo $po_wise_data[$row[csf('po_id')]]['job_no'];?></td>
            <td><? echo $garments_item[$row[csf("item_id")]];?></td>
            <td><? echo $po_wise_data[$row[csf('po_id')]]['po_number'];?></td>
            <td><? echo $color_type[$row[csf("color_type")]];?></td>
            <td align="right"><? echo number_format($row[csf("wo_qty")],2);//$order_qty; ?></td>
            <td align="right"><? echo number_format($row[csf("bill_qty")],2); $tot_wo_qty+=$row[csf("bill_qty")];?></td>
            <td align="center"><? echo $unit_of_measurement[$row[csf("uom")]];?></td>
            <td align="right"><? echo number_format($row[csf("avg_rate")],2);?></td>
            <td align="right"><? echo number_format($row[csf("amount")],2); $tot_amount+=$row[csf("amount")];?></td>
            <td align="left"><? echo $row[csf("remarks")];?></td>
        </tr>
        <? 
        $sl++;  
        }
        ?>
        <tr>
            <th colspan="9" align="right">Total : </th>
            <th align="right"><? echo number_format($tot_wo_qty,2);?></th>
            <th></th>
            <th></th>
            <th align="right"><? echo number_format($tot_amount,2);?></th>
        </tr>
        <tr>
            <th colspan="12" align="right">Upcharge</th>
            <th align="right"><? echo number_format($upcharge,2);?></th>
        </tr>
        <tr>
            <th colspan="12" align="right">Discount</th>
            <th align="right"><? echo number_format($discount,2);?></th>
        </tr>
        <tr>
            <th colspan="12" align="right">Grand Total</th>
            <th align="right"><? echo number_format($grand_total,2);?></th>
        </tr>
        
    </table>
    <table  width="700">
        <tr>
            <td >In Words: <?
                $cur=$currency[$currency];
                if($currency==1){ $paysa_sent="Paisa"; } else if($currency==2){ $paysa_sent="CENTS"; }
              echo number_to_words(number_format($grand_total,2,'.',''),$cur,$paysa_sent); 
             ?></td>
        </tr>
    </table>

    <table width="700">  
       <? echo signature_table(431, $company_id, "700px"); ?>
    </table>
    <br>
    <div style=" width:700px;">
            
    </div>

    <?

exit();
}





if ($action=="save_update_delete")
{
    $process = array( &$_POST );
    //print_r($process);die;
    extract(check_magic_quote_gpc( $process )); 
    
    $rate_source =  str_replace("'","",$qty_source);
    if ($operation==0)  // Insert Here
    { 
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }
        
        
        $flag=1;
        if(str_replace("'","",$update_id)=="")
        {
            if($db_type==0) $year_cond="YEAR(insert_date)"; 
            else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
            else $year_cond="";//defined Later
            
            $id = return_next_id_by_sequence("piece_rate_bill_mst_seq", "piece_rate_bill_mst", $con);

        
            
            // master part--------------------------------------------------------------;
            $price_rate_bill_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'PRBO', date("Y",time()), 5, "select sys_number_prefix, sys_number_prefix_num from piece_rate_bill_mst where company_id=$cbo_company_id and $year_cond=".date('Y',time())." order by id desc", "sys_number_prefix", "sys_number_prefix_num" ));
            
            
            $field_array_mst="id,sys_number_prefix,sys_number_prefix_num,sys_number,company_id,working_company_id,source,bill_date,currency,exchange_rate,location,remarks,upcharge,discount,grand_total,rate_bill_var,inserted_by,insert_date,status_active,is_deleted";
            


            $data_array_mst="(".$id.",'".$price_rate_bill_system_id[1]."',".$price_rate_bill_system_id[2].",'".$price_rate_bill_system_id[0]."',".$cbo_company_id.",".$cbo_working_company.",".$cbo_source.",".$txt_bill_date.",".$cbo_currency.",".$txt_exchange_rate.",".$cbo_location.",".$txt_remarks_mst.",".$upcharge.",".$discount.",".$grand_total.",".$rate_source.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
            
            // details part--------------------------------------------------------------;

            $field_array_dtls="id, mst_id, wo_dtls_id,bill_qty, avg_rate,rate_history,line_id,prod_reso_allo,amount, remarks, inserted_by, insert_date,status_active,is_deleted,type";
            
            $id_dtls = return_next_id_by_sequence("piece_rate_bill_dtls_seq", "piece_rate_bill_dtls", $con);
            
            if ($rate_source ==2)  //PO WISE
			{
				
				$field_array_rate_dtls="id,mst_id,dtls_id,lib_sew_op_id,rate,entry_form,inserted_by,insert_date,status_active,is_deleted";
			}
            $tot_rows= str_replace("'","",$tot_rows);
            
            for($i=1; $i<=$tot_rows; $i++)
            {
                
                
                $txtwodtlsid='txtwodtlsid_'.$i;
                $txttype = 'txttype_'.$i;
                
                $txtbillqty='txtbillqty_'.$i;
               
                $txtavgrate='txtavgrate_'.$i;
                $txtremarks='txtremarks_'.$i;
            
                $txtdtlamount='txtdtlamount_'.$i;
                $rate_history='txtRate_'.$i;
                $line_id='sewingLineId_'.$i;
                $prod_reso_allo='prodResoAllo_'.$i;
                
                if ($rate_source == 2)  //PO WISE
				{
					$rate_history_arr = array();
					$rate_history_arr = explode('@@',$$rate_history);  
					$kk =0;
					foreach ($rate_history_arr as $data) 
					{
						$rate_dtls_id = return_next_id_by_sequence("piece_rate_wo_rate_dtls_seq", "piece_rate_wo_rate_dtls", $con);
						$pair = explode ('#', $data);
						$lib_sew_op_id = str_replace("'","",$pair[0]);
						$rate	= str_replace("'","",$pair[1]);
                        if (!$rate) {
                            $rate = 0;
                        }
						// echo "10**".$rate; die;
						if($kk==0 && $i==1)
						{
							$data_array_rate_dtls="(".$rate_dtls_id.",".$id.",".$id_dtls.",'".$lib_sew_op_id."','".$rate."',693,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
						}
						else
						{
							$data_array_rate_dtls.=",(".$rate_dtls_id.",".$id.",".$id_dtls.",'".$lib_sew_op_id."','".$rate."',693,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
						}
						$kk++;
					}
					

				}
                if(str_replace("'",'',$$txtbillqty)!=""){
                    if($i>1)
                    {
                        $data_array_dtls.=",";
                    }
                    $data_array_dtls.="(".$id_dtls.",".$id.",".$$txtwodtlsid.",".$$txtbillqty.",".$$txtavgrate.",".$$rate_history.",".$$line_id.",".$$prod_reso_allo.",".$$txtdtlamount.",".$$txtremarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0',".$$txttype.")";
                    $id_dtls++;
                }

                
            }

        }
        
        //    echo "10**".$data_array_dtls; die;

        $rID1=sql_insert("piece_rate_bill_mst",$field_array_mst,$data_array_mst,0);
        

        $rID2=sql_insert("piece_rate_bill_dtls",$field_array_dtls,$data_array_dtls,0);
        $rID3 = true;
        if ($rate_source ==2) //PO WISE
		{
			$rID3=sql_insert("piece_rate_wo_rate_dtls",$field_array_rate_dtls,$data_array_rate_dtls,0);
		}
        
        // echo "10**insert into piece_rate_bill_dtls (".$field_array_dtls.") values ".$data_array_dtls;

       
        
        
        // echo "10** ".$rID1."**".$rID2."**".$rID3;print_r($data_array_wo_dtls); die;
        
        if($db_type==0)
        {
            if($rID1 && $rID2)
            {
                mysql_query("COMMIT");  
                echo "0**".$id."**".$price_rate_bill_system_id[0]."**".str_replace("'", "", $upcharge)."**".str_replace("'", "", $discount);
            }
            else
            {
                mysql_query("ROLLBACK"); 
                echo "10**".$rID1."**".$rID2;
                 //echo "10** insert into piece_rate_wo_urmi_dtls($field_array_dtls)values".$data_array_dtls;die;
            }
        }
        else if($db_type==2 || $db_type==1 )
        {
            if($rID1 && $rID2 && $rID3)
            {
                oci_commit($con);  
                echo "0**".$id."**".$price_rate_bill_system_id[0]."**".str_replace("'", "", $upcharge)."**".str_replace("'", "", $discount)."**".$$txttype;
            }
            else
            {
                oci_rollback($con);
                echo "10**".$rID1."**".$rID2;
                 //echo "10** insert into piece_rate_wo_urmi_dtls($field_array_dtls)values".$data_array_dtls;die;
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
        
        $flag=1;

        $field_array_mst="company_id*working_company_id*source*bill_date*manual_bill*currency*exchange_rate*location*remarks*upcharge*discount*grand_total*rate_bill_var*updated_by*update_date";
        //$data_array_mst="".$cbo_company_id."*".$cbo_working_company."*".$cbo_source."*".$txt_bill_date."*".$txt_manual_bill."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_location."*".$txt_remarks_mst."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

        $data_array_mst_up[str_replace("'","",$update_id)] =explode("*",("".$cbo_company_id."*".$cbo_working_company."*".$cbo_source."*".$txt_bill_date."*".$txt_manual_bill."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_location."*".$txt_remarks_mst."*".$upcharge."*".$discount."*".$grand_total."*".$rate_source."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
        
        //-----------------------------------------------------         
        $field_array_dtls_up="wo_dtls_id*bill_qty*avg_rate*rate_history*amount*remarks*updated_by*update_date";
        $field_array_dtls="id, mst_id, wo_dtls_id,bill_qty, avg_rate,rate_history,amount, remarks, inserted_by, insert_date,status_active,is_deleted";
        $id_dtls = return_next_id_by_sequence("piece_rate_bill_dtls_seq", "piece_rate_bill_dtls", $con);

        if ($rate_source ==2)  //PO WISE
        {
            
            $field_array_rate_dtls="id,mst_id,dtls_id,lib_sew_op_id,rate,entry_form,inserted_by,insert_date,status_active,is_deleted";
        } 
        
        $tot_rows=str_replace("'","",$tot_rows);
        $f=1;$df=1;

        for($i=1; $i<=$tot_rows; $i++)
        {

            
            $txtwodtlsid='txtwodtlsid_'.$i;
                
            $txtbillqty='txtbillqty_'.$i;
           
            $txtavgrate='txtavgrate_'.$i;
            $txtremarks='txtremarks_'.$i;
            $txtdtlamount='txtdtlamount_'.$i;
            $details_update_id='detailsUpdateId_'.$i;
            $rate_history='txtRate_'.$i;
            $txttype = 'txttype_'.$i;

            
            $txtbillqty=str_replace("'","",$$txtbillqty);
            $txtavgrate=str_replace("'","",$$txtavgrate);
            $txtremarks=str_replace("'","",$$txtremarks);
            
            $txtdtlamount=str_replace("'","",$$txtdtlamount);
            $txtwodtlsid=str_replace("'","",$$txtwodtlsid);
            $rate_history=str_replace("'","",$$rate_history);
           
            
    
            if(str_replace("'","",$$details_update_id)!="")
            {
                 //this is for update dels
                $all_dtls_id[]=str_replace("'","",$$details_update_id);
                $update_dtls_id[]=str_replace("'","",$$details_update_id);
                $dtls_id = $$details_update_id;
                $data_array_dtls_up[str_replace("'","",$$details_update_id)] =explode("*",("'".$txtwodtlsid."'*'".$txtbillqty."'*'".$txtavgrate."'*'".$rate_history."'*'".$txtdtlamount."'*'".$txtremarks."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
            }
            else
            {
               //this is for news insert dels   
                if($txtwoqty!="")
                { 
                    if($data_array_dtls=='')
                    {
                        $data_array_dtls.=",";
                    }
                    $all_dtls_id[]=$id_dtls;
                    $dtls_id = $id_dtls;
                    $data_array_dtls.="('".$id_dtls."','".$update_id."','".$txtwodtlsid."','".$txtavgrate."','".$rate_history."','".$txtdtlamount."','".$txtremarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
                        
                    $f++;
                }
            }

            if ($rate_source == 2)  //PO WISE
            {
                $rate_history_arr = array();
                $rate_history_arr = explode('@@',$rate_history);  
                $kk =0;
                foreach ($rate_history_arr as $data) 
                {
                    $rate_dtls_id = return_next_id_by_sequence("piece_rate_wo_rate_dtls_seq", "piece_rate_wo_rate_dtls", $con);
                    $pair = explode ('#', $data);
                    $lib_sew_op_id = str_replace("'","",$pair[0]);
                    $rate	= str_replace("'","",$pair[1]);
                    if (!$rate) {
                        $rate = 0;
                    }
                    // echo "10**".$rate; die;
                    if($kk==0 && $i==1)
                    {
                        $data_array_rate_dtls="(".$rate_dtls_id.",".$update_id.",".$dtls_id.",'".$lib_sew_op_id."','".$rate."',693,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
                    }
                    else
                    {
                        $data_array_rate_dtls.=",(".$rate_dtls_id.",".$update_id.",".$dtls_id.",'".$lib_sew_op_id."','".$rate."',693,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
                    }
                    $kk++;
                }
                

            }

        }
            
        $update_mst_id[]=str_replace("'","",$update_id);
        //echo "10**".bulk_update_sql_statement("piece_rate_bill_dtls", "id",$field_array_dtls_up, $data_array_dtls_up, $update_dtls_id ); die;
        //$rID1=sql_update("piece_rate_bill_mst",$field_array_mst,$data_array_mst,"id","".str_replace("'", "", $update_id)."",1);
        $rID1=execute_query(bulk_update_sql_statement("piece_rate_bill_mst", "id",$field_array_mst,$data_array_mst_up,$update_mst_id ));

        
        $rID2=execute_query(bulk_update_sql_statement("piece_rate_bill_dtls", "id",$field_array_dtls_up,$data_array_dtls_up,$update_dtls_id ));

        //echo "10**".sql_update("piece_rate_bill_mst",$field_array_mst,$data_array_mst,"id","".$update_id.""); die;

        $rID2_insert=true;
        if($data_array_dtls!=''){$rID2_insert=sql_insert("piece_rate_bill_dtls",$field_array_dtls,$data_array_dtls,0);}
        
        $delete1 = execute_query("update piece_rate_bill_dtls set is_deleted=1,status_active=0,updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where mst_id=$update_id and id not in(".implode(',',$all_dtls_id).")", 0);

        if ($rate_source == 2) //PO WISE
        {
            $rateDelete = execute_query("DELETE from piece_rate_wo_rate_dtls where mst_id=$update_id",1);
            if($rateDelete) $flag=1; else $flag=0; 
            

            if($flag==1) 
            {
                $rID3=sql_insert("piece_rate_wo_rate_dtls",$field_array_rate_dtls,$data_array_rate_dtls,0);
                if($rID3) $flag=1; else $flag=0;   
            }  
        }
        

        
        if($db_type==0)
        {
            if($rID1 && $rID2 && $rID2_insert && $delete1)
            {
                mysql_query("COMMIT");  
                //echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_id)."**0";
                echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_id)."**".str_replace("'", "", $upcharge)."**".str_replace("'", "", $discount);
            }
            else
            {
                mysql_query("ROLLBACK"); 
                echo "10**".$rID1."**".$rID2 ."**". $rID2_insert ."**". $delete1;
            }
        }
        else if($db_type==2 || $db_type==1 )
        {
            if($rID1 && $rID2 && $rID2_insert && $delete1 && $flag)
            {
                oci_commit($con);  
                // echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_id)."**0";
                 echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_id)."**".str_replace("'", "", $upcharge)."**".str_replace("'", "", $discount)."**".$$txttype;
            }
            else
            {
                oci_rollback($con);
                echo "10**".$rID1."**".$rID2 ."**". $rID2_insert ."**". $delete1;
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
       $delete1 = execute_query("update piece_rate_bill_mst set is_deleted=1,status_active=0,updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where id=$update_id ", 0);
       $delete2 = execute_query("update piece_rate_bill_dtls set is_deleted=1,status_active=0,updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where mst_id=$update_id ", 0);

        if($db_type==0)
        {
            if($delete1 && $delete2 )
            {
                mysql_query("COMMIT");  
                echo "2**0";
            }
            else
            {
                mysql_query("ROLLBACK"); 
                echo "10**".$delete1."**".$delete2 ;
            }
        }
        else if($db_type==2 || $db_type==1 )
        {
            if($delete1 && $delete2)
            {
                oci_commit($con);  
                echo "2**0";
            }
            else
            {
                oci_rollback($con);
                 echo "10**".$delete1."**".$delete2 ;
            }
        }
        disconnect($con);
        die;

    }
    
}









?>