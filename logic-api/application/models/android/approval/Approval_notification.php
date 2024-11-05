<?php

class Approval_notification extends CI_Model {

	function __construct() {
		error_reporting(0);
		parent::__construct();
	}

    public function insert_fcm_token($user_id,$device_id,$fcm_token)
	{
		$insert_date = date("d-M-Y");
		$this->db->trans_begin();

		$fcm_token_data = array(
			array(
				"USER_ID" => $user_id,
				"DEVICE_ID" => $device_id,
				"FCM_TOKEN" => $fcm_token,
				"INSERT_DATE" => $insert_date,
			)
		);

        $this->db->where('USER_ID', 165);
        $this->db->delete('APPROVAL_NOTI_USER_DEVICES');
		$this->db->insert_batch("APPROVAL_NOTI_USER_DEVICES", $fcm_token_data);

		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
		}
		else
		{
			$this->db->trans_commit();
		}
	
	}

	public function get_approval_menu_by_privilege($user_id)
	{
	  
		$menu_sql = "SELECT
					a.menu_name AS MENU,
					a.f_location AS MENU_LINK,
					a.m_menu_id AS MENU_ID,
					c.user_full_name AS FULL_NAME,
					c.id AS USER_LOGIN_ID,
					a.slno AS SLNO,
                    d.is_seen AS IS_SEEN,
					COUNT(e.NOTIFI_DTLS_ID) AS NOTIFICATIONS
				FROM
					main_menu a
				INNER JOIN
					user_priv_mst b ON a.m_menu_id = b.main_menu_id
				INNER JOIN
					user_passwd c ON b.user_id = c.id
				LEFT JOIN
					APPROVAL_NOTIFICATION_MST d ON a.m_menu_id = d.M_MENU_ID 
				LEFT JOIN
					APPROVAL_NOTIFICATION_DTLS e ON d.NOTIFI_ID = e.NOTIFI_ID AND e.NOTIFI_USER = ? AND e.IS_APPROVED != 1
				WHERE
					b.user_id = ?
					AND a.status = 1
					AND a.m_module_id = 12
					AND a.root_menu > 0
				GROUP BY
					a.menu_name, a.f_location, a.m_menu_id, c.user_full_name, c.id, a.slno,d.is_seen
				ORDER BY
					a.slno ASC";

				$menu_result = $this->db->query($menu_sql, array($user_id, $user_id))->result();

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
							'IS_SEEN' => $menu->IS_SEEN
						);
					}
				}

		

		return $menu_arr;

	}

	public function get_notification_details($user_id,$menu_id)
	{
		$menu_sql = "SELECT d.entry_form,
						d.ref_id,
						d.m_menu_id,
						d.m_mod_id,
                        d.is_seen
						FROM      approval_notification_mst d
								JOIN
							approval_notification_dtls e
								ON     d.notifi_id = e.notifi_id
								AND e.notifi_user = ?
								AND e.is_approved != 1
						WHERE   d.m_menu_id = ? ";

		$menu_result = $this->db->query($menu_sql, array($user_id, $menu_id))->result();

		$menu_arr = array();

		if (!empty($menu_result))
		{
			foreach ($menu_result as $menu)
			{
                $entry_form = $menu->ENTRY_FORM;
                if($entry_form == 1) //Purchase Requisition Approval
                {
                    return $this->get_purchase_requisition_approval_data($menu->REF_ID,$menu->IS_SEEN);
                    
                }
                else if($entry_form == 2) //Yarn WO Approval
                {

                }
                else if($entry_form == 3) //Dyes/Chemical WO Approval
                {

                }
                else if($entry_form == 4) //Spare parts WO Approval
                {

                }
                else if($entry_form == 5) //Stationary WO Approval
                {

                }
                else if($entry_form == 6) //Pro-forma Invoice WO Approval
                {

                }
                else if($entry_form == 7) //Fabric Booking Approval
                {

                }
                else if($entry_form == 8) //Trims Booking Approval
                {

                }
                else if($entry_form == 9) //Sample Booking (Without Order) Approval
                {

                }
                else if($entry_form == 10) //Price Quatation Approval
                {
                    return $this->get_price_quatation_approval_data($menu->REF_ID);
             
                }
                else if($entry_form == 11) //Component Wise Precost Approval
                {

                }
                else if($entry_form == 12) //Short Fabric Booking Approval
                {

                }
                else if($entry_form == 13) //Sample Fabric Booking-With Order
                {

                }
                else if($entry_form == 14) //Yarn Delivery Approval
                {

                }
                else if($entry_form == 15) //Pre-Costing
                {

                }
                else if($entry_form == 16) //Dyeing Batch Approval
                {

                }
                else if($entry_form == 17) //Other Purchase WO Approval
                {

                }
                else if($entry_form == 19) //Gate Pass Activation Approval
                {

                }
                else if($entry_form == 20) //yarn requisition approval
                {

                }
                else if($entry_form == 21) //PI approval
                {

                }
                else if($entry_form == 22) //All approval
                {

                }
                else if($entry_form == 23) //GSD Entry Approval
                {

                }
                else if($entry_form == 24) //Fabric Sales Order Approval
                {

                }
                else if($entry_form == 25) //Sample Requisition Approval
                {

                }
                else if($entry_form == 26) //Item Issue Requisiton
                {

                }
                else if($entry_form == 27) //PI approval v2
                {

                }
                else if($entry_form == 28) //Service Booking AOP Approval
                {

                }
                else if($entry_form == 29) //Service Booking For Knitting
                {
                    
                }
                else if($entry_form == 30) //Yarn Dyeing Work Order
                {
                    
                }
                else if($entry_form == 31) //Sample Requisition with Booking
                {
                    
                }
                else if($entry_form == 32) //Embellishment Work Order Approval
                {
                    
                }
                else if($entry_form == 33) //Yarn Dyeing without Work Order
                {
                    
                }
                else if($entry_form == 34) //Price Quotation V3
                {
                    
                }
                else if($entry_form == 35) //Yarn Delivery Acknowledgement
                {
                    
                }
                else if($entry_form == 36) //Quick Costing Approval
                {
                    
                }
                else if($entry_form == 37) //Transfer Requisition Approval
                {
                    
                }
                else if($entry_form == 38) //Import Document Acceptance Approval
                {
                    
                }
                else if($entry_form == 39) //Commercial Office Note Approval
                {
                    
                }
                else if($entry_form == 40) //Transfer Requisition Approval for Sales Order
                {
                    
                }
                else if($entry_form == 41) //TNA Approval
                {
                    
                }
                else if($entry_form == 42) //Lab Test Approval
                {
                    
                }
                else if($entry_form == 43) //Yarn WO Approval Sweater
                {
                    
                }
                else if($entry_form == 44) //Topping Adding Stripping Recipe Entry
                {
                    
                }
                else if($entry_form == 45) //Quick Costing Approval [WVN]
                {
                    
                }
                else if($entry_form == 46) //Pre-Costing Approval [WVN]
                {
                    
                }
                else if($entry_form == 47) //Sourcing Post Cost Approval
                {
                    
                }
                else if($entry_form == 48) //Fabric Sales Order Approval V2
                {
                    
                }
                else if($entry_form == 49) //CS Approval [General]
                {
                    
                }
                else if($entry_form == 50) //CS Approval [Accessories]
                {
                    
                }
                else if($entry_form == 51) //Trims order rcv Approval
                {
                    
                }
                else if($entry_form == 52) //General Item Transfer Requisition Approval
                {
                    
                }
                else if($entry_form == 53) //Sample Or Additional Yarn WO Approval
                {
                    
                }
                else if($entry_form == 54) //Sample Requisition Acknowledge
                {
                    
                }
                else if($entry_form == 55) //Sample Trims Booking Without Order
                {
                    
                }
                else if($entry_form == 56) //Item Issue Requisition Approval V2
                {
                    
                }
                else if($entry_form == 57) //CS Approval [Fabrics]
                {
                    
                }
                else if($entry_form == 58) //Yarn Test
                {
                    
                }
                else if($entry_form == 59) //Gate Pass Approval
                {
                    
                }
                else if($entry_form == 60) //Service Work Order Approval
                {
                    
                }
                else if($entry_form == 61) //Service Requisition Approval
                {
                    
                }
                else if($entry_form == 62) //Export LC Approval
                {
                    
                }
                else if($entry_form == 63) //Sales Contract Approval
                {
                    
                }
                else if($entry_form == 64) //Price Quotation Approval [ Sweater]
                {
                    
                }
                else if($entry_form == 65) //Fabric Service Booking Approval
                {
                    
                }
                else if($entry_form == 66) //Erosion List for Approval
                {
                    
                }
                else if($entry_form == 67) //Multiple Job Wise Additional Trims Booking Approval
                {
                    
                }
                else if($entry_form == 68) //Garments Service Work Order Approval
                {
                    
                }
                else if($entry_form == 69) //Yarn Parking Receive/GRN Entry approval
                {
                    
                }
                else if($entry_form == 70) //Quick Costing Approval [Knit]
                {
                    
                }
                else if($entry_form == 71) //General Service Bill Approval
                {
                    
                }
                else if($entry_form == 72) //Sub Contract order Entry Approval
                {
                    
                }
                else if($entry_form == 73) //Knitting Work Order Approval
                {
                    
                }
                else if($entry_form == 74) //Dyeing Work Order Approval
                {
                    
                }
				else if($entry_form == 75) //Yarn Service Work Order Approval
                {
                    
                }
                else if($entry_form == 76) //Yarn Dyeing Sales Approval
                {
                    
                }
                else if($entry_form == 77) //Pre Costing Approval Group By
                {
                    
                }
                else if($entry_form == 78) //Lab Test Approval V2
                {
                    
                }
                else if($entry_form == 80) //Sample Requisition for Woven Textile Approval
                {
                    
                }
                else if($entry_form == 81) //Dyes N Chemical Issue Approval
                {
                    
                }
                else if($entry_form == 82) //Monthly Plan Approval
                {
                    
                }
                else if($entry_form == 83) //Buyer Inquiry for Woven Textile Acknowledge
                {
                    
                }
			}
		}
		return $menu_arr;

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

    public function get_purchase_requisition_approval_data($ref_id,$IS_SEEN = 0)
	{
        $sql = "SELECT a.id,
                        a.requ_no,
                        b.company_name,
                        c.store_name,
                        a.requisition_date,
                        d.user_full_name,
                        a.delivery_date
                FROM inv_purchase_requisition_mst  a
                        LEFT JOIN user_passwd d ON a.inserted_by = d.id
                        LEFT JOIN lib_company b ON a.company_id = b.id
                        LEFT JOIN lib_store_location c ON a.store_name = c.id
                WHERE a.id = ? ";

        $result = $this->db->query($sql, array($ref_id))->result();

        $data_arr = array();

        if (!empty($result))
        {
            foreach ($result as $row)
            {
                $desc = "Requisition Id: ".$row->ID.",\nStore: ".$row->STORE_NAME.",\nRequisition No: ".$row->REQU_NO.",\nRequisition date: ".date('d-m-Y',strtotime($row->REQUISITION_DATE)).",\nDelivery date: ".date('d-m-Y',strtotime($row->DELIVERY_DATE)).",\nInserted by : ".$row->USER_FULL_NAME;
                $data_arr[] = array(
                    'ID' => $row->ID,
                    'DATE' => $row->REQUISITION_DATE,
                    'DELIVERY_DATE' => $row->DELIVERY_DATE,
                    'COMPANY' => $row->COMPANY_NAME,
                    'BUYER' => '',
                    'SYS_NUMBER' => $row->REQU_NO,
                    'SYS_DEF' => '',
                    'DESC' => $desc,
                    'IS_SEEN' => $IS_SEEN

                );
            }
        }
        return $data_arr;
	}

    public function insert_counting($user_id)
    {
        $NOTIFI_ID = $this->getNextNotifiId('NOTIFI_ID','APPROVAL_NOTIFICATION_MST');
        $mst_data = array(
			array(
				"NOTIFI_ID" =>  $NOTIFI_ID,
				"REF_ID" => 10679,
				"ENTRY_FORM" => 1,
				"M_MENU_ID" => 2302,
				"NOTIFI_DESC" => 'Requisition Id: 10679,
                Store: General Store-DnC [ICT],
                Requisition No: OG-RQSN-23-00353,
                Requisition date: 28-08-2023,
                Delivery date: 01-01-1970,
                Inserted by : Hossain Mahmud Rana',
				"NOTIFI_USERS" => $user_id,
			)
		);
        
        $dtls_data = array(
			array(
				"NOTIFI_DTLS_ID" => $this->getNextNotifiId('NOTIFI_DTLS_ID','APPROVAL_NOTIFICATION_DTLS'),
				"NOTIFI_ID" => $NOTIFI_ID,
				"NOTIFI_USER" => $user_id,
				"IS_APPROVED" =>0
			)
		);

		$this->db->insert_batch("APPROVAL_NOTIFICATION_MST", $mst_data);
        $this->db->insert_batch("APPROVAL_NOTIFICATION_DTLS", $dtls_data);

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

    public function getNextNotifiId($column,$table) {
       
        $this->db->select_max($column);
        $query = $this->db->get($table);
        $row = $query->row();
        $nextNotifiId = $row->NOTIFI_ID + 1;
        return $nextNotifiId;
    }
}