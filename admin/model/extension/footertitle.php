<?php
class ModelExtensionfootertitle extends Model {
	public function addfootertitle($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "footertitle SET  status = '" . (int)$data['status'] . "', sort_order = '" . (int)$data['sort_order'] . "'");

		$footertitle_id = $this->db->getLastId(); 
		
		foreach ($data['footertitle_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "footertitle_description SET footertitle_id = '" . (int)$footertitle_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "'");
		}

		$this->cache->delete('footertitle');
	}
	
	public function editfootertitle($footertitle_id, $data) {
		
		$this->db->query("UPDATE " . DB_PREFIX . "footertitle SET status = '" . (int)$data['status'] . "', sort_order = '" . (int)$data['sort_order'] . "' WHERE footertitle_id = '" . (int)$footertitle_id . "'");
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "footertitle_description WHERE footertitle_id = '" . (int)$footertitle_id . "'");
		
		foreach ($data['footertitle_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "footertitle_description SET footertitle_id = '" . (int)$footertitle_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "'");
		}
		
		$this->cache->delete('footertitle');
	}
	
	public function deletefootertitle($footertitle_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "footertitle WHERE footertitle_id = '" . (int)$footertitle_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "footertitle_description WHERE footertitle_id = '" . (int)$footertitle_id . "'");
		
		

		$this->cache->delete('footertitle');
	}	
	public function getfootertitle($footertitle_id) {
	
			$sql = "SELECT * FROM " . DB_PREFIX . "footertitle where footertitle_id='".$footertitle_id."' ";
			$query = $this->db->query($sql);
			return $query->row;
		
	}
		

	
	public function getfootertitles($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "footertitle i LEFT JOIN " . DB_PREFIX . "footertitle_description id ON (i.footertitle_id = id.footertitle_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		
			$sort_data = array(
				'id.title',
				'i.link'
			);		
		
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];	
			} else {
				$sql .= " ORDER BY id.title";	
			}
			
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}
		
			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}		

				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}	
			
				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}	
			
			$query = $this->db->query($sql);
			
			return $query->rows;
		} else {
			$footertitle_data = $this->cache->get('footertitle.' . (int)$this->config->get('config_language_id'));
		
			if (!$footertitle_data) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "footertitle i LEFT JOIN " . DB_PREFIX . "footertitle_description id ON (i.footertitle_id = id.footertitle_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY id.title");
	
				$footertitle_data = $query->rows;
			
				$this->cache->set('footertitle.' . (int)$this->config->get('config_language_id'), $footertitle_data);
			}	
	
			return $footertitle_data;			
		}
	}
	
	public function getfootertitleDescriptions($footertitle_id) {
		$footertitle_description_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "footertitle_description WHERE footertitle_id = '" . (int)$footertitle_id . "'");

		foreach ($query->rows as $result) {
			$footertitle_description_data[$result['language_id']] = array(
				'title'       => $result['title'],
				);
		}
		
		return $footertitle_description_data;
	}
	
	
		
	public function getTotalfootertitles() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "footertitle");
		
		return $query->row['total'];
	}	
	
	
}
?>