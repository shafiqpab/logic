<?php

class Android_model extends CI_Model
{
	private $db_connection;

	function __construct()
	{
		parent::__construct();
		$this->db_connection = $this->db->dbdriver;
	}

	public function login($user_id, $password)
	{
		$this->db->select("user_passwd.*");
		$this->db->from("user_passwd");
		$this->db->where("USER_NAME", "$user_id");
		$this->db->where("PASSWORD", "$password");
		$query = $this->db->get();
		if ($query->num_rows() == 1) {
			$user_info = $query->row();
			return $this->get_menu_by_privilege($user_info->ID);
		} else {
			return false;
		}
	}

	public function get_menu_by_privilege($user_id)
	{
		$sql = "select a.m_page_name page, a.m_page_short_name page_short_name,a.menu_name menu,a.m_menu_id menu_id, b.show_priv,b.save_priv,b.edit_priv, b.delete_priv, b.approve_priv,c.user_full_name full_name,c.user_name user_login_id,c.department_id department_name,c.unit_id unit_name,c.designation designation,c.buyer_id from main_menu a, user_priv_mst b,user_passwd c where b.user_id=? and a.m_menu_id=b.main_menu_id and b.user_id=c.id and a.is_mobile_menu=1 and a.status=1 order by main_menu_id asc";
		return $query = $this->db->query($sql, array($user_id))->result_array();
	}

	public function get_chart_by_ordership($company = 0, $location = 0)
	{
		$company_cond = $location_cond = "";
		if ($company != 0) {
			$company_cond = " and c.company_name=?";
		}
		if ($location != 0) {
			$location_cond = " and c.location_name=?";
		}

		if ($this->db_connection == "mysqli") {
			$sql = "select c.COMPANY_NAME COMPANY_ID,d.COMPANY_NAME COMPANY,a.country_ship_date as shipment_date,sum(a.order_total) AS POVALUE, sum(a.order_quantity) AS POQTY
					from wo_po_color_size_breakdown a, wo_po_break_down b, wo_po_details_master c, lib_company d 
					where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.COMPANY_NAME=d.id and a.is_deleted=0 and a.status_active=1 $company_cond $location_cond 
					and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and c.company_name='3' and year(a.country_ship_date) 
					between (year(a.country_ship_date)-4) and (year(a.country_ship_date)+3) group by c.company_name,year(a.country_ship_date) 
					order by year(a.country_ship_date)";
		} else {
			$sql = "select c.COMPANY_NAME COMPANY_ID,d.COMPANY_NAME COMPANY,to_char(a.country_ship_date,'YYYY') as shipment_date,sum(a.order_total) AS POVALUE, 
					sum(a.order_quantity) AS POQTY
					from wo_po_color_size_breakdown a, wo_po_break_down b, wo_po_details_master c, lib_company d
					where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.COMPANY_NAME=d.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 
					and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $company_cond $location_cond and to_char(a.country_ship_date,'YYYY') 
					between (to_char(sysdate, 'YYYY')-4) and (to_char(sysdate, 'YYYY')+3) group by to_char(a.country_ship_date,'YYYY'),d.COMPANY_NAME, c.company_name 
					order by to_char(a.country_ship_date,'YYYY')";
		}
		return $this->db->query($sql, array($company, $location))->result();
	}

	public function get_chart_by_ontime_delivery()
	{
		$ex_sql = "select b.EX_FACTORY_DATE, b.COUNTRY_ID, b.PO_BREAK_DOWN_ID,
        sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as EX_FACTORY_QNTY,
        sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as EX_FACTORY_RETURN_QNTY
        from  pro_ex_factory_mst b  where  b.status_active=1 and b.is_deleted=0 
        group by b.ex_factory_date, b.country_id, b.po_break_down_id
        order by b.po_break_down_id";
		$ex_res = $this->db->query($ex_sql)->result();
		$ex_arr = array();
		foreach ($ex_res as $row) {
			if ($row->PO_BREAK_DOWN_ID && $row->COUNTRY_ID) {
				$ex_arr[$row->PO_BREAK_DOWN_ID][$row->COUNTRY_ID]["EX_QTY"] = $row->EX_FACTORY_QNTY - $row->EX_FACTORY_RETURN_QNTY;
				$ex_arr[$row->PO_BREAK_DOWN_ID][$row->COUNTRY_ID]["EX_DATE"] = $row->EX_FACTORY_DATE;
			}
		}
		if ($this->db_connection == "mysqli") {
			$ship_sql = "select a.COUNTRY_ID, a.PO_BREAK_DOWN_ID, a.COUNTRY_SHIP_DATE,SUM(a.ORDER_QUANTITY) AS ORDER_QNTY
							from wo_po_color_size_breakdown a
							where a.country_ship_date >= date_add(sysdate(), interval -12 month) 
							and a.country_ship_date < sysdate()
							group by  a.country_id, a.po_break_down_id, a.country_ship_date
							order by a.po_break_down_id,a.country_ship_date";
		} else {
			$ship_sql = "select A.COUNTRY_ID, A.PO_BREAK_DOWN_ID, A.COUNTRY_SHIP_DATE,SUM(A.ORDER_QUANTITY) AS ORDER_QNTY
							from wo_po_color_size_breakdown a
							where a.country_ship_date >= add_months(sysdate,-12) and a.country_ship_date < add_months(sysdate,0)
							group by  a.country_id, a.po_break_down_id, a.country_ship_date
							order by a.po_break_down_id,a.country_ship_date";
		}
		$ship_res = $this->db->query($ship_sql)->result();
		$data_arr = array();
		$ship_arr = array();
		foreach ($ship_res as $row) {
			if (isset($row->PO_BREAK_DOWN_ID) && isset($row->COUNTRY_ID)) {
				if (isset($ex_arr[$row->PO_BREAK_DOWN_ID][$row->COUNTRY_ID]["EX_QTY"])) {
					$ex_qny = $ex_arr[$row->PO_BREAK_DOWN_ID][$row->COUNTRY_ID]["EX_QTY"];
					$ex_date = $ex_arr[$row->PO_BREAK_DOWN_ID][$row->COUNTRY_ID]["EX_DATE"];
					$date_diff = strtotime($ex_date) - strtotime($row->COUNTRY_SHIP_DATE);
					if ($row->COUNTRY_SHIP_DATE) {
						if ($date_diff <= 0) {
							$ship_arr[$row->PO_BREAK_DOWN_ID][$row->COUNTRY_ID]["EX_QNY"] = $ex_qny;
							$ship_arr[$row->PO_BREAK_DOWN_ID][$row->COUNTRY_ID]["SH_DATE"] = $row->COUNTRY_SHIP_DATE;

							if (isset($ship_arr[$row->PO_BREAK_DOWN_ID][$row->COUNTRY_ID]["SH_QTY"])) {
								$ship_arr[$row->PO_BREAK_DOWN_ID][$row->COUNTRY_ID]["SH_QTY"] += $row->ORDER_QNTY;
							} else {
								$ship_arr[$row->PO_BREAK_DOWN_ID][$row->COUNTRY_ID]["SH_QTY"] = $row->ORDER_QNTY;
							}
						}
					}
				}
			}
		}

		foreach ($ship_arr as $po_id => $country_id) {
			foreach ($country_id as $value) {
				$data_arr[] = array("year" => date("Y-m", strtotime($value["SH_DATE"])), "value" => ($value["EX_QNY"] * 100) / $value["SH_QTY"]);
			}
		}
		return $data_arr;
	}

}
