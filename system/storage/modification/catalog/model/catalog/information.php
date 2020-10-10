<?php
class ModelCatalogInformation extends Model {
	public function getInformation($information_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id) WHERE i.information_id = '" . (int)$information_id . "' AND id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND i.status = '1'");

		return $query->row;
	}


				public function getFotterTitle(){
			$data=array();
			$query =$this->db->query("SELECT ftd.title,ft.footertitle_id FROM " . DB_PREFIX . "footertitle ft LEFT JOIN " . DB_PREFIX . "footertitle_description ftd ON (ft.footertitle_id = ftd.footertitle_id) where ftd.language_id = '" . (int)$this->config->get('config_language_id') . "' and ft.status=1 order by ft.sort_order");



			foreach($query->rows as $row){
				$query2 = $this->db->query("SELECT * FROM " . DB_PREFIX . "footerlink f LEFT JOIN " . DB_PREFIX . "footerlink_description fd ON (f.footerlink_id = fd.footerlink_id) where fd.language_id = '" . (int)$this->config->get('config_language_id') . "' and f.status=1 and f.selectheading='".$row['footertitle_id']."' order by f.sort_order");
				$subtitle=array();
				foreach($query2->rows as $row2){
					$subtitle[]=array('title' => $row2['title'], 'link' =>$row2['link']);
				}

				$data[]=array(
					'title' => $row['title'],
					'sub_title' => $subtitle
				);
			}

			return $data;
		}
	public function getInformations() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND i.status = '1' ORDER BY i.sort_order, LCASE(id.title) ASC");

		return $query->rows;
	}

	public function getInformationLayoutId($information_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_to_layout WHERE information_id = '" . (int)$information_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return (int)$query->row['layout_id'];
		} else {
			return 0;
		}
	}
}