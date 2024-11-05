<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create BTB or Margin LC Register Report
				
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	21-12-2013
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("BTB or Margin LC Register Report","../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var tableFilters = 
	{
		col_61: "none",
		col_operation: {
		id: ["value_tot_lc_value"],
		col: [11],
		operation: ["sum"],
		write_method: ["innerHTML"]
		}
	} 
    var tableFilters1 = 
	{
		col_61: "none",
		col_operation: {
		id: ["value_tot_lc_value_1","value_tot_rec_amt_1","value_tot_bal_amt_1","value_tot_tlc_qty_1","value_tot_rec_1","value_tot_bal_1","value_tot_ttl_acceptance_vlu_1","value_tot_ttl_paid_amount_1"],
		col: [12,13,14,15,16,17,19,21],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 
    var tableFilters2 = 
	{
		col_61: "none",
		col_operation: {
		id: ["value_tot_lc_value_2","value_tot_rec_amt_2","value_tot_bal_amt_2","value_tot_tlc_qty_2","value_tot_rec_2","value_tot_bal_2","value_tot_ttl_acceptance_vlu_2","value_tot_ttl_paid_amount_2"],
		col: [12,13,14,15,16,17,20,22],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 
    
     var tableFilters3 = 
	{
		col_61: "none",
		col_operation: {
		id: ["value_tot_lc_value_3","value_tot_rec_amt_3","value_tot_bal_amt_3","value_tot_tlc_qty_3","value_tot_rec_3","value_tot_bal_3","value_tot_ttl_acceptance_vlu_3","value_tot_ttl_paid_amount_3"],
		col: [12,13,14,15,16,17,20,22],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}  
    var tableFilters4 = 
	{
		col_61: "none",
		col_operation: {
		id: ["value_tot_lc_value_4","value_tot_rec_amt_4","value_tot_bal_amt_4","value_tot_tlc_qty_4","value_tot_rec_4","value_tot_bal_4","value_tot_ttl_acceptance_vlu_4","value_tot_ttl_paid_amount_4"],
		col: [12,13,14,15,16,17,19,21],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 
    var tableFilters5 = 
	{
		col_61: "none",
		col_operation: {
		id: ["value_tot_lc_value_5","value_tot_rec_amt_5","value_tot_bal_amt_5","value_tot_tlc_qty_5","value_tot_rec_5","value_tot_bal_5","value_tot_ttl_acceptance_vlu_5","value_tot_ttl_paid_amount_5"],
		col: [12,13,14,15,16,17,19,21],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 
    var tableFilters6 = 
	{
		col_61: "none",
		col_operation: {
		id: ["value_tot_lc_value_6","value_tot_rec_amt_6","value_tot_bal_amt_6","value_tot_tlc_qty_6","value_tot_rec_6","value_tot_bal_6","value_tot_ttl_acceptance_vlu_6","value_tot_ttl_paid_amount_6"],
		col: [12,13,14,15,16,17,19,21],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 

    var tableFilters7 = 
	{
		col_61: "none",
		col_operation: {
		id: ["value_tot_lc_value_7","value_tot_rec_amt_7","value_tot_bal_amt_7","value_tot_tlc_qty_7","value_tot_rec_7","value_tot_bal_7","value_tot_ttl_acceptance_vlu_7","value_tot_ttl_paid_amount_7"],
		col: [12,13,14,15,16,17,19,21],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}  
   
   

	function openmypage(company_name,lc_number,ship_date,supplier_id,lc_date,exp_date,payterm,pi_id,action,title)
	{
		var popup_width="";
		if(action=="pi_details") popup_width="900px"; else popup_width="850px";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/btb_or_margin_lc_register_report_controller.php?company_name='+company_name+'&lc_number='+lc_number+'&ship_date='+ship_date+'&supplier_id='+supplier_id+'&lc_date='+lc_date+'&exp_date='+exp_date+'&payterm='+payterm+'&pi_id='+pi_id+'&action='+action, title, 'width='+popup_width+',height=390px,center=1,resize=0,scrolling=0','../');
	}	
	
	function generate_report(operation)
	{
        var company_id=$("#cbo_company_id").val();
		// if(form_validation('cbo_company_id','Company Name')==false)
        if(operation==8){
            if(form_validation('cbo_company_id*txt_file_no','Company*File No')==false){
              return;
            }
        }
        if(operation==9){
            if(form_validation('txt_date_from*txt_date_to','Date From*Date To')==false){
              return;
            }
        }
		if(company_id=='')
		{
            $("#cbo_company_id").focus();
			return;
		}
		else
		{	
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_issue_banking*cbo_item_category_id*cbo_lc_type_id*cbo_supply_source*cbo_bonded_warehouse*txt_date_from*txt_date_to*cbo_based_on*txt_lc_no*cbo_payterm_id*cbo_status*cbo_reference_close*cbo_search_by*txt_search_common*cbo_supplier*txt_file_no',"../../")+'&report_title='+report_title+'&report_type='+operation;
            // alert(data);return;

			freeze_window(3);
			http.open("POST","requires/btb_or_margin_lc_register_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			// var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
            //alert(reponse[0]);return;
			//document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
            document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            if(reponse[2]==3){
               var currId_arr=reponse[3].split(",");

               currId_arr.forEach(function(currId) {
				   //alert(currId); 
                   if(currId==1){
                    setFilterGrid("tbl_marginlc_list_1",-1,tableFilters1);
                   }
                   if(currId==2){
                    setFilterGrid("tbl_marginlc_list_2",-1,tableFilters2);
                   }
                   if(currId==3){
                    setFilterGrid("tbl_marginlc_list_3",-1,tableFilters3);
                   }
                   if(currId==4){
                    setFilterGrid("tbl_marginlc_list_4",-1,tableFilters4);
                   }
                   if(currId==5){
                    setFilterGrid("tbl_marginlc_list_5",-1,tableFilters5);
                   }
                   if(currId==6){
                    setFilterGrid("tbl_marginlc_list_6",-1,tableFilters6);
                   }
                   if(currId==7){
                    setFilterGrid("tbl_marginlc_list_7",-1,tableFilters7);
                   }
                 
                });

            }else{
				//alert(9);
				setFilterGrid("tbl_marginlc_list",-1);
            }
	 		show_msg('3');
			release_freezing();
		}
	}

    function openmypage_lc_popup(report_type,company_id,all_btb,title,to_date,from_date)
    {
        if(report_type==1){
            var width ='700px';
        }
        if(report_type==2){
            var width ='900px';
        }
        if(report_type==3){
            var width ='900px';
        }
        if(report_type==4){
            var width ='900px';
        }
        if(report_type==5){
            var width ='900px';
        }
        if(report_type==6){
            var width ='700px';
        }
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/btb_or_margin_lc_register_report_controller.php?action=btb_lc_details&report_type='+report_type+'&company_id='+company_id+'&all_btb='+all_btb+'&title='+title+'&to_date='+to_date+'&from_date='+from_date, title, 'width='+width+',height=350px,center=1,resize=0,scrolling=0','../');
        emailwindow.onclose=function()
        {
        }
    } 
	
	

    function new_window()
    {
        document.getElementById('scroll_body2').style.overflow="auto";
        document.getElementById('scroll_body2').style.maxHeight="none"; 
        $("#tbl_marginlc_list tr:first").hide();
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
        '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
        d.close();         
        document.getElementById('scroll_body2').style.overflowY="auto"; 
        document.getElementById('scroll_body2').style.maxHeight="400px";
        $("#tbl_marginlc_list tr:first").show();
    }
	
	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
		}
	}
	
	function lc_details_popup(lc_no,action,title,int_file)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/btb_or_margin_lc_register_report_controller.php?lc_no='+lc_no+'&action='+action+'&int_file='+int_file, title, 'width=620px,height=390px,center=1,resize=0,scrolling=0','../');
	}
	
	function openmypagRcvDetails(pi_id,itm_cate_arr,rcv_basis,company_name,item_group_ids)
	{   
        // var company_name = $("#cbo_company_id").val();
		//alert(item_group_ids);return;
		var title = "MRR Details";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/btb_or_margin_lc_register_report_controller.php?company_name='+company_name+'&pi_ids='+pi_id+'&rcv_basis='+rcv_basis+'&item_cate_array='+itm_cate_arr+'&item_group_ids='+item_group_ids+'&action=receive_return_details', title, 'width=850px,height=250px,center=1,resize=0,scrolling=0','../');
	}

    function txt_search_function(str)
    {
        if (str==1) 
        {
            var search_by_td = $("#search_by_td_up").html('Export LC No'); 
        }
        else
        {
            var search_by_td = $("#search_by_td_up").html('SC No'); 
        }
        
        $("#txt_search_common").val('');        
    }

    function openmypage_file(pi_id, btb_sys_id, acceptance_invoice_id,btb_id)
    {
        var page_link='requires/btb_or_margin_lc_register_report_controller.php?action=show_file&pi_id='+pi_id+'&btb_sys_id='+btb_sys_id+'&acceptance_invoice_id='+acceptance_invoice_id+'&btb_id='+btb_id;
        var title="File View";
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=450px,center=1,resize=0,scrolling=0','../');
    }
    function openmypage_file_btb(btb_sys_id,btb_id)

    {
        var page_link='requires/btb_or_margin_lc_register_report_controller.php?action=show_file_btb&btb_sys_id='+btb_sys_id+'&btb_id='+btb_id;
        var title="File View";
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=450px,center=1,resize=0,scrolling=0','../');
    }
    function fnc_load_report_format()
    {
        var data=$('#cbo_company_id').val();
        var report_ids = return_global_ajax_value( data, 'load_report_format', '', 'requires/btb_or_margin_lc_register_report_controller');
        print_report_button_setting(report_ids);
    }


    function print_report_button_setting(report_ids)
    {
        if(trim(report_ids)=="")
        {
            $("#search1").show();
            $("#search2").show();
            $("#search3").show();
            $("#search4").show();
            $("#search5").show();
            $("#search6").show();
            $("#search7").show();
         }
        else
        {
            var report_id=report_ids.split(",");
            $("#search1").hide();
            $("#search2").hide();
            $("#search3").hide();
            $("#search4").hide();
            $("#search5").hide();
            $("#search6").hide();
            $("#search7").hide();
             for (var k=0; k<report_id.length; k++)
            {
               
                if(report_id[k]==108)
                {
                    $("#search1").show(); //Show
                }
                if(report_id[k]==261)  //Details
                {
                    $("#search2").show();
                }
                if(report_id[k]==877) // LC Status
                {
                    $("#search3").show(); 
                }
                if(report_id[k]==878) //WVN
                {
                    $("#search4").show();
                }
                if(report_id[k]==879) //Import Register
                {
                    $("#search5").show();
                }
                if(report_id[k]==195)  //Show 2
                {
                    $("#search6").show();
                }
                if(report_id[k]==242) // Show 3
                {
                    $("#search7").show();
                }

            }
        }


    }
</script>
</head>
<body onLoad="set_hotkey();fnc_load_report_format()">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>   		 
        <form name="marginlcregister_1" id="marginlcregister_1" autocomplete="off" > 
         <h3 style="width:1750px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:1740px" >      
            <fieldset>  
                <table class="rpt_table" width="1740" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <th width="120" class="must_entry_caption">Company</th>
                        <th width="120">Issue Banking</th>
                        <th width="120">Item Category</th>
                        <th width="80">LC Type</th>
                        <th width="80">Pay Term</th>
                        <th width="120">Supply Source</th>
                        <th width="80">Bonded Warehouse</th>
                        <th width="120">Supplier</th>
                        <th width="80">File No</th>
                        <th width="80">Based On</th>
                        <th>Date Range</th>
                        <th width="90">LC No.</th>
                        <th width="120">BTB LC Status</th>
                        <th width="90">Reference Close</th>
                        <th width="80">Search By</th>
                        <th width="80" id="search_by_td_up"><? echo "Export LC No"; ?></th>
                        <th width="70"><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_form('marginlcregister_1','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td align="center">
                                <? 
                                    echo create_drop_down( "cbo_company_id", 120, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and comp.core_business in(1,3) $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/btb_or_margin_lc_register_report_controller',this.value, 'load_drop_down_supplier', 'supplier_td' );" );
                                ?>                            
                           </td>
                            <td align="center">
                                <? 
                                    if ($db_type==0)
                                    {
                                        echo create_drop_down( "cbo_issue_banking", 120, "select id,concat(a.bank_name,' (', a.branch_name,')') as bank_name from  lib_bank where issusing_bank=1 and status_active=1 and is_deleted=0  order by bank_name","id,bank_name", 1, "--Select Bank--", $selected, "" );
                                    }
                                    else
                                    {
                                        echo create_drop_down( "cbo_issue_banking", 120, "select id,(bank_name || ' (' || branch_name || ')' ) as bank_name from  lib_bank where issusing_bank=1 and status_active=1 and is_deleted=0  order by bank_name","id,bank_name", 1, "--Select Bank--", $selected, "" );
                                    }
                                ?>                            
                           </td>
                           <td align="center">
								<?
									echo create_drop_down( "cbo_item_category_id", 120,$item_category,"", 1, "-- Select Category--", $selected, "","","","","","");
                                ?> 
                          </td>
                            <td align="center">
                            	<?
                            		echo create_drop_down( "cbo_lc_type_id",80,$lc_type,'',1,'--Select--',0,"",0); 
                            	?>  
                                <!--                            	<input style="width:120px;" name="txt_lc_type" id="txt_lc_type" class="text_boxes" onDblClick="openmypage_lc_type()" placeholder="Browse" readonly />
                                <input type="text" name="txt_lc_type_id" id="txt_lc_type_id" style="width:90px;"/>
                                -->
                             </td>
							<td align="center">
                                <? 
                                    echo create_drop_down( "cbo_payterm_id",80,$pay_term,'',1,'-Select-',0,"",0,'');
                                ?>
                           </td>
							<td align="center">
                                <? 
                                    echo create_drop_down( "cbo_supply_source", 120, $supply_source,"", 1, "--Select source--", "", "" );
                                ?>
                           </td>

                           <td align="center">
                                <? 
                                    echo create_drop_down( "cbo_bonded_warehouse", 80, $yes_no,"", 1, "--Select Warehouse--", "", "" );
                                ?>
                           </td>
                           <td align="center" id="supplier_td">
                                <? 
                                    echo create_drop_down( "cbo_supplier", 120, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, "--Select Supplier--", $selected, "" );
                                ?>                            
                           </td>
                           <td align="center">
                                <input type="text" style="width:80px" class="text_boxes" name="txt_file_no" id="txt_file_no"/>
                            </td>
                           <td align="center">
                                <? 
									$based_on=array(1=>"Lc Date",2=>"Insert Date");
                                    echo create_drop_down( "cbo_based_on", 80, $based_on,"", 0, "", "", "" );
                                ?>
                           </td>                         
                            <td align="center">
                                <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px;"/>                    							
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px;"/>                        
                            </td>
                            <td align="center"><input type="text" name="txt_lc_no" id="txt_lc_no" class="text_boxes" style="width:80px;"/></td>
                            <td align="center">
                                <? 
                                    //$btb_lc_status=array(1=>"Active",2=>"In-active",3=>"Cancelled");
                                    echo create_drop_down( "cbo_status", 120, $row_status,"", 0, "", "", "" );
                                ?>
                           </td>
                           <td align="center">
                                <? 
                                    //$reference_close=array(1=>"Yes",2=>"No");         
                                    echo create_drop_down( "cbo_reference_close", 90, $yes_no, "", 1, "--Select--", "", "" );
                                ?>
                           </td>

                            <td align="center">
                                <? 
                                $search_by_arr = array(1 => "Export LC No", 2 => "SC No");
                                //$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
                                $selected = 1;
                                echo create_drop_down("cbo_search_by", 80, $search_by_arr, "", 0, "--Select--", $selected, "txt_search_function(this.value);", 0); ?>
                            </td>
                            
                            <td align="center">
                                <input type="text" style="width:80px" class="text_boxes" name="txt_search_common" id="txt_search_common"/>
                            </td>
                            <td>
                                <input type="button" name="search1" id="search1" value="Show" onClick="generate_report(3)" style="width:60px" class="formbutton" />
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="10" align="center"><? echo load_month_buttons(1);  ?></td>
                            <td align="center" colspan="7">
                                <input type="button" name="search2" id="search2" value="Details" onClick="generate_report(4)" style="width:60px" class="formbutton" />
                                <input type="button" name="search3" id="search3" value="LC Status" onClick="generate_report(5)" style="width:60px" class="formbutton" />
                                <input type="button" name="search4" id="search4" value="WVN" onClick="generate_report(6)" style="width:60px" class="formbutton" />
                                <input type="button" name="search5" id="search5" value="Import Register" onClick="generate_report(7)" style="width:90px" class="formbutton" />
                                <input type="button" name="search6" id="search6" value="Show 2" onClick="generate_report(8)" style="width:60px" class="formbutton" />
                                <input type="button" name="search7" id="search7" value="Show 3" onClick="generate_report(9)" style="width:60px" class="formbutton" />
                            </td>
                        </tr>
                    </tfoot>
                </table> 
            </fieldset>
        </div>
    <div style="margin-top:10px" id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form>  
 </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>set_multiselect('cbo_company_id','0','0','','0',"load_drop_down( 'requires/btb_or_margin_lc_register_report_controller',$('#cbo_company_id').val(), 'load_drop_down_supplier', 'supplier_td' );fnc_load_report_format();");</script>

<?
    $sql=sql_select("select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name");
    $company_id='';
    $is_single_select=0;
    if(count($sql)==1){
        $company_id=$sql[0][csf('id')];
        $is_single_select=1;
        ?>
        <script>
        set_multiselect('cbo_company_id','0','<? echo $is_single_select?>','<? echo $company_id?>','0');
        </script>      
        <?
    }	
?>
</html>
