<?php
class ModelExtensionOCFilterPage extends Model {
	public function addPage($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_page SET keyword = '" . $this->db->escape($data['keyword']) . "', params = '" . $this->db->escape(trim($data['params'], '/')) . "', category_id = '" . (int)$data['category_id'] . "', status = '" . (int)$data['status'] . "'");

		$ocfilter_page_id = $this->db->getLastId();

		foreach ($data['page_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_page_description SET ocfilter_page_id = '" . (int)$ocfilter_page_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		$this->cache->delete('ocfilter.page');

		return $ocfilter_page_id;
	}

	public function editPage($ocfilter_page_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "ocfilter_page SET keyword = '" . $this->db->escape($data['keyword']) . "', params = '" . $this->db->escape(trim($data['params'], '/')) . "', category_id = '" . (int)$data['category_id'] . "', status = '" . (int)$data['status'] . "' WHERE ocfilter_page_id = '" . (int)$ocfilter_page_id . "'");

    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_page_description WHERE ocfilter_page_id = '" . (int)$ocfilter_page_id . "'");

		foreach ($data['page_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_page_description SET ocfilter_page_id = '" . (int)$ocfilter_page_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		$this->cache->delete('ocfilter.page');
	}

	public function deletePage($ocfilter_page_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_page WHERE ocfilter_page_id = '" . (int)$ocfilter_page_id . "'");
    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_page_description WHERE ocfilter_page_id = '" . (int)$ocfilter_page_id . "'");

		$this->cache->delete('ocfilter.page');
	}

	public function getPage($ocfilter_page_id) {
		$query = $this->db->query("SELECT op.*, opd.*, (SELECT GROUP_CONCAT(cd1.name ORDER BY level SEPARATOR ' > ') FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id) WHERE cp.category_id = op.category_id AND cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY cp.category_id) AS path FROM " . DB_PREFIX . "ocfilter_page op LEFT JOIN " . DB_PREFIX . "ocfilter_page_description opd ON (op.ocfilter_page_id = opd.ocfilter_page_id) WHERE op.ocfilter_page_id = '" . (int)$ocfilter_page_id . "' AND opd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getPages($data = array()) {
		$sql = "SELECT op.*, opd.title, (SELECT cd.name FROM " . DB_PREFIX . "category_description cd WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cd.category_id = op.category_id) AS category FROM " . DB_PREFIX . "ocfilter_page op LEFT JOIN " . DB_PREFIX . "ocfilter_page_description opd ON (op.ocfilter_page_id = opd.ocfilter_page_id) WHERE opd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

    if (isset($data['filter_category_id']) && !is_null($data['filter_category_id'])) {
		  $sql .= " AND op.category_id = '" . (int)$data['filter_category_id'] . "'";
    }

    if (!empty($data['filter_title'])) {
		  $sql .= " AND LCASE(opd.title) LIKE '" . $this->db->escape(utf8_strtolower($data['filter_title'])). "%'";
    }

		$sql .= " ORDER BY op.category_id, opd.title ASC";

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
	}

	public function getTotalPages() {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "ocfilter_page op LEFT JOIN " . DB_PREFIX . "ocfilter_page_description opd ON (op.ocfilter_page_id = opd.ocfilter_page_id) WHERE opd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

    if (isset($data['filter_category_id']) && !is_null($data['filter_category_id'])) {
		  $sql .= " AND op.category_id = '" . (int)$data['filter_category_id'] . "'";
    }

    if (!empty($data['filter_title'])) {
		  $sql .= " AND LCASE(opd.title) LIKE '" . $this->db->escape(utf8_strtolower($data['filter_title'])). "%'";
    }

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getPageDescriptions($ocfilter_page_id) {
		$page_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ocfilter_page_description WHERE ocfilter_page_id = '" . (int)$ocfilter_page_id . "'");

		foreach ($query->rows as $result) {
			$page_description_data[$result['language_id']] = $result;
		}

		return $page_description_data;
	}

  public function getUrlAlias($keyword) {
    $url_alias_info = array();

    $query = $this->db->query("SELECT keyword, CONCAT('option_id=', option_id) AS `query` FROM " . DB_PREFIX . "ocfilter_option WHERE LCASE(keyword) = '" . $this->db->escape(utf8_strtolower($keyword)) . "'");

    if (!$query->num_rows) {
      $query = $this->db->query("SELECT keyword, CONCAT('value_id=', value_id) AS `query` FROM " . DB_PREFIX . "ocfilter_option_value WHERE LCASE(keyword) = '" . $this->db->escape(utf8_strtolower($keyword)) . "'");
    }

    if (!$query->num_rows) {
      $query = $this->db->query("SELECT keyword, CONCAT('ocfilter_page_id=', ocfilter_page_id) AS `query` FROM " . DB_PREFIX . "ocfilter_page WHERE LCASE(keyword) = '" . $this->db->escape(utf8_strtolower($keyword)) . "'");
    }

    if ($query->num_rows) {
      $url_alias_info = $query->row;
    }

    return $url_alias_info;
  }

  public function translit($string) {
    $replace = array(
      'а' => 'a', 'б' => 'b',
      'в' => 'v', 'г' => 'g', 'ґ' => 'g', 'д' => 'd', 'е' => 'e',
      'є' => 'je', 'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
      'і' => 'i', 'ї' => 'ji', 'й' => 'j', 'к' => 'k', 'л' => 'l',
      'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r',
      'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h',
      'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ъ' => '',
      'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'ju', 'я' => 'ja',

  		' ' => '-', '+' => 'plus'
    );

    $string = mb_strtolower($string, 'UTF-8');
    $string = strtr($string, $replace);
    $string = preg_replace('![^a-zа-яёйъ0-9]+!isu', '-', $string);
  	$string = preg_replace('!\-{2,}!si', '-', $string);

  	return $string;
  }
}
?>