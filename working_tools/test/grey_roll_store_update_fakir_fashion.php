<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


/*$rcv_by_batch_sql=sql_select("select a.entry_form, b.id as dtls_id, b.trans_id, b.to_trans_id
from inv_item_transfer_mst a, inv_item_transfer_dtls b 
where a.id=b.mst_id and a.entry_form in (83,110,183,180) and b.status_active=1 and b.is_deleted=0 and b.from_store =0");*/


$rcv_by_batch_sql=sql_select("select a.entry_form, b.id as dtls_id, b.trans_id, b.to_trans_id, c.prod_id
from inv_item_transfer_mst a, inv_item_transfer_dtls b left join inv_transaction c on b.trans_id = c.id
where a.id=b.mst_id and a.entry_form in (83,110,183,180) and b.status_active=1 and b.is_deleted=0 and b.from_store =0");

if(empty($rcv_by_batch_sql))
{
    echo "Mismatch Not Found";
    die;
}


//110 => "Roll wise Grey Fabric Order To Sample Transfer Entry"
//180 => "Roll Wise Grey Fabric Sample To Sample Transfer Entry"
//183 => "Roll Wise Grey Fabric Sample To Order Transfer Entry"
//83 => "Roll wise Grey Fabric Order To Order Transfer Entry"


//update inv_transaction set floor_id=1869, room=1870,rack=1871,self=1872 where store_id=28;    SAMPE

//update inv_transaction set floor_id=1856, room=1857,rack=1858,self=1859 where store_id=2;     BULK


foreach($rcv_by_batch_sql as $val)
{
    if($val[csf("entry_form")]==83)
    {
        if($val[csf("dtls_id")]){
            //echo "update inv_item_transfer_dtls set from_store=2, floor_id=1856, room=1857,  rack=1858,  shelf=1859, to_store=2, to_floor_id=1856, to_room=1857,  to_rack=1858,  to_shelf=1859 where id=".$val[csf("dtls_id")]." <br>";

            execute_query("update inv_item_transfer_dtls set from_store=2, floor_id=1856, room=1857,  rack=1858,  shelf=1859, to_store=2, to_floor_id=1856, to_room=1857,  to_rack=1858,  to_shelf=1859 where id=".$val[csf("dtls_id")],0);
        }

        if($val[csf("trans_id")]){
            //echo "update inv_transaction set floor_id=1856, room=1857,rack=1858, self=1859, store_id=2 where id=".$val[csf("trans_id")]." <br>";

            execute_query("update inv_transaction set floor_id=1856, room=1857,rack=1858, self=1859, store_id=2 where id=".$val[csf("trans_id")],0);
        }
        if($val[csf("to_trans_id")]){
            //echo "update inv_transaction set floor_id=1856, room=1857,rack=1858, self=1859, store_id=2 where id=".$val[csf("to_trans_id")]." <br>";

            execute_query("update inv_transaction set floor_id=1856, room=1857,rack=1858, self=1859, store_id=2 where id=".$val[csf("to_trans_id")],0);
        }

        
    }
    else if($val[csf("entry_form")]==110)
    {
        if($val[csf("dtls_id")]){
            //echo "update inv_item_transfer_dtls set from_store=2, floor_id=1856, room=1857,  rack=1858,  shelf=1859, to_store=28, to_floor_id=1869, to_room=1870,  to_rack=1871, to_shelf=1872 where id=".$val[csf("dtls_id")]." <br>";

            execute_query("update inv_item_transfer_dtls set from_store=2, floor_id=1856, room=1857,  rack=1858,  shelf=1859, to_store=28, to_floor_id=1869, to_room=1870,  to_rack=1871, to_shelf=1872 where id=".$val[csf("dtls_id")],0);
        }

        if($val[csf("trans_id")]){
            //echo "update inv_transaction set floor_id=1856, room=1857, rack=1858, self=1859, store_id=2 where id=".$val[csf("trans_id")]." <br>";

            execute_query("update inv_transaction set floor_id=1856, room=1857, rack=1858, self=1859, store_id=2 where id=".$val[csf("trans_id")],0);
        }
        if($val[csf("to_trans_id")]){
            //echo "update inv_transaction set floor_id=1869, room=1870, rack=1871, self=1872, store_id=28 where id=".$val[csf("to_trans_id")]." <br>";

            execute_query("update inv_transaction set floor_id=1869, room=1870, rack=1871, self=1872, store_id=28 where id=".$val[csf("to_trans_id")],0);
        }
    }
    else if($val[csf("entry_form")]==183)
    {
        if($val[csf("dtls_id")]){
            //echo "update inv_item_transfer_dtls set from_store=28, floor_id=1869, room=1870,  rack=1871,  shelf=1872, to_store=2, to_floor_id=1856, to_room=1857,  to_rack=1858, to_shelf=1859 where id=".$val[csf("dtls_id")]." <br>";

            execute_query("update inv_item_transfer_dtls set from_store=28, floor_id=1869, room=1870,  rack=1871,  shelf=1872, to_store=2, to_floor_id=1856, to_room=1857,  to_rack=1858, to_shelf=1859 where id=".$val[csf("dtls_id")],0);
        }

        if($val[csf("trans_id")]){
            //echo "update inv_transaction set floor_id=1869, room=1870,rack=1871,self=1872, store_id=28 where id=".$val[csf("trans_id")]." <br>";

            execute_query("update inv_transaction set floor_id=1869, room=1870,rack=1871,self=1872, store_id=28 where id=".$val[csf("trans_id")],0);
        }

        if($val[csf("to_trans_id")]){
            //echo "update inv_transaction set floor_id=1856, room=1857,rack=1858,self=1859, store_id=2 where id=".$val[csf("to_trans_id")]." <br>";

            execute_query("update inv_transaction set floor_id=1856, room=1857,rack=1858,self=1859, store_id=2 where id=".$val[csf("to_trans_id")],0);
        }
        
    }
    else if($val[csf("entry_form")]==180)
    {
        if($val[csf("dtls_id")]){
            //echo "update inv_item_transfer_dtls set from_store=28, floor_id=1869, room=1870,  rack=1871,  shelf=1872, to_store=28, to_floor_id=1869, to_room=1870,  to_rack=1871, to_shelf=1872 where id=".$val[csf("dtls_id")]." <br>";

            execute_query("update inv_item_transfer_dtls set from_store=28, floor_id=1869, room=1870,  rack=1871,  shelf=1872, to_store=28, to_floor_id=1869, to_room=1870,  to_rack=1871, to_shelf=1872 where id=".$val[csf("dtls_id")],0);
        }

        if($val[csf("trans_id")]){
            //echo "update inv_transaction set floor_id=1869, room=1870,rack=1871,self=1872, store_id=28 where id=".$val[csf("trans_id")]." <br>";

            execute_query("update inv_transaction set floor_id=1869, room=1870,rack=1871,self=1872, store_id=28 where id=".$val[csf("trans_id")],0);
        }
        
        if($val[csf("to_trans_id")]){
            //echo "update inv_transaction set floor_id=1869, room=1870,rack=1871,self=1872, store_id=28 where id=".$val[csf("to_trans_id")]." <br>";

            execute_query("update inv_transaction set floor_id=1869, room=1870,rack=1871,self=1872, store_id=28 where id=".$val[csf("to_trans_id")],0);
        }
    }


    //execute_query("update pro_grey_batch_dtls set body_part_id = $body_part_id where id =".$val[csf("dtls_id")],0);
    
}


oci_commit($con);
echo "Success"; 
die;


?>