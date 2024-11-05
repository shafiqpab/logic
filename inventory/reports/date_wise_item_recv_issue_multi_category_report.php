<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Date Wise Item Receive Issue Report

Functionality	:
JS Functions	:
Created by		:	Tofael
Creation date 	:
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
echo load_html_head_contents("Date Wise  Receive Issue","../../", 1, 1, $unicode,1,1);
//echo load_html_head_contents("Purchase Recap Report", "../../", 1, 1,'',1,'');
?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";


	var tableFilters6 = 
	{
        col_45: "none",
        col_operation: {
        id: ["value_total_receive","value_total_issue_return","value_total_trans_in","value_total_issue_bulkSewing","value_total_issue_sampleWithOrder","value_total_issue_sampleWithOutOrder","value_total_trans_out","value_total_rcv_return","value_total_issue_rePocess","value_total_issue_sales","value_total_issue_fabricTest","value_total_issue_scrapStore","value_total_issue_damage","value_total_issue_adjustment","value_total_issue_stolen","value_total_actual_amt","value_total_amounts"],
        //col: [25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,41],
        col: [25,27,28,29,30,31,32,33,34,35,36,37,38,39,40,44,46],
        operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
        write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]           
		}
	}

    var tableFilters10 = 
	{
        col_45: "none",
        col_operation: {
        id: ["value_total_receive","value_total_issue_return","value_total_trans_in","value_total_issue_bulkSewing","value_total_issue_sampleWithOrder","value_total_issue_sampleWithOutOrder","value_total_trans_out","value_total_rcv_return","value_total_issue_rePocess","value_total_issue_sales","value_total_issue_fabricTest","value_total_issue_scrapStore","value_total_issue_damage","value_total_issue_adjustment","value_total_issue_stolen","value_total_amounts"],
        //col: [25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,41],
        col: [26,28,29,30,31,32,33,34,35,36,37,38,39,40,41,43],
        operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
        write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]       
		}
	}

	function reset_field()
	{
		reset_form('item_receive_issue_1','report_container2','','','','');
	}


	function  generate_report(rptType)
	{
		var cbo_item_cat = $("#cbo_item_cat").val();
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_buyer_name = $("#cbo_buyer_name").val();
		var cbo_store_name = $("#cbo_store_name").val();
		var txt_date_from = $("#txt_date_from").val();
		var txt_date_to = $("#txt_date_to").val();
		var cbo_yarn_count = $("#cbo_yarn_count").val();
		var cbo_based_on = $("#cbo_based_on").val();
	    var txt_mrr_no = $("#txt_mrr_no").val();

	    if(txt_mrr_no!="") 
		{
			if( form_validation('cbo_company_name*txt_item_process_name','Company Name*Item Category')==false )
			{
				return;
			} 	
		}
		else 
		{
			if( form_validation('cbo_company_name*txt_item_process_name*txt_date_from*txt_date_to','Company Name*Item Category*Date From*Date To')==false )
			{
				return;
			}
		}

		var dataString = "&cbo_item_cat="+cbo_item_cat+"&cbo_company_name="+cbo_company_name+"&cbo_buyer_name="+cbo_buyer_name+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&cbo_yarn_count="+cbo_yarn_count+"&cbo_based_on="+cbo_based_on+"&rptType="+rptType+"&cbo_store_name="+cbo_store_name+"&txt_mrr_no="+txt_mrr_no;
		var data="action=generate_report"+dataString;
		// alert(data);return;
		freeze_window(5);
		http.open("POST","requires/date_wise_item_recv_issue_multi_category_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}
    

    function generate_report_reponse()
    {
        if(http.readyState == 4) 
        {
            var reponse=trim(http.responseText).split("####");

            $("#report_container2").html(reponse[0]);
            document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none;"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            release_freezing();
            if ($("#table_body_1").length){}
            var ItemCategoryArr  = reponse[2].split(',');

            if(ItemCategoryArr.indexOf("2") > -1)
            {
                ItemCategoryArr.splice(ItemCategoryArr.indexOf("2"), 1);

                if(reponse[3]==1)
                {
                    var tableFilters =
                        {
                            col_30: "none",
                            col_operation: {
                                id: ["value_total_receive_f","value_total_ret_receive_f","value_total_issue_f","value_total_ret_issue_f"],
                                //col: [22,23,24,25],
                                col: [23,24,25,26],
                                operation: ["sum","sum","sum","sum"],
                                write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
                            }
                        }
                }
                if(reponse[3]==2)
                {
                    var tableFilters =
                        {
                            col_30: "none",
                            col_operation: {
                                id: ["value_total_receive_f","value_total_ret_receive_f","value_total_issue_f","value_total_ret_issue_f"],
                               // col: [22,23,24,25],
                                col: [23,24,25,26],
                                operation: ["sum","sum","sum","sum"],
                                write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
                            }
                        }
                }
                if(reponse[3]==3)
                {
                    var tableFilters =
                        {
                            col_30: "none",
                            col_operation: {
                                id: ["value_total_receive_f","value_total_ret_receive_f","value_total_issue_f","value_total_ret_issue_f"],
                                //col: [24,25,26,27],
                                col: [25,26,27,28],
                                operation: ["sum","sum","sum","sum"],
                                write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
                            }
                        }
                }

                setFilterGrid("table_body_2",-1,tableFilters);
                setFilterGrid("table_body2",-1);
            }

            if(reponse[3]==2 && ItemCategoryArr.indexOf("3") > -1)
            {
                ItemCategoryArr.splice(ItemCategoryArr.indexOf("3"), 1);
                setFilterGrid("table_body_3",-1,tableFilters6);
                setFilterGrid("table_body23",-1);
            }
            
            if(reponse[3]==3 && ItemCategoryArr.indexOf("3") > -1)
            {
                ItemCategoryArr.splice(ItemCategoryArr.indexOf("3"), 1);
                setFilterGrid("table_body_3",-1,tableFilters10);
                setFilterGrid("table_body23",-1);
            }

            if(ItemCategoryArr.indexOf("13") > -1)
            {
                ItemCategoryArr.splice(ItemCategoryArr.indexOf("13"), 1);
                if(reponse[3]==1)
                {
                    var tableFilters2 =
                        {
                            col_30: "none",
                            col_operation: {
                                id: ["value_total_receive_g","value_total_ret_receive_g","value_total_trans_in_g","value_total_issue_g","value_total_ret_issue_g","value_total_trans_out_g"],
                                col: [24,25,26,27,28,29],
                                operation: ["sum","sum","sum","sum","sum","sum"],
                                write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
                            }
                        }
                }
                if(reponse[3]==2)
                {
                    var tableFilters2 =
                        {
                            col_30: "none",
                            col_operation: {
                                id: ["value_total_receive_g","value_total_ret_receive_g","value_total_trans_in_g","value_total_issue_g","value_total_ret_issue_g","value_total_trans_out_g"],
                                col: [24,25,26,27,28,29],
                                operation: ["sum","sum","sum","sum","sum","sum"],
                                write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
                            }
                        }
                }
                if(reponse[3]==3)
                {
                    var tableFilters2 =
                        {
                            col_30: "none",
                            col_operation: {
                                id: ["value_total_receive_g","value_total_ret_receive_g","value_total_trans_in_g","value_total_issue_g","value_total_ret_issue_g","value_total_trans_out_g"],
                                col: [26,27,28,29,30,31],
                                operation: ["sum","sum","sum","sum","sum","sum"],
                                write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
                            }
                        }
                }
                setFilterGrid("table_body_13",-1,tableFilters2);
                setFilterGrid("table_body_213",-1);
            }

            if(ItemCategoryArr.indexOf("4") > -1)
            {
                ItemCategoryArr.splice(ItemCategoryArr.indexOf("4"), 1);
                if(reponse[3]==1)
                {
                    var tableFilters3 =
                        {
                            col_4: "none",
                            col_operation: {
                                id: ["value_total_receive_qty_a","val_tot_receive_ret_qty_a","value_total_order_amt_a","value_total_issue_qty_a","val_tot_issue_ret_qty_a","value_total_amount_a"],
                               // col: [22,23,24,25,26,28],//col: [18,19,20,22],
                                col: [23,24,25,26,27,29],
                                operation: ["sum","sum","sum","sum","sum","sum"],
                                write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
                            }
                        }

                }
                else if(reponse[3]==2)
                {
                    var tableFilters3 =
                        {
                            col_4: "none",
                            col_operation: {
                                id: ["value_total_receive_qty_a","val_tot_receive_ret_qty_a","value_total_order_amt_a","value_total_amount_a"],
                                col: [22,23,24,26],//col: [19,20,22],
                                operation: ["sum","sum","sum","sum"],
                                write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
                            }
                        }
                }
                else if(reponse[3]==3)
                {
                    var tableFilters3 =
                        {
                            col_30: "none",
                            col_operation: {
                                id: ["value_total_issue_qty_a","val_tot_issue_ret_qty_a","value_total_amount_a"],
                                col: [20,21,23],//col: [17,19],
                                operation: ["sum","sum","sum"],
                                write_method: ["innerHTML","innerHTML","innerHTML"]
                            }
                        }
                }

                setFilterGrid("table_body_4",-1,tableFilters3);
                setFilterGrid("filter_grid_1",-1);
            }

            if(ItemCategoryArr.indexOf("1") > -1)
            {
                ItemCategoryArr.splice(ItemCategoryArr.indexOf("1"), 1);
                if(reponse[3]==1)
                {
                    //alert(44);
                    var tableFilters4 =
                        {
                            col_40: "none",
                            col_operation: {
                                //id: ["value_total_receive","value_total_order_amt","value_total_issue","value_total_return","value_totla_amount"],
                                // col: [23,24,25,27],
                                id: ["value_total_receive_y","val_tot_receive_ret_qty_y","value_total_order_amt_y","value_total_issue_y","val_tot_issue_ret_qty_y","value_total_return_y","value_totla_amount_y","value_totla_amount_usd"],
                                //  col: [20,21,22,23,24,24,27,29],
                                col: [21,22,23,24,25,26,28,30],
                                operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
                                write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
                            }
                        }
                }
                else if(reponse[3]==2)
                {
                    var tableFilters4 =
                        {
                            col_30: "none",
                            col_operation: {
                                id: ["value_total_receive_y","val_tot_receive_ret_qty_y","value_total_order_amt_y","value_totla_amount_y","value_totla_amount_usd"],
                                //col: [20,21,22,24,26],
                                col: [21,22,23,25,27],
                                operation: ["sum","sum","sum","sum","sum"],
                                write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
                            }
                        }
                }
                else if(reponse[3]==3)
                {
                    var tableFilters4 =
                        {
                            col_30: "none",
                            col_operation: {
                                id: ["value_total_issue_y","val_tot_issue_ret_qty_y","value_total_return_y","value_totla_amount_y","value_totla_amount_usd"],
                                col: [20,21,22,24,26],
                                operation: ["sum","sum","sum","sum","sum"],
                                write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
                            }
                        }
                }
                setFilterGrid("table_body_1",-1,tableFilters4);
            }

            if (ItemCategoryArr.length > 0)
            {
                if(reponse[3]==1)
                {
                    var tableFilters5 =
                        {
                            col_30: "none",
                            col_operation: {
                                id: ["value_total_receive","value_total_order_amt","value_total_issue","value_total_amount"],
                                //col: [18,19,20,22],
                                // col: [22,23,24,26],
                                col: [23,24,26,28],
                                operation: ["sum","sum","sum","sum"],
                                write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
                            }
                        }
                }
                else if(reponse[3]==2)
                {
                    var tableFilters5 =
                        {
                            col_30: "none",
                            col_operation: {
                                id: ["value_total_receive","value_total_order_amt","value_total_amount"],
                                // col: [21,22,24],
                               // col: [20,21,23],
                                col: [21,22,24],
                                operation: ["sum","sum","sum"],
                                write_method: ["innerHTML","innerHTML","innerHTML"]
                            }
                        }
                }
                else if(reponse[3]==3)
                {
                    var tableFilters5 =
                        {
                            col_30: "none",
                            col_operation: {
                                id: ["value_total_issue","value_total_amount"],
                                // col: [17,19],
                                // col: [21,23],
                                col: [23,26],
                                operation: ["sum","sum"],
                                write_method: ["innerHTML","innerHTML"]
                            }
                        }
                }

                setFilterGrid("table_body_0",-1,tableFilters5);
               
            }
			// document.getElementById('excel').click();

            release_freezing();
            show_msg('3');
        }
    }
	
	
	function  generate_report_1(rptType)
	{
		var cbo_item_cat = $("#cbo_item_cat").val();
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_buyer_name = $("#cbo_buyer_name").val();
		var cbo_store_name = $("#cbo_store_name").val();
		var txt_date_from = $("#txt_date_from").val();
		var txt_date_to = $("#txt_date_to").val();
		var cbo_yarn_count = $("#cbo_yarn_count").val();
		var cbo_based_on = $("#cbo_based_on").val();
	    var txt_mrr_no = $("#txt_mrr_no").val();

	    if(txt_mrr_no!="") 
		{
			if( form_validation('cbo_company_name*txt_item_process_name','Company Name*Item Category')==false )
			{
				return;
			} 	
		}
		else 
		{
			if( form_validation('cbo_company_name*txt_item_process_name*txt_date_from*txt_date_to','Company Name*Item Category*Date From*Date To')==false )
			{
				return;
			}
		}

		var dataString = "&cbo_item_cat="+cbo_item_cat+"&cbo_company_name="+cbo_company_name+"&cbo_buyer_name="+cbo_buyer_name+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&cbo_yarn_count="+cbo_yarn_count+"&cbo_based_on="+cbo_based_on+"&rptType="+rptType+"&cbo_store_name="+cbo_store_name+"&txt_mrr_no="+txt_mrr_no;
		var data="action=generate_report_excel"+dataString;
		// alert(data);return;
		freeze_window(5);
		http.open("POST","requires/date_wise_item_recv_issue_multi_category_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse_1;
	}
	
    function generate_report_reponse_1()
    {
        if(http.readyState == 4)
        {
            var reponse=trim(http.responseText).split("####");
            
            // $("#report_container2").html(reponse[0]);
            document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none;"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            release_freezing();
			show_msg('3');
			document.getElementById('excel').click();
        }
    }


    function pi_report_generate(pi_no){
        print_report(pi_no, "pi_print_report", "requires/date_wise_item_recv_issue_multi_category_report_controller" );
        return;
    }

    function wo_report_generate(type, wo_id, wo_number = '',  company){
        if(type == 'trims'){
            print_report(wo_number+'*'+company, "trim_booking_report", "requires/date_wise_item_recv_issue_multi_category_report_controller" );
            return;
        }
    }

    function req_report_generate(type, req_id, company, remarks = "", template = 1, location = 0){
        if(type==3)
        {
            var show_item="";
            var report_title= "Purchase Requisition";
            print_report( company+'*'+req_id+'*'+report_title+'*'+remarks+'*'+type+'*'+show_item+'*'+template+'*'+location, "purchase_requisition_print_2", "../requires/purchase_requisition_controller" ) ;
            show_msg("3");
        }
        else if(type==5)
        {
            var show_item="";
            var report_title= "Purchase Requisition";
            print_report( company+'*'+req_id+'*'+report_title+'*'+remarks+'*'+type+'*'+show_item+'*'+template+'*'+location+'*'+$('#is_approved').val(), "purchase_requisition_print_3", "../requires/purchase_requisition_controller" ) ;
            //return;
            show_msg("3");
        }
        else if(type==8)
        {
            var show_item="";
            var report_title= "Purchase Requisition";
            print_report( company+'*'+req_id+'*'+report_title+'*'+remarks+'*'+type+'*'+show_item+'*'+template+'*'+location, "purchase_requisition_print_8", "../requires/purchase_requisition_controller" ) ;
            //return;
            show_msg("3");
        }else if(type==26)
        {
            var show_item="";
            var report_title= "Purchase Requisition";
            print_report( company+'*'+req_id+'*'+report_title+'*'+remarks+'*'+type+'*'+show_item+'*'+template+'*'+location, "purchase_requisition_print_26", "../requires/purchase_requisition_controller" ) ;
            //return;
            show_msg("3");
        }
        else if(type==6)
        {

            var show_item="";
            var report_title= "Purchase Requisition";
            print_report( company+'*'+req_id+'*'+report_title+'*'+remarks+'*'+type+'*'+show_item+'*'+template+'*'+location, "purchase_requisition_print_4", "../requires/purchase_requisition_controller" ) ;
            //return;
            show_msg("3");
        }
        else if(type==9)
        {

            var show_item="";
            var report_title= "Purchase Requisition";
            print_report( company+'*'+req_id+'*'+report_title+'*'+remarks+'*'+type+'*'+show_item+'*'+template+'*'+location, "purchase_requisition_print_9", "../requires/purchase_requisition_controller" ) ;
            //return;
            show_msg("3");
        }
        else if(type==7)
        {
            var show_item="";
            var report_title= "Purchase Requisition";
            print_report( company+'*'+req_id+'*'+report_title+'*'+remarks+'*'+type+'*'+show_item+'*'+template+'*'+location, "purchase_requisition_print_5", "../requires/purchase_requisition_controller" ) ;
            //return;
            show_msg("3");
        }
        else if(type==10)
        {


            var show_item="";
            //var r=confirm("Press  \"Cancel\"  to hide  Last Rate & Req Value \nPress  \"OK\"  to Show Last Rate & Req Value");
            var r=confirm("Press  \"Cancel\"  to hide Last Rec. Date & Last Rec. Qty & Last Rate & Req. Value \nPress  \"OK\"  to Show Last Rec. Date & Last Rec. Qty & Last Rate & Req. Value");
            if (r==true)
            {
                show_item="1";
            }
            else
            {
                show_item="0";
            }

            var report_title= "Purchase Requisition";
            print_report( company+'*'+req_id+'*'+report_title+'*'+remarks+'*'+type+'*'+show_item+'*'+template+'*'+location, "purchase_requisition_print_10", "../requires/purchase_requisition_controller" ) ;
            //return;
            show_msg("3");
        }
        else if(type==11)
        {
            var report_title= "Purchase Requisition";
            print_report( company+'*'+req_id+'*'+report_title+'*'+remarks+'*'+type+'*'+''+'*'+template+'*'+location, "purchase_requisition_print_11", "../requires/purchase_requisition_controller" ) ;
            //return;
            show_msg("3");
        }
        else if(type==12)
        {

            var show_item="";
            var report_title= "Purchase Requisition";
            print_report( company+'*'+req_id+'*'+report_title+'*'+remarks+'*'+type+'*'+show_item+'*'+template+'*'+location, "purchase_requisition_print_4_akh", "../requires/purchase_requisition_controller" ) ;
            //return;
            show_msg("3");
        }
        else if(type==13)
        {
            var show_item="";
            var report_title= "Purchase Requisition";
            print_report( company+'*'+req_id+'*'+report_title+'*'+remarks+'*'+type+'*'+show_item+'*'+template+'*'+location, "purchase_requisition_print_13", "../requires/purchase_requisition_controller" ) ;
            show_msg("3");
        }
        else if(type==14)
        {
            var show_item="";
            var report_title= "Purchase Requisition";
            print_report( company+'*'+req_id+'*'+report_title+'*'+remarks+'*'+type+'*'+show_item+'*'+template+'*'+location, "purchase_requisition_print_14", "../requires/purchase_requisition_controller" ) ;
            show_msg("3");
        }
        else if(type==15)
        {
            var show_item="";
            var report_title= "Purchase Requisition";
            print_report( company+'*'+req_id+'*'+report_title+'*'+remarks+'*'+type+'*'+show_item+'*'+template+'*'+location, "purchase_requisition_print_15", "../requires/purchase_requisition_controller" ) ;
            //return;
            show_msg("3");
        }
        else if(type==16)
        {
            var show_item="";
            var report_title= "Purchase Requisition";
            print_report( company+'*'+req_id+'*'+report_title+'*'+remarks+'*'+type+'*'+show_item+'*'+template+'*'+location, "purchase_requisition_print_16", "../requires/purchase_requisition_controller" ) ;
            //return;
            show_msg("3");
        }
        else if(type==17)
        {
            var show_item="";
            var r=confirm("Press \"OK\" Show With Model / Article, Size/MSR, Brand \nPress \"Cancel\" Show Without Model / Article, Size/MSR, Brand");
            if(r==true){ show_item=1; }else{ show_item=0; }
            var report_title= "Purchase Requisition";
            print_report( company+'*'+req_id+'*'+report_title+'*'+remarks+'*'+type+'*'+show_item+'*'+template+'*'+location, "purchase_requisition_category_wise_print", "../requires/purchase_requisition_controller" ) ;
            show_msg("3");
        }
        else if(type==18)
        {
            var show_item="";
            var report_title= "Purchase Requisition";
            print_report( company+'*'+req_id+'*'+report_title+'*'+remarks+'*'+type+'*'+show_item+'*'+template+'*'+location, "purchase_requisition_print_18", "../requires/purchase_requisition_controller" ) ;
            show_msg("3");
        }
        else if(type==19)
        {
            var show_item="";
            var report_title= "Purchase Requisition";
            print_report( company+'*'+req_id+'*'+report_title+'*'+remarks+'*'+type+'*'+show_item+'*'+template+'*'+location, "purchase_requisition_print_19", "../requires/purchase_requisition_controller" ) ;
            show_msg("3");
        }
        else if(type==20)
        {
            var show_item="";
            var report_title= "Purchase Requisition";
            print_report( company+'*'+req_id+'*'+report_title+'*'+remarks+'*'+type+'*'+show_item+'*'+template+'*'+location, "purchase_requisition_print_20", "../requires/purchase_requisition_controller" ) ;
            show_msg("3");
        }
        else if(type==21)
        {
            var show_item="";
            var report_title= "Purchase Requisition";
            print_report( company+'*'+req_id+'*'+report_title+'*'+remarks+'*'+type+'*'+show_item+'*'+template+'*'+location, "purchase_requisition_print_21", "../requires/purchase_requisition_controller" ) ;
            show_msg("3");
        }
        else if(type==22)
        {
            var show_item="";
            var report_title= "Purchase Requisition";
            print_report( company+'*'+req_id+'*'+report_title+'*'+remarks+'*'+type+'*'+show_item+'*'+template+'*'+location, "purchase_requisition_print_22", "../requires/purchase_requisition_controller" ) ;
            //return;
            show_msg("3");
        }
        else if(type==23)
        {
            var show_item="";
            var report_title= "Purchase Requisition";
            print_report( company+'*'+req_id+'*'+report_title+'*'+remarks+'*'+type+'*'+show_item+'*'+template+'*'+location, "purchase_requisition_print_23", "../requires/purchase_requisition_controller" ) ;
            //return;
            show_msg("3");
        }
        else if(type==24)
        {
            var show_item="";
            var report_title= "Purchase Requisition";
            print_report( company+'*'+req_id+'*'+report_title+'*'+remarks+'*'+type+'*'+show_item+'*'+template+'*'+location, "purchase_requisition_print_24", "../requires/purchase_requisition_controller" ) ;
            show_msg("3");
        }
        else if(type==25)
        {
            var show_item="";
            var report_title= "Purchase Requisition";
            print_report( company+'*'+req_id+'*'+report_title+'*'+remarks+'*'+type+'*'+show_item+'*'+template+'*'+location, "purchase_requisition_print_25", "../requires/purchase_requisition_controller" );
            show_msg("3");
        }
        else
        {
            var report_title= "Purchase Requisition";
            print_report( company+'*'+req_id+'*'+report_title+'*'+''+'*'+''+'*'+template+'*'+location, "purchase_requisition_print", "../requires/purchase_requisition_controller" ) ;
            //return;
            show_msg("3");

        }
    }

    function generate_trim_report(dataparam, report_type, action,entry_form){
        if(dataparam.length == 0) {
            return;
        }
        var dataparam = dataparam.split('*#*');
        var txt_booking_no = dataparam[0];
        var cbo_company_name = dataparam[1];
        var id_approved_id = dataparam[2];

        if(action=='show_trim_booking_report3')
        {
            var show_comment='';
            var r=confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
            if (r==true) show_comment="1"; else show_comment="0";
        }
        else if(action=='show_trim_booking_report12')
        {
            var show_comment='';
            var r=confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
            if (r==true) show_comment="1"; else show_comment="0";
        }
        else if(action=='show_trim_booking_report5')
        {
            var show_comment='';
            var r=confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
            if (r==true) show_comment="1"; else show_comment="0";
        }
        else if(action=='show_trim_booking_report13')
        {
            var show_comment='';
            var r=confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
            if (r==true) show_comment="1"; else show_comment="0";
        }
        else if(action=='show_trim_booking_report6')
        {
            var show_comment=1;
        }
        else if(action=='show_trim_booking_report18')
        {
            var show_comment=1;
        }
        else
        {
            var show_comment='';
            var r=confirm("Press  \"Cancel\"  to hide  Remarks\nPress  \"OK\"  to Show Remarks");
            if (r==true) show_comment="1"; else show_comment="0";
        }
        var report_title = 'Trims Booking Report'

        var data="action="+action+"&txt_booking_no='"+txt_booking_no+"'&cbo_company_name='"+cbo_company_name+"'&id_approved_id='"+id_approved_id+"'&report_title="+report_title+"&show_comment="+show_comment+"&report_type="+report_type;

        var page_link = '';

        if(entry_form=='272')
        {
            page_link = "../../order/woven_gmts/requires/trims_booking_multi_job_controllerurmi.php";
        }
        else if(entry_form=='87')
        {
            page_link = "../../order/woven_order/requires/trims_booking_multi_job_controllerurmi.php";
        }
        if(entry_form=='252')
        {
            page_link = "../../order/sweater/trims_booking/requires/trims_booking_multi_job_controllerurmi.php";
        }

        http.open("POST",page_link,true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = generate_trim_report_reponse;


    }

    function generate_trim_report_reponse(){
        // console.log(http.responseText);
        if(http.readyState == 4){
            release_freezing();
            var file_data=http.responseText.split("####");
            //  alert(file_data[2]);
            $('#data_panel').html(file_data[0]);


            var report_title=$( "div.form_caption" ).html();
            var w = window.open("Surprise", "_blank");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title>'+report_title+'</title></head><body>'+document.getElementById('data_panel').innerHTML+'</body></html>');
            d.close();
        }
    }

	function new_window()
	{
		if ($("#scroll_body").length){
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
                }
                if ($("#scroll_body1").length){
                document.getElementById('scroll_body1').style.overflow="auto";
		document.getElementById('scroll_body1').style.maxHeight="none";
                }
                if ($("#scroll_body2").length){
                document.getElementById('scroll_body2').style.overflow="auto";
		document.getElementById('scroll_body2').style.maxHeight="none";
                }
                if ($("#scroll_body3").length){
                document.getElementById('scroll_body3').style.overflow="auto";
		document.getElementById('scroll_body3').style.maxHeight="none";
                }
                if ($("#scroll_body4").length){
                document.getElementById('scroll_body4').style.overflow="auto";
		document.getElementById('scroll_body4').style.maxHeight="none";
                }
                if ($("#scroll_body13").length){
                document.getElementById('scroll_body13').style.overflow="auto";
		document.getElementById('scroll_body13').style.maxHeight="none";
                }


		//$('#table_body tr:first').hide();
                if ($("#table_body_1").length){
                    $('#table_body_1 tr:first').hide();
                }
                if ($("#table_body_4").length){
                    $('#table_body_4 tr:first').hide();
                }
                if ($("#table_body_13").length){
                    $('#table_body_13 tr:first').hide();
                }
                if ($("#table_body_2").length){
                    $('#table_body_2 tr:first').hide();
                }
                if ($("#table_body_3").length){
                    $('#table_body_3 tr:first').hide();
                }

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
                if ($("#scroll_body").length){
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="250px";
                }
                if ($("#scroll_body1").length){
                document.getElementById('scroll_body1').style.overflow="auto";
		document.getElementById('scroll_body1').style.maxHeight="250px";
                }
                if ($("#scroll_body2").length){
                document.getElementById('scroll_body2').style.overflow="auto";
		document.getElementById('scroll_body2').style.maxHeight="250px";
                }
                if ($("#scroll_body3").length){
                document.getElementById('scroll_body3').style.overflow="auto";
		document.getElementById('scroll_body3').style.maxHeight="250px";
                }
                if ($("#scroll_body4").length){
                document.getElementById('scroll_body4').style.overflow="auto";
		document.getElementById('scroll_body4').style.maxHeight="250px";
                }
                if ($("#scroll_body13").length){
                document.getElementById('scroll_body13').style.overflow="auto";
		document.getElementById('scroll_body13').style.maxHeight="250px";
                }
		//$('#table_body tr:first').show();
                if ($("#table_body_1").length){
                    $('#table_body_1 tr:first').show();
                }
                if ($("#table_body_4").length){
                    $('#table_body_4 tr:first').show();
                }
                if ($("#table_body_13").length){
                    $('#table_body_13 tr:first').show();
                }
                if ($("#table_body_2").length){
                    $('#table_body_2 tr:first').show();
                }
                if ($("#table_body_3").length){
                    $('#table_body_3 tr:first').show();
                }
	}

	function  generate_report_raw_material(rptType)
	{
		var cbo_item_cat = $("#cbo_item_cat").val();
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_buyer_name = $("#cbo_buyer_name").val();
		var cbo_store_name = $("#cbo_store_name").val();
		var txt_date_from = $("#txt_date_from").val();
		var txt_date_to = $("#txt_date_to").val();
		var cbo_yarn_count = $("#cbo_yarn_count").val();
		var cbo_based_on = $("#cbo_based_on").val();
	    var txt_mrr_no = $("#txt_mrr_no").val();

	    if(txt_mrr_no!="") 
		{
			if( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			} 	
		}
		else 
		{
			if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false )
			{
				return;
			}
		}

		var dataString = "&cbo_item_cat="+cbo_item_cat+"&cbo_company_name="+cbo_company_name+"&cbo_buyer_name="+cbo_buyer_name+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&cbo_yarn_count="+cbo_yarn_count+"&cbo_based_on="+cbo_based_on+"&rptType="+rptType+"&cbo_store_name="+cbo_store_name+"&txt_mrr_no="+txt_mrr_no;
		var data="action=generate_report_raw_material"+dataString;
		// alert(data);return;
		freeze_window(5);
		http.open("POST","requires/date_wise_item_recv_issue_multi_category_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_raw_material_reponse;
	}

	function generate_report_raw_material_reponse()
	{
		if(http.readyState == 4)
		{
			//alert(http.responseText);
			var reponse=trim(http.responseText).split("**");
			//alert(reponse[1]+"=="+reponse[2]+"=="+reponse[3]+"=="+reponse[4]);
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none;"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window_raw_material()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//document.getElementById('report_container').innerHTML=report_convert_button('../../');
			//append_report_checkbox('table_header_1',1);
			release_freezing();
	        if ($("#table_body_1").length){}

	        var tableFilters6 =
			 {
				col_30: "none",
				col_operation: {
				id: ["value_total_issue","value_total_amount"],
			  // col: [17,19],
			   // col: [21,23],
			    col: [13,15],
			   operation: ["sum","sum"],
			   write_method: ["innerHTML","innerHTML"]
				}
			 }


			setFilterGrid("table_body_raw_material",-1,tableFilters6);

			release_freezing();
			show_msg('3');
			//document.getElementById('report_container').innerHTML=report_convert_button('../../');
		}
	}

	function new_window_raw_material()
	{
		if ($("#scroll_body").length){
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
        }

		//$('#table_body tr:first').hide();
        if ($("#table_body_raw_material").length){
            $('#table_body_raw_material tr:first').hide();
        }

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
        if ($("#scroll_body").length){
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="250px";
        }
                
		//$('#table_body tr:first').show();
        if ($("#table_body_raw_material").length){
            $('#table_body_raw_material tr:first').show();
        }
                
	}

	function fn_change_base(str)
	{
		//alert(str);
		if(str==1)
		{
			$("#up_tr_date").html("");
			$("#up_tr_date").html("Transaction Date Range");
			$('#up_tr_date').attr('style','color:blue');
		}
		else
		{
			$("#up_tr_date").html("");
			$("#up_tr_date").html("Insert Date Range");
			$('#up_tr_date').attr('style','color:blue');
		}
	}

	function openmypage_process()
	{
		var cbo_item_cat = $('#cbo_item_cat').val();
        
		var title = 'Item Name Selection Form';
		var page_link = 'requires/date_wise_item_recv_issue_multi_category_report_controller.php?cbo_item_cat='+cbo_item_cat+'&action=item_process_name_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','../');

        //emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/date_wise_item_recv_issue_multi_category_report_controller.php?action=item_process_name_popup&cbo_item_cat='+cbo_item_cat,'Item Name Selection Form', 'width=980px,height=350px,center=1,resize=1,scrolling=0','../')


		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var item_process_id=this.contentDoc.getElementById("hidden_item_process_id").value;	 //Access form field with id="emailfield"
			var item_process_name=this.contentDoc.getElementById("hidden_process_name").value;
			$('#cbo_item_cat').val(item_process_id);
			$('#txt_item_process_name').val(item_process_name);
		}
	}	

	function generate_trims_print_report(transaction_type,rec_issue_id,is_multi,company_id,entry_form,print_btn)
	{
        // alert(transaction_type+"--"+rec_issue_id+"--"+is_multi+"--"+company_id+"--"+entry_form+"--"+print_btn);
		if(entry_form==24 || entry_form==25 || entry_form==49 || entry_form==73)
		{
			if(is_multi==0 && transaction_type==1)
			{
				var report_title="Trims Receive Entry";
				if(print_btn==86)
				{
					print_report( company_id+'__'+rec_issue_id+'__'+report_title, "trims_receive_entry_print", "../trims_store/requires/trims_receive_entry_controller" ) ;
				}
				else if(print_btn==116)
				{
					print_report( company_id+'__'+rec_issue_id+'__'+report_title, "trims_receive_entry_print_2", "../trims_store/requires/trims_receive_entry_controller" ) ;
				}
				else if(print_btn==136)
				{
					print_report( company_id+'__'+rec_issue_id+'__'+report_title, "trims_receive_entry_print_4", "../trims_store/requires/trims_receive_entry_controller" ) ;
				}

			}
			else if(is_multi==1 && transaction_type==1)
			{
				var report_title="Trims Receive Multi Ref.";
				print_report( company_id+'*'+rec_issue_id+'*'+report_title, "trims_receive_entry_print", "../trims_store/requires/trims_receive_multi_ref_entry_controller" );
			}
            else if(is_multi==3 && transaction_type==1)
            {
                var report_title="Trims Receive Multi Ref. v3";
                print_report( company_id+'*'+rec_issue_id+'*'+report_title, "trims_receive_entry_print", "../trims_store/requires/trims_receive_multi_ref_entry_v3_controller" )
            }
			else
			{
				if(transaction_type==2)
				{
					var report_title="Trims Issue";
					//alert(transaction_type);
					//generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val()+'*'+report_title,'trims_issue_entry_print');
					print_report( company_id+'*'+rec_issue_id+'*'+report_title, "trims_issue_entry_print", "../trims_store/requires/trims_issue_entry_controller" );
				}
				else if(transaction_type==3)
				{
					var report_title="Trims Receive Return";
					print_report( company_id+'*'+rec_issue_id+'*'+report_title, "trims_receive_return_print", "../trims_store/requires/trims_receive_rtn_controller" );
				}
				else if(transaction_type==4)
				{
					var report_title="Trims Issue Return";
					generate_report_file( company_id+'*'+rec_issue_id+'*'+report_title,'trims_issue_entry_print','../trims_store/requires/trims_issue_return_entry_controller');
				}
			}
		}
		else
		{

			if(transaction_type==1)
			{
				var report_title="General Trims Receive";
				//print_report( company_id+'*'+rec_issue_id+'*'+report_title, "trims_receive_entry_print", "../trims_store/requires/trims_receive_entry_controller" ) ;
				print_report( company_id+'*'+rec_issue_id+'*'+report_title, "general_item_receive_print", "../general_store/general_item_receive/requires/general_item_receive_controller" );
			}
			if(transaction_type==2)
			{
				var report_title="General Trims Issue";
				//alert(transaction_type);
				//generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val()+'*'+report_title,'trims_issue_entry_print');
				print_report( company_id+'*'+rec_issue_id+'*'+report_title, "general_item_issue_print", "../general_store/general_item_issue/requires/general_item_issue_controller" );
			}
			else if(transaction_type==3)
			{
				var report_title="General Trims Receive Return";
				print_report( company_id+'*'+rec_issue_id+'*'+report_title, "general_item_receive_return_print", "../general_store/requires/general_item_receive_return_entry_controller" );
			}
			else if(transaction_type==4)
			{
				var report_title="General Trims Issue Return";
				generate_report_file( company_id+'*'+rec_issue_id+'*'+report_title,'general_item_issue_return_print','../general_store/requires/general_item_issue_return_controller');
			}
		}

		return;
	}

    function openmypage_mrr(action,type,trans_id,company)
    {
		
        var responseHtml = return_ajax_request_value(type+'**'+trans_id, action, 'requires/date_wise_item_recv_issue_multi_category_report_controller');
		// alert(responseHtml);
        var splitResponse="";
        splitResponse = responseHtml.split("##");
        var report_title = "";
        var transaction_type=  splitResponse[3]*1;
        var rec_issue_id = splitResponse[0]*1;
        var entry_form = splitResponse[2]*1;
        var report_type = splitResponse[6]*1;
        var location = splitResponse[7]*1;
        var store = splitResponse[8]*1;
        var buyer = splitResponse[9]*1;
        var basis = splitResponse[10]*1;
        //alert(entry_form);
		// alert(splitResponse[5]+"===="+responseHtml);//return;
        if(transaction_type == 1) // Receive
        {
            if(entry_form == 1) // Yarn Receive
            {
                print_report( company+'*'+splitResponse[1]+'*'+rec_issue_id, "yarn_receive_print", "../requires/yarn_receive_controller" )
            }
            else if(entry_form == 58) // Knit Grey Fabric roll Receive
            {
            	if (splitResponse[6]==1) // print
	            {
	            	var report_title = "Fabric Roll Receive";
	            	var data = company+'*'+splitResponse[1]+'*'+rec_issue_id+'*'+report_title+'*'+splitResponse[4]+'*'+splitResponse[5];
	            	window.open("../grey_fabric/requires/grey_fabric_receive_roll_controller.php?data=" + data + '&action=grey_fabric_receive_print', true);
	            }
	            else if(splitResponse[6]==2) // print2
	            {
	            	var report_title = "Fabric Roll Receive";
	            	var data = company+'*'+splitResponse[1]+'*'+rec_issue_id+'*'+report_title+'*'+splitResponse[4]+'*'+'print2'+'*'+'2';
	            	window.open("../grey_fabric/requires/grey_fabric_receive_roll_controller.php?data=" + data + '&action=grey_fabric_receive_print', true);
	            }
	            else if(splitResponse[6]==3) // print3
	            {
	            	var report_title = "Knit Grey Fabric Roll Receive";
	            	var data = company+'*'+splitResponse[1]+'*'+rec_issue_id+'*'+report_title+'*'+'print3'+'*'+''+'*'+splitResponse[4]+'*'+splitResponse[5];
	            	window.open("../grey_fabric/requires/grey_fabric_receive_roll_controller.php?data=" + data + '&action=grey_fabric_receive_print3', true);
	            }
	            else if(splitResponse[6]==4) // Print Barcode
	            {
	            	var data = company+'*'+splitResponse[1]+'*'+rec_issue_id;
	            	window.open("../grey_fabric/requires/grey_fabric_receive_roll_controller.php?data=" + data + '&action=receive_challan_print', true);
	            }
	            else if(splitResponse[6]==5) // Fabric Details
	            {
	            	var data = company+'*'+splitResponse[1]+'*'+rec_issue_id;
	            	window.open("../grey_fabric/requires/grey_fabric_receive_roll_controller.php?data=" + data + '&action=fabric_details_print', true);
	            }
            }
            else if(entry_form == 68) // Knit Finish Fabric roll Receive
            {
            	if (splitResponse[5]==1) // print, // splitResponse[5] is report type
	            {
	            	var data = company+'*'+splitResponse[1]+'*'+rec_issue_id+'*'+splitResponse[4];
	            	window.open("../finish_fabric/requires/finish_feb_roll_receive_by_store_controller.php?data=" + data + '&action=finish_delivery_print', true);
	            }
	            else if(splitResponse[5]==2) // Fabric Details
	            {
	            	var data = company+'*'+splitResponse[1]+'*'+rec_issue_id+'*'+splitResponse[4];
	            	window.open("../finish_fabric/requires/finish_feb_roll_receive_by_store_controller.php?data=" + data + '&action=fabric_details_print', true);
	            }
            }
            else if(entry_form == 265) // Knit Finish Fabric roll Receive
            {
            	print_report( company+'*'+rec_issue_id+'*'+report_title, "general_item_receive_print", "../raw_material_store/requires/raw_material_item_issue_controller" )
            }
            else // General item
            {
                print_report( company+'__'+rec_issue_id+'__'+report_title, "general_item_receive_print", "../general_store/requires/general_item_receive_controller" )
            }
        }
        else if(transaction_type==2) // Issue 
        {
            if(entry_form == 3) // yarn issue
            {
	            var show_val_column = "0";
	            var print_with_vat = 0;
	            var show_val_column = "1";
	            var report_title = "Yarn Issue";
	            var data = company + '*' + splitResponse[1] + '*' + report_title + '*' + splitResponse[4]*1 + '*' + splitResponse[5] + '*' + rec_issue_id + '*' + show_val_column + '*' + print_with_vat;
	            if (report_type==1) // print
	            {
	            	var show_val_column = "0";
		        	var print_with_vat = 0;
		        	var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");
		        	if (r == true) {
		        		show_val_column = "1";
		        	}
		        	else {
		        		show_val_column = "0";
		        	}
		        	
		        	var data = company + '*' + splitResponse[1] + '*' + report_title + '*' + splitResponse[4]*1 + '*' + splitResponse[5] + '*' + rec_issue_id + '*' + show_val_column + '*' + print_with_vat;
	            	window.open("../requires/yarn_issue_controller.php?data=" + data + '&action=yarn_issue_print', true);
	            }
	            else if(report_type==2) // Print4
	            {
	            	var show_val_column = "0";
		        	var print_with_vat = 0;
		        	var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");
		        	if (r == true) {
		        		show_val_column = "1";
		        	}
		        	else {
		        		show_val_column = "0";
		        	}
		        	
		        	var data = company + '*' + splitResponse[1] + '*' + report_title + '*' + splitResponse[4]*1 + '*' + splitResponse[5] + '*' + rec_issue_id + '*' + show_val_column + '*' + print_with_vat;

	            	window.open("../requires/yarn_issue_controller.php?data=" + data + '&action=yarn_issue_print10', true);
	            }
	            else if(report_type==3) // Print2
	            {
	            	var show_val_column = "0";
		        	var print_with_vat = 0;
		        	var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");
		        	if (r == true) {
		        		show_val_column = "1";
		        	}
		        	else {
		        		show_val_column = "0";
		        	}
		        	
		        	var data = company + '*' + splitResponse[1] + '*' + report_title + '*' + splitResponse[4]*1 + '*' + splitResponse[5] + '*' + rec_issue_id + '*' + show_val_column + '*' + print_with_vat;
	            	window.open("../requires/yarn_issue_controller.php?data=" + data + '&action=yarn_issue_print2', true);
	            }
	            else if(report_type==4) // Print3
	            {
	            	var show_val_column = "0";
		        	var print_with_vat = 0;
		        	var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");
		        	if (r == true) {
		        		show_val_column = "1";
		        	}
		        	else {
		        		show_val_column = "0";
		        	}
		        	
		        	var data = company + '*' + splitResponse[1] + '*' + report_title + '*' + splitResponse[4]*1 + '*' + splitResponse[5] + '*' + rec_issue_id + '*' + show_val_column + '*' + print_with_vat;
	            	window.open("../requires/yarn_issue_controller.php?data=" + data + '&action=yarn_issue_print3', true);
	            }
	            else if(report_type==5)  // Print With VAT
	            {
	            	var show_val_column = "0";
		        	var print_with_vat = 0;
		        	var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");
		        	if (r == true) {
		        		show_val_column = "1";
		        	}
		        	else {
		        		show_val_column = "0";
		        	}
		        	
		        	var data = company + '*' + splitResponse[1] + '*' + report_title + '*' + splitResponse[4]*1 + '*' + splitResponse[5] + '*' + rec_issue_id + '*' + show_val_column + '*' + print_with_vat;
	            	window.open("../requires/yarn_issue_controller.php?data=" + data + '&action=yarn_issue_print', true);
	            }
	            else if(report_type==6) // Requisition Details
	            {
	            	var data = company + '_' + splitResponse[7] + '_' + splitResponse[1];
	            	window.open("../requires/yarn_issue_controller.php?data=" + data + '&action=requisition_print', true);
	            }
	            else if(report_type==7) // Without Program
	            {
	            	var show_val_column = "0";
		            var print_with_vat = 1;
		            var report_title = "Yarn Issue";
		            var data = company + '*' + splitResponse[1] + '*' + report_title + '*' + splitResponse[4]*1 + '*' + splitResponse[5] + '*' + rec_issue_id + '*' + show_val_column + '*' + print_with_vat;

	            	window.open("../requires/yarn_issue_controller.php?data=" + data + '&action=yarn_issue_print', true);
	            }
	            else if(report_type==8) // Print7
	            {
	            	var show_val_column = "0";
		        	var print_with_vat = 0;
		        	var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");
		        	if (r == true) {
		        		show_val_column = "1";
		        	}
		        	else {
		        		show_val_column = "0";
		        	}
		        	var data = company + '*' + splitResponse[1] + '*' + report_title + '*' + splitResponse[4]*1 + '*' + splitResponse[5] + '*' + rec_issue_id + '*' + show_val_column + '*' + print_with_vat + '*1';
	            	window.open("../requires/yarn_issue_controller.php?data=" + data + '&action=yarn_issue_print12', true);
	            }
	            else if(report_type==9) // Print 6
	            {
	            	var show_val_column = "0";
		        	var print_with_vat = 0;
		        	var organ_print = 0;
		        	var data = company + '*' + splitResponse[1] + '*' + report_title + '*' + splitResponse[4]*1 + '*' + splitResponse[5] + '*' + rec_issue_id + '*' + show_val_column + '*' + print_with_vat+'*'+organ_print;
	            	window.open("../requires/yarn_issue_controller.php?data=" + data + '&action=yarn_issue_print6', true);
	            }
	            else if(report_type==10) // Print Outbound
	            {
	            	var show_val_column = "0";
        			var print_with_vat = 1;
        			var data = company + '*' + splitResponse[1] + '*' + report_title + '*' + splitResponse[4]*1 + '*' + splitResponse[5] + '*' + rec_issue_id + '*' + show_val_column + '*' + print_with_vat;
	            	window.open("../requires/yarn_issue_controller.php?data=" + data + '&action=yarn_issue_print8', true);
	            }
	            else if(report_type==11) // Composition wise lot
	            {
	            	window.open("../requires/yarn_issue_controller.php?data=" + data + '&action=yarn_issue_print', true);
	            }
	            else if(report_type==12) // print 8
	            {
	            	var show_val_column = "0";
		            var print_with_vat = 1;
		            var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");
		            if (r == true) {
		                show_val_column = "1";
		            }
		            else {
		                show_val_column = "0";
		            }
		            var data = company + '*' + splitResponse[1] + '*' + report_title + '*' + splitResponse[4]*1 + '*' + splitResponse[5] + '*' + rec_issue_id + '*' + show_val_column + '*' + print_with_vat;
	            	window.open("../requires/yarn_issue_controller.php?data=" + data + '&action=yarn_issue_print15', true);
	            }
	            else if(report_type==13) // print 7
	            {
	            	window.open("../requires/yarn_issue_controller.php?data=" + data + '&action=yarn_issue_print', true);
	            }
	            else if(report_type==14) // print 9
	            {
	            	var show_val_column = "0";
		        	var print_with_vat = 0;
		        	var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");
		        	if (r == true) {
		        		show_val_column = "1";
		        	}
		        	else {
		        		show_val_column = "0";
		        	}
		        	var data = company + '*' + splitResponse[1] + '*' + report_title + '*' + splitResponse[4]*1 + '*' + splitResponse[5] + '*' + rec_issue_id + '*' + show_val_column + '*' + print_with_vat;
	            	window.open("../requires/yarn_issue_controller.php?data=" + data + '&action=yarn_issue_store_print', true);
	            }
	            else if(report_type==15) // print 11
	            {
	            	var show_val_column = "0";
            		var print_with_vat = 1;
            		var data = company + '*' + splitResponse[1] + '*' + report_title + '*' + splitResponse[4]*1 + '*' + splitResponse[5] + '*' + rec_issue_id + '*' + show_val_column + '*' + print_with_vat;
	            	window.open("../requires/yarn_issue_controller.php?data=" + data + '&action=yarn_issue_print11', true);
	            }
	            else if(report_type==16) // Print 10, report_type==16
	            {
	            	var show_val_column = "0";
		            var print_with_vat = 1;
		            var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");
		            if (r == true)
					{
		                show_val_column = "1";
		            }
		            else
					{
		                show_val_column = "0";
		            }
		            var data = company + '*' + splitResponse[1] + '*' + report_title + '*' + splitResponse[4]*1 + '*' + splitResponse[5] + '*' + rec_issue_id + '*' + show_val_column + '*' + print_with_vat+ '*0'+ '*0'+ '*1';
	            	window.open("../requires/yarn_issue_controller.php?data=" + data + '&action=yarn_issue_print17', true);
	            }
                else if(report_type==17) // Print 15, report_type==17
                {
                    var show_val_column = "0";
		            var print_with_vat = 1;
		            var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");
		            if (r == true)
					{
		                show_val_column = "1";
		            }
		            else
					{
		                show_val_column = "0";
		            }

                    var data = company + '*' + splitResponse[1] + '*' + report_title + '*' + splitResponse[4]*1 + '*' + splitResponse[5] + '*' + rec_issue_id + '*' + show_val_column + '*' + print_with_vat+ '*' + location+ '*' + store+ '*' + basis;

	            	window.open("../requires/yarn_issue_controller.php?data=" + data + '&action=yarn_issue_print23', true);
                }
                else // Print 18, report_type==18
                {
                    var show_val_column = "0";
		            var print_with_vat = 1;
		            var r = confirm("Press \"OK\" to open with Cust Buyer.\nPress \"Cancel\" to open without Cust Buyer.");
		            if (r == true)
					{
		                show_val_column = "1";
		            }
		            else
					{
		                show_val_column = "0";
		            }
		            var data = company + '*' + splitResponse[1] + '*' + report_title + '*' + splitResponse[4]*1 + '*' + splitResponse[5] + '*' + rec_issue_id + '*' + show_val_column + '*' + print_with_vat+ '*' + location+ '*' + store+ '*' + basis+ '*' + buyer+'*1';
	            	window.open("../requires/yarn_issue_controller.php?data=" + data + '&action=yarn_issue_store_printccl', true);
                }
            }
            else if(entry_form == 61) // Knit Grey Fabric roll Issue
            {            	
	            if (report_type==1) // Print Barcode
	            {		        	
		        	var data = company + '*' + splitResponse[1] + '*' + rec_issue_id;
	            	window.open("../grey_fabric/requires/grey_fabric_issue_roll_wise_controller.php?data=" + data + '&action=issue_challan_print', true);
	            }
	            else if(report_type==2) // Fabric Details
	            {
	            	var data = company + '*' + splitResponse[1] + '*' + rec_issue_id;
	            	window.open("../grey_fabric/requires/grey_fabric_issue_roll_wise_controller.php?data=" + data + '&action=fabric_details_print', true);
	            }
	            else if(report_type==3) // GIN3-MC
	            {
	            	var report_title = "MC Wise Print";
	            	var data = company + '*' + splitResponse[1] + '*' + rec_issue_id;
	            	window.open("../grey_fabric/requires/grey_fabric_issue_roll_wise_controller.php?data=" + data + '&action=mc_wise_print', true);
	            }
	            else if(report_type==4) // GIN4
	            {
	            	var data = company + '*' + splitResponse[1] + '*' + rec_issue_id;
	            	window.open("../grey_fabric/requires/grey_fabric_issue_roll_wise_controller.php?data=" + data + '&action=fabric_details_print_bpkw', true);
	            }
	            else if(report_type==5) // GIN5
	            {
	            	var data = company + '*' + splitResponse[1] + '*' + rec_issue_id;
	            	window.open("../grey_fabric/requires/grey_fabric_issue_roll_wise_controller.php?data=" + data + '&action=fabric_details_print_bpkw_gin5', true);
	            }
	            else if(report_type==6) // Print With Collar Cuff
	            {
	            	var data = company + '*' + splitResponse[1] + '*' + rec_issue_id;
	            	window.open("../grey_fabric/requires/grey_fabric_issue_roll_wise_controller.php?data=" + data + '&action=roll_issue_challan_print2', true);
	            }
	            else if(report_type==7) // GIN2
	            {
	            	var data = company + '*' + splitResponse[1] + '*' + rec_issue_id;
	            	window.open("../grey_fabric/requires/grey_fabric_issue_roll_wise_controller.php?data=" + data + '&action=roll_issue_challan_print', true);
	            }
	            else if(report_type==8) // Sales Wise Issue
	            {
	            	var data = company + '*' + splitResponse[1] + '*' + rec_issue_id;
	            	window.open("../grey_fabric/requires/grey_fabric_issue_roll_wise_controller.php?data=" + data + '&action=sales_roll_issue_challan_print', true);
	            }
	            else if(report_type==9) // GIN1
	            {
	            	var data = company + '*' + splitResponse[1] + '*' + rec_issue_id;
	            	window.open("../grey_fabric/requires/grey_fabric_issue_roll_wise_controller.php?data=" + data + '&action=roll_issue_challan_print1', true);
	            }
	            else if(report_type==10) // Print 1
	            {
	            	var data = company + '*' + splitResponse[1] + '*' + report_title + '*' + rec_issue_id + '*' + splitResponse[4]*1 + '*1' + '*' + splitResponse[7];
	            	window.open("../grey_fabric/requires/grey_fabric_issue_roll_wise_controller.php?data=" + data + '&action=roll_issue_no_of_copy_print', true);
	            }
	            else
	            {
	            	var data = company + '*' + splitResponse[1] + '*' + report_title + '*' + rec_issue_id + '*' + splitResponse[4]*1 + '*1' + '*' + splitResponse[7];
	            	window.open("../grey_fabric/requires/grey_fabric_issue_roll_wise_controller.php?data=" + data + '&action=roll_issue_no_of_copy_print', true);
	            }
            }
            else if(entry_form == 71) // Knit Finish Fabric roll Issue
            {            	
	            if (report_type==1) // Print1
	            {		        	
		        	var data = company + '*' + splitResponse[1] + '*' + rec_issue_id+'*'+report_title;
	            	window.open("../finish_fabric/requires/finish_fabric_issue_roll_wise_controller.php?data=" + data + '&action=finish_issue_print', true);
	            }
	            else if(report_type==2) // Print2
	            {
	            	var data = company + '*' + splitResponse[1] + '*' + rec_issue_id+'*'+report_title;
	            	window.open("../finish_fabric/requires/finish_fabric_issue_roll_wise_controller.php?data=" + data + '&action=finish_issue_print2', true);
	            }
	            else if(report_type==3) // Print3
	            {
	            	var data = company + '*' + splitResponse[1] + '*' + rec_issue_id+'*'+report_title;
	            	window.open("../finish_fabric/requires/finish_fabric_issue_roll_wise_controller.php?data=" + data + '&action=actn_print_button_3', true);
	            }
	            else if(report_type==4) // Print4
	            {
	            	var data = company + '*' + splitResponse[1] + '*' + rec_issue_id;
	            	window.open("../finish_fabric/requires/finish_fabric_issue_roll_wise_controller.php?data=" + data + '&action=actn_print_button_4', true);
	            }
	            else if(report_type==5) // Print5
	            {
	            	var data = company + '*' + splitResponse[1] + '*' + report_title + '*' + rec_issue_id + '*' + splitResponse[4]*1 + '*1' + '*' + splitResponse[7];
	            	window.open("../finish_fabric/requires/finish_fabric_issue_roll_wise_controller.php?data=" + data + '&action=roll_issue_no_of_copy_print', true);
	            }
	            else if(report_type==6) // Print Barcode
	            {
	            	var data = company + '*' + splitResponse[1] + '*' + rec_issue_id;
	            	window.open("../finish_fabric/requires/finish_fabric_issue_roll_wise_controller.php?data=" + data + '&action=issue_challan_print', true);
	            }
	            else if(report_type==7) // Fabric Details
	            {
	            	var data = company + '*' + splitResponse[1] + '*' + rec_issue_id;
	            	window.open("../finish_fabric/requires/finish_fabric_issue_roll_wise_controller.php?data=" + data + '&action=fabric_details_print', true);
	            }
	            else
	            {
	            	var data = company + '*' + splitResponse[1] + '*' + rec_issue_id;
	            	window.open("../finish_fabric/requires/finish_fabric_issue_roll_wise_controller.php?data=" + data + '&action=fabric_details_print', true);
	            }

            }
            else
            {
                var report_title="General Trims Issue";
                print_report( company+'*'+rec_issue_id+'*'+report_title, "general_item_issue_print", "../general_store/general_item_issue/requires/general_item_issue_controller" );
            }
        }
        else if(transaction_type==3)
        {
            if(entry_form == 8)
            {
                var path = "../"
                var report_title="Yarn Receive Return";
                 print_report( company+'*'+splitResponse[1]+'*'+report_title+'*'+path, "yarn_receive_return_print", "../requires/yarn_receive_return_controller" )
            }else
            {
                var report_title="General Trims Receive Return";
                print_report( company+'*'+rec_issue_id+'*'+report_title, "general_item_receive_return_print", "../general_store/requires/general_item_receive_return_entry_controller" );
            }
        }
        else if(transaction_type==4)
        {
            if(entry_form == 9)
            {
                var report_title = "Yarn Issue Return";
                print_report( company+'*'+splitResponse[1]+'*'+report_title,"yarn_issue_return_print", "../requires/yarn_issue_return_controller")
            }else{
                var report_title="General Trims Issue Return";
                print_report( company+'*'+rec_issue_id+'*'+report_title,'general_item_issue_return_print','../general_store/requires/general_item_issue_return_controller');
            }
        }
		
		else if(transaction_type==5 || transaction_type==6)
        {
			//alert(transaction_type);
            if(entry_form==10)
            {
                var report_title="Yarn Transfer Entry";
                print_report( company+'*'+rec_issue_id+'*'+report_title,'yarn_transfer_print','../yarn/requires/yarn_transfer_controller');
            }
            else
            {

                var report_title="General Item Transfer";
                print_report( company+'*'+rec_issue_id+'*'+report_title,'yarn_transfer_print','../general_store/requires/general_item_transfer_controller');
            }
        }
    }

	function openmypage_file(rcv_sys_id)
    {
        var page_link='requires/date_wise_item_recv_issue_multi_category_report_controller.php?action=show_file&rcv_sys_id='+rcv_sys_id;
        var title="File View";
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=450px,center=1,resize=0,scrolling=0','../');
    }
    function generate_knit_finish_fabric_nonorder_report(data)
    {
        var explodeData = data.split("*");
        var data = "action="+explodeData[0]+"&txt_booking_no='"+explodeData[1]+"'&cbo_company_name='"+explodeData[2]+"'&id_approved_id='"+explodeData[3]+"'&cbo_fabric_natu='"+explodeData[4]+"'&report_title=Sample Fabric Booking - Without order";
        http.open("POST","../../order/woven_order/requires/sample_booking_non_order_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = generate_knit_finish_fabric_nonorder_report_reponse;
    }

    function generate_knit_finish_fabric_nonorder_report_reponse()
    {
        if(http.readyState == 4)
        {
            var file_data=http.responseText.split('****');
            $('#pdf_file_name').html(file_data[1]);
            $('#data_panel').html(file_data[0] );
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
                '<html><head><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
            d.close();
        }
    }

</script>
</head>

  <body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />
    <form name="item_receive_issue_1" id="item_receive_issue_1" autocomplete="off" >
    <h3 align="left" id="accordion_h1" style="width:1280px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div style="width:1280px;" align="center" id="content_search_panel">
        <fieldset style="width:1280px;">
                <table class="rpt_table" width="1270" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                    	<th width="180" class="must_entry_caption">Item Category</th>
                        <th width="100" class="must_entry_caption">Company</th>
                        <th width="100" >Buyer Name</th>
                        <th width="100" >Store Name</th>
                        <th width="100" >MRR No.</th>
                        <th width="80" >Count</th>
                        <th width="80">Based On</th>
                        <th width="180" id="up_tr_date" class="must_entry_caption">Transaction Date Range</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:90px" class="formbutton" onClick="reset_field()" /></th>
                    </tr>
                </thead>
                <tr>
                    <td>
                         <input type="text" name="txt_item_process_name" id="txt_item_process_name" class="text_boxes" style="width:180px;" placeholder="Double Click To Search" onDblClick="openmypage_process();" tabindex="13" readonly />
                         <input type="hidden" name="cbo_item_cat" id="cbo_item_cat" value="" />
                    </td>
                    <td>
                            <?
                        	echo create_drop_down( "cbo_company_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/date_wise_item_recv_issue_multi_category_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/date_wise_item_recv_issue_multi_category_report_controller',this.value, 'load_drop_down_store', 'store_td' );" );
                        ?>
                    </td>
                    <td id="buyer_td"><?
                        	echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                        ?></td>
                 	<td  id="store_td"><?
                       		echo create_drop_down( "cbo_store_name", 100, $blank_array,"", 1, "-- All Store --", $selected, "", 0 );
                    ?></td>
                    <td>
                       	<input type="text" name="txt_mrr_no" id="txt_mrr_no" class="text_boxes_numeric" style="width:100px;" placeholder="Write" />
                    </td>
                    <td>
						<?
                        echo create_drop_down( "cbo_yarn_count", 80, "select id,yarn_count from lib_yarn_count where is_deleted = 0 AND status_active = 1 ORDER BY yarn_count ASC","id,yarn_count", 1, "--Select--", 0, "",0 );
                        ?>
                    </td>
                    <td>
                    	<?
						$base_on_arr=array(1=>"Transaction Date",2=>"Insert Date");
                        echo create_drop_down( "cbo_based_on", 80, $base_on_arr,"", 0, "", 1, "fn_change_base(this.value);",0 );
                        ?>
                    </td>
                    <td>
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px;" placeholder="From Date" readonly/>&nbsp; TO &nbsp;
                    	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To Date" style="width:55px;" readonly/>
                    </td>
                    <td align="center">
                        <input type="button" name="search" id="search" value="All" onClick="generate_report(1)" style="width:60px" class="formbutton" />
                        <input type="button" name="search" id="search" value="Receive" onClick="generate_report(2)" style="width:60px" class="formbutton" />
                        <input type="button" name="search" id="search" value="Issue" onClick="generate_report(3)" style="width:60px" class="formbutton" />
                        <input type="button" name="search" id="search" value="Excel(All)" onClick="generate_report_1(1)" style="width:70px" class="formbutton" />
                    </td>
                </tr>
                <tr>
                	<td colspan="8" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                	<td align="center" valign="bottom">
                        <input type="button" name="search" id="search" value="Excel(Receive)" onClick="generate_report_1(2)" style="width:90px" class="formbutton" />
                        <input type="button" name="search" id="search" value="Excel (Issue)" onClick="generate_report_1(3)" style="width:80px" class="formbutton" />
                        <input type="button" name="search" id="search" value="Raw Material Issue" onClick="generate_report_raw_material(1)" style="width:120px" class="formbutton" />
                    </td>
                    
                </tr>

            </table>
        </fieldset>

    </div>
        <!-- Result Contain Start-->
        	<div id="report_container" align="center"></div>
            <div id="report_container2"></div>
        <!-- Result Contain END-->
        <div style="display:none" id="data_panel"></div>


    </form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
