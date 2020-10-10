<?php
class ModelExtensionOCFilter extends Model {
  public function addOption($data) {
    $this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_option SET status = '" . (isset($data['status']) ? (int)$data['status'] : 0) . "', sort_order = '" . (int)$data['sort_order'] . "', type = '" . $this->db->escape($data['type']) . "', selectbox = '" . (isset($data['selectbox']) ? (int)$data['selectbox'] : 0) . "', color = '" . (isset($data['color']) ? (int)$data['color'] : 0) . "', image = '" . (isset($data['image']) ? (int)$data['image'] : 0) . "'");

    $option_id = $this->db->getLastId();

    foreach ($data['ocfilter_option_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_option_description SET option_id = '" . (int)$option_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', postfix = '" . $this->db->escape($value['postfix']) . "'");
		}

    if (isset($data['category_id'])) {
			foreach ($data['category_id'] as $category_id => $name) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_option_to_category SET option_id = '" . (int)$option_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		if (isset($data['option_store'])) {
			foreach ($data['option_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_option_to_store SET option_id = '" . (int)$option_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		if (isset($data['ocfilter_option_value']['insert'])) {
			foreach ($data['ocfilter_option_value']['insert'] as $value) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_option_value SET option_id = '" . (int)$option_id . "', sort_order = '" . (int)$value['sort_order'] . "', `keyword` = '" . $this->db->escape($this->translit($value['language'][$this->config->get('config_language_id')]['name'])) . "', color = '" . $this->db->escape($value['color']) . "', image = '" . $this->db->escape($value['image']) . "'");

				$value_id = $this->db->getLastId();

        foreach ($value['language'] as $language_id => $language) {
				  $this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_option_value_description SET value_id = '" . (string)$value_id . "', option_id = '" . (int)$option_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($language['name']) . "'");
				}
			}
		}

    if (empty($data['keyword'])) {
    	$data['keyword'] = $this->translit($data['ocfilter_option_description'][$this->config->get('config_language_id')]['name']);
    }

    $this->db->query("UPDATE " . DB_PREFIX . "ocfilter_option SET `keyword` = '" . $this->db->escape($data['keyword']) . "' WHERE option_id = '" . (int)$option_id . "'");

    $this->cache->delete('ocfilter');
    $this->cache->delete('product');
  }

  public function editOption($option_id, $data) {
    $this->db->query("UPDATE " . DB_PREFIX . "ocfilter_option SET status = '" . (isset($data['status']) ? (int)$data['status'] : 0) . "', sort_order = '" . (int)$data['sort_order'] . "', type = '" . $this->db->escape($data['type']) . "', selectbox = '" . (isset($data['selectbox']) ? (int)$data['selectbox'] : 0) . "', color = '" . (isset($data['color']) ? (int)$data['color'] : 0) . "', image = '" . (isset($data['image']) ? (int)$data['image'] : 0) . "' WHERE option_id = '" . (int)$option_id . "'");

    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_option_description WHERE option_id = '" . (int)$option_id . "'");

    foreach ($data['ocfilter_option_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_option_description SET option_id = '" . (int)$option_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', postfix = '" . $this->db->escape($value['postfix']) . "'");
		}

    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_option_to_category WHERE option_id = '" . (int)$option_id . "'");

    if (isset($data['category_id'])) {
			foreach ($data['category_id'] as $category_id => $name) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_option_to_category SET option_id = '" . (int)$option_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_option_to_store WHERE option_id = '" . (int)$option_id . "'");

		if (isset($data['option_store'])) {
			foreach ($data['option_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_option_to_store SET option_id = '" . (int)$option_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_option_value WHERE option_id = '" . (int)$option_id . "'");
    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_option_value_description WHERE option_id = '" . (int)$option_id . "'");

    if (isset($data['ocfilter_option_value'])) {
			if (isset($data['ocfilter_option_value']['update'])) {
				foreach ($data['ocfilter_option_value']['update'] as $value_id => $value) {
	        $this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_option_value SET option_id = '" . (int)$option_id . "', value_id = '" . (string)$value_id . "', sort_order = '" . (int)$value['sort_order'] . "', `keyword` = '" . $this->db->escape($this->translit($value['language'][$this->config->get('config_language_id')]['name'])) . "', color = '" . $this->db->escape($value['color']) . "', image = '" . $this->db->escape($value['image']) . "'");

	        foreach ($value['language'] as $language_id => $language) {
					  $this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_option_value_description SET value_id = '" . (string)$value_id . "', option_id = '" . (int)$option_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($language['name']) . "'");
					}
				}
			}

			if (isset($data['ocfilter_option_value']['insert'])) {
				foreach ($data['ocfilter_option_value']['insert'] as $value) {
	        $this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_option_value SET option_id = '" . (int)$option_id . "', sort_order = '" . (int)$value['sort_order'] . "', `keyword` = '" . $this->db->escape($this->translit($value['language'][$this->config->get('config_language_id')]['name'])) . "', color = '" . $this->db->escape($value['color']) . "', image = '" . $this->db->escape($value['image']) . "'");

					$value_id = $this->db->getLastId();

	        foreach ($value['language'] as $language_id => $language) {
					  $this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_option_value_description SET value_id = '" . (string)$value_id . "', option_id = '" . (int)$option_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($language['name']) . "'");
					}
				}
			}
    }

    if (empty($data['keyword'])) {
    	$data['keyword'] = $this->translit($data['ocfilter_option_description'][$this->config->get('config_language_id')]['name']);
    }

    $this->db->query("UPDATE " . DB_PREFIX . "ocfilter_option SET `keyword` = '" . $this->db->escape($data['keyword']) . "' WHERE option_id = '" . (int)$option_id . "'");

    if ($data['type'] == 'slide' || $data['type'] == 'slide_dual') {
      $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ocfilter_option_value_description WHERE option_id = '" . (int)$option_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

      foreach ($query->rows as $result) {
        $slide_value = str_replace(',', '.', $result['name']);
        $slide_value = trim(preg_replace('/[^0-9\.\-]+/s', '', $slide_value));

        if (is_numeric($slide_value)) {
  		    $this->db->query("UPDATE IGNORE " . DB_PREFIX . "ocfilter_option_value_to_product SET value_id = '0', slide_value_min = '" . (float)$slide_value . "', slide_value_max = '" . (float)$slide_value . "' WHERE option_id = '" . (int)$result['option_id'] . "' AND value_id = '" . (string)$result['value_id'] . "'");
        }
      }
    }

    $this->cache->delete('ocfilter');
    $this->cache->delete('product');
  }

  public function deleteOption($option_id) {
    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_option WHERE option_id = '" . (int)$option_id . "'");
    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_option_description WHERE option_id = '" . (int)$option_id . "'");
    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_option_to_category WHERE option_id = '" . (int)$option_id . "'");
    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_option_to_store WHERE option_id = '" . (int)$option_id . "'");
    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_option_value WHERE option_id = '" . (int)$option_id . "'");
    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_option_value_description WHERE option_id = '" . (int)$option_id . "'");
    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_option_value_to_product WHERE option_id = '" . (int)$option_id . "'");

    $this->cache->delete('ocfilter');
    $this->cache->delete('product');
  }

  public function getOption($option_id) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ocfilter_option oo LEFT JOIN " . DB_PREFIX . "ocfilter_option_description ood ON (oo.option_id = ood.option_id) WHERE oo.option_id = '" . (int)$option_id . "'");

    return $query->row;
  }

  public function getOptionByCategoriesId($categories_id) {

    $data = array();

    foreach ($categories_id as $category_id) $data[] = (int)$category_id;

    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ocfilter_option oo LEFT JOIN " . DB_PREFIX . "ocfilter_option_description ood ON (ood.option_id = oo.option_id) LEFT JOIN " . DB_PREFIX . "ocfilter_option_to_category oo2c ON (oo.option_id = oo2c.option_id) WHERE oo2c.category_id IN (" . implode(',', $data) . ") AND ood.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY oo.sort_order");

    return $query->rows;
  }

  public function getOptionsByCategoryId($category_id) {
    $options_data = array();

    $options_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ocfilter_option oo LEFT JOIN " . DB_PREFIX . "ocfilter_option_description ood ON (oo.option_id = ood.option_id) LEFT JOIN " . DB_PREFIX . "ocfilter_option_to_category cotc ON (oo.option_id = cotc.option_id) WHERE oo.status = '1' AND cotc.category_id = '" . (int)$category_id . "' AND ood.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY oo.sort_order");

    if ($options_query->num_rows) {
      $options_id = array();

      foreach ($options_query->rows as $option) $options_id[] = (int)$option['option_id'];

      $values_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ocfilter_option_value oov LEFT JOIN " . DB_PREFIX . "ocfilter_option_value_description oovd ON (oov.value_id = oovd.value_id) WHERE oov.option_id IN (" . implode(',', $options_id) . ") AND oovd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY oov.sort_order, ABS(oovd.name)");

      $values = array();

      foreach ($values_query->rows as $value) $values[$value['option_id']][] = $value;

      foreach ($options_query->rows as $option) {
        $options_data[$option['option_id']] = $option;
        $options_data[$option['option_id']]['values'] = array();

        if (isset($values[$option['option_id']])) {
          $options_data[$option['option_id']]['values'] = $values[$option['option_id']];
        }
      }
    }

    return $options_data;
  }

  public function getOptionCategories($option_id) {
    $option_category_data = array();

    $query = $this->db->query("SELECT oo2c.category_id, cd.name FROM " . DB_PREFIX . "ocfilter_option_to_category oo2c LEFT JOIN " . DB_PREFIX . "category_description cd ON (oo2c.category_id = cd.category_id) WHERE oo2c.option_id = '" . (int)$option_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

    foreach ($query->rows as $result) {
      $option_category_data[$result['category_id']] = $result['name'];
    }

    return $option_category_data;
  }

  public function getOptionStores($option_id) {
		$option_store_data = array();

		$query = $this->db->query("SELECT store_id FROM " . DB_PREFIX . "ocfilter_option_to_store WHERE option_id = '" . (int)$option_id . "'");

		foreach ($query->rows as $result) { $option_store_data[] = $result['store_id']; }

		return $option_store_data;
	}

  public function getProductValues($product_id) {
    $product_values_data = array();

    $query = $this->db->query("SELECT oov2p.*, oov2pd.description, oov2pd.language_id FROM " . DB_PREFIX . "ocfilter_option_value_to_product oov2p LEFT OUTER JOIN " . DB_PREFIX . "ocfilter_option_value_to_product_description oov2pd ON (oov2pd.product_id = oov2p.product_id AND oov2pd.option_id = oov2p.option_id AND oov2pd.value_id = oov2p.value_id) WHERE oov2p.product_id = '" . (int)$product_id . "'");

    $description = array();

    $this->load->model('localisation/language');

    $languages = $this->model_localisation_language->getLanguages();

    foreach ($query->rows as $result) {
      if ($result['language_id'] && $result['description']) {
        $description[$result['option_id'] . $result['value_id']][$result['language_id']] = array(
          'description' => $result['description']
        );
      } else {
        foreach ($languages as $language) {
          $description[$result['option_id'] . $result['value_id']][$language['language_id']] = array(
            'description' => ''
          );
        }
      }
    }

    foreach ($query->rows as $result) {
      unset($result['language_id']);
      unset($result['description']);

      $product_values_data[$result['option_id']][$result['value_id']] = $result;
      $product_values_data[$result['option_id']][$result['value_id']]['description'] = $description[$result['option_id'] . $result['value_id']];
    }

    return $product_values_data;
  }

  public function getOptionValues($option_id) { # In option form and product callback
    $value_data = array();

    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ocfilter_option_value oov LEFT JOIN " . DB_PREFIX . "ocfilter_option_value_description oovd ON (oovd.value_id = oov.value_id) WHERE oov.option_id = '" . (int)$option_id . "' ORDER BY oov.sort_order, ABS(oovd.name)");

		$results = array();

		foreach ($query->rows as $row) {
      $results[$row['value_id']][] = $row;
		}

    foreach ($results as $key => $values) {
			$value = array_shift($values);

			$value_description = array();

			foreach ($results[$value['value_id']] as $result) {
				$value_description[$result['language_id']] = array(
          'name' => $result['name']
        );
			}

      $value_data[$key] = $value;
      $value_data[$key]['language'] = $value_description;
    }

		return $value_data;
  }

	public function getOptionDescriptions($option_id) {
		$option_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ocfilter_option_description WHERE option_id = '" . (int)$option_id . "'");

		foreach ($query->rows as $result) {
			$option_description_data[$result['language_id']] = array(
        'name'        => $result['name'],
        'description' => $result['description'],
        'postfix'     => $result['postfix']
      );
		}

		return $option_description_data;
	}

  public function getOptions($data = array()) { # In options list
    $option_data = array();

    $sql = "SELECT * FROM " . DB_PREFIX . "ocfilter_option oo LEFT JOIN " . DB_PREFIX . "ocfilter_option_description ood ON (oo.option_id = ood.option_id)";

    if (!empty($data['filter_category_id'])) {
      $sql .= " LEFT JOIN " . DB_PREFIX . "ocfilter_option_to_category oo2c ON (oo.option_id = oo2c.option_id)";
    }

    $sql .= " WHERE ood.language_id = '" . (int)$this->config->get('config_language_id') . "'";

    if (!empty($data['filter_category_id'])) {
			$sql .= " AND oo2c.category_id = '" . (int)$data['filter_category_id'] . "'";
		}

    if (!empty($data['filter_type'])) {
			$sql .= " AND oo.type = '" . $this->db->escape($data['filter_type']) . "'";
		}

    if (!empty($data['filter_name'])) {
			$sql .= " AND LCASE(ood.name) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
		}

    if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND oo.status = '" . (int)$data['filter_status'] . "'";
		}

    $sql .= " GROUP BY oo.option_id";

    $sort_data = array(
			'oo.sort_order',
			'ood.name'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY oo.sort_order, ood.name";
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

    $options_query = $this->db->query($sql);

    if ($options_query->num_rows) {
      $options_id = array();

      foreach ($options_query->rows as $option) {
        $options_id[] = (int)$option['option_id'];
      }

      $values_query = $this->db->query("SELECT oov.value_id, oov.option_id, oovd.name FROM " . DB_PREFIX . "ocfilter_option_value oov LEFT JOIN " . DB_PREFIX . "ocfilter_option_value_description oovd ON (oov.value_id = oovd.value_id) WHERE oov.option_id IN (" . implode(',', $options_id) . ") AND oovd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY oov.sort_order ASC, oovd.name DESC");

      $values = array();
      foreach ($values_query->rows as $value) $values[$value['option_id']][] = $value;

      $categories_query = $this->db->query("SELECT c.category_id, cd.name, oo2c.option_id FROM " . DB_PREFIX . "ocfilter_option_to_category oo2c LEFT JOIN " . DB_PREFIX . "category c ON (c.category_id = oo2c.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd ON (cd.category_id = c.category_id) WHERE oo2c.option_id IN (" . implode(',', $options_id) . ") AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY c.sort_order, cd.name DESC");

      $categories = array();
      foreach ($categories_query->rows as $category) $categories[$category['option_id']][] = $category;

      foreach ($options_query->rows as $key => $option) {
        $option_data[$key] = $option;
        $option_data[$key]['values'] = (isset($values[$option['option_id']]) ? $values[$option['option_id']] : array());
        $option_data[$key]['categories'] = (isset($categories[$option['option_id']]) ? $categories[$option['option_id']] : array());
      }
    }

    return $option_data;
  }

  public function getTotalOptions($data = array()) {

    $sql = "SELECT COUNT(DISTINCT oo.option_id) AS total  FROM " . DB_PREFIX . "ocfilter_option oo LEFT JOIN " . DB_PREFIX . "ocfilter_option_description ood ON (oo.option_id = ood.option_id)";

    if (!empty($data['filter_category_id'])) {
      $sql .= " LEFT JOIN " . DB_PREFIX . "ocfilter_option_to_category oo2c ON (oo.option_id = oo2c.option_id)";
    }

    $sql .= " WHERE ood.language_id = '" . (int)$this->config->get('config_language_id') . "'";

    if (!empty($data['filter_category_id'])) {
			$sql .= " AND oo2c.category_id = '" . (int)$data['filter_category_id'] . "'";
		}

    if (!empty($data['filter_type'])) {
			$sql .= " AND oo.type = '" . $this->db->escape($data['filter_type']) . "'";
		}

    if (!empty($data['filter_name'])) {
			$sql .= " AND LCASE(ood.name) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
		}

    if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND oo.status = '" . (int)$data['filter_status'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

  public function getCategories($parent_id, $level = -1) {
    $level++;

    $results = $this->getCategoriesByParentId($parent_id);

    $categories_data = array();

    foreach ($results as $result) {
      $categories_data[] = array(
        'category_id' => $result['category_id'],
        'name'        => $result['name'],
        'level'       => $level
      );

      $categories_data = array_merge($categories_data, $this->getCategories($result['category_id'], $level));
    }

    return $categories_data;
  }

  private function getCategoriesByParentId($parent_id = 0) { # For options list and form
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY c.sort_order, cd.name ASC");

		return $query->rows;
	}

  public function getProductOCFilterValues($product_id) { # For product copy
    $product_filter_value_data = array();

    $product_filter_value_description = array();

		$description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ocfilter_option_value_to_product_description WHERE product_id = '" . (int)$product_id . "'");

		foreach ($description_query->rows as $row) {
      $product_filter_value_description[$row['value_id']][$row['language_id']] = $row;
		}

    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ocfilter_option_value_to_product WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $key => $result) {
			$product_filter_value_data[$result['option_id']]['values'][(string)$result['value_id']] = array_merge($result, array('selected' => true));

      $product_filter_value_data[$result['option_id']]['values'][(string)$result['value_id']]['description'] = array();

			if (isset($product_filter_value_description[$result['value_id']])) {
        $product_filter_value_data[$result['option_id']]['values'][(string)$result['value_id']]['description'] = $product_filter_value_description[$result['value_id']];
			}
		}

		return $product_filter_value_data;
  }

  public function copyFilters($data = array()) {
    if (!empty($data['copy_truncate'])) {
  		$this->db->query("TRUNCATE `" . DB_PREFIX . "ocfilter_option`");
      $this->db->query("TRUNCATE `" . DB_PREFIX . "ocfilter_option_description`");
      $this->db->query("TRUNCATE `" . DB_PREFIX . "ocfilter_option_to_category`");
      $this->db->query("TRUNCATE `" . DB_PREFIX . "ocfilter_option_to_store`");
      $this->db->query("TRUNCATE `" . DB_PREFIX . "ocfilter_option_value`");
      $this->db->query("TRUNCATE `" . DB_PREFIX . "ocfilter_option_value_to_product`");
      $this->db->query("TRUNCATE `" . DB_PREFIX . "ocfilter_option_value_description`");
    }

    $type = $data['copy_type'];
    $status = ($data['copy_status'] != 0);

    // Copy Product Options
    if (!empty($data['copy_option'])) {
      $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_option                   (option_id, `type`, `status`, sort_order, image)  SELECT option_id, '" . $this->db->escape($type) . "', '" . (int)$status . "', sort_order, IF(`type` = 'image', '1', '0') FROM `" . DB_PREFIX . "option`");
      $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_option_description       (option_id, language_id, name)                    SELECT option_id, language_id, name FROM " . DB_PREFIX . "option_description");

      $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_option_value             (option_id, value_id, image, sort_order)          SELECT option_id, option_value_id, image, sort_order FROM " . DB_PREFIX . "option_value");
      $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_option_value_description (option_id, value_id,	language_id, name)          SELECT option_id, option_value_id, language_id, name FROM " . DB_PREFIX . "option_value_description");

      $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_option_value_to_product WHERE option_id IN(SELECT option_id FROM " . DB_PREFIX . "product_option_value WHERE quantity < '1')");

      $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_option_value_to_product  (option_id, value_id, product_id)                 SELECT option_id, option_value_id, product_id FROM " . DB_PREFIX . "product_option_value WHERE quantity > '0'");
    }

    // Copy Product Filters
    $o = 5000;
    $v = 10000;

    if (!empty($data['copy_filter'])) {
      $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_option                   (option_id, `type`, `status`, sort_order) SELECT (filter_group_id + '" . (int)$o . "'), '" . $this->db->escape($type) . "', '" . (int)$status . "', sort_order FROM `" . DB_PREFIX . "filter_group`");
      $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_option_description       (option_id, language_id, name)            SELECT (filter_group_id + '" . (int)$o . "'), language_id, name FROM " . DB_PREFIX . "filter_group_description");

      $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_option_value             (option_id, value_id, sort_order)         SELECT (filter_group_id + '" . (int)$o . "'), (filter_id + '" . (int)$v . "'), sort_order FROM " . DB_PREFIX . "filter");
      $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_option_value_description (option_id, value_id,	language_id, name)  SELECT (filter_group_id + '" . (int)$o . "'), (filter_id + '" . (int)$v . "'), language_id, name FROM " . DB_PREFIX . "filter_description");

      $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_option_value_to_product WHERE option_id IN(SELECT (filter_group_id + '" . (int)$o . "') FROM " . DB_PREFIX . "filter_group)");

      $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_option_value_to_product  (option_id, value_id, product_id)         SELECT (SELECT oov.option_id FROM " . DB_PREFIX . "ocfilter_option_value oov WHERE oov.value_id = (pf.filter_id + '" . (int)$v . "')), (pf.filter_id + '" . (int)$v . "'), pf.product_id FROM " . DB_PREFIX . "product_filter pf");
    }

    if (!empty($data['copy_attribute'])) {
      $this->db->query("UPDATE " . DB_PREFIX . "product_attribute SET text = TRIM(REPLACE(REPLACE(REPLACE(text, '\t', ''), '\n', ''), '\r', ''))");

      $o *= 2;
      $v *= 4;

      $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_option                   (option_id, status, type, sort_order)    SELECT (attribute_id + '" . (int)$o . "'), '" . (int)$status . "', '" . $this->db->escape($type) . "', sort_order FROM " . DB_PREFIX . "attribute");
      $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_option_description       (option_id, language_id, name)           SELECT (attribute_id + '" . (int)$o . "'), language_id, name FROM " . DB_PREFIX . "attribute_description");

  		$this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_option_value             (option_id, value_id)                    SELECT (attribute_id + '" . (int)$o . "'), (CRC32(CONCAT(attribute_id, text)) + '" . (int)$v . "') FROM " . DB_PREFIX . "product_attribute WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY attribute_id, text");
  		$this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_option_value_description (option_id, value_id, language_id, name) SELECT (attribute_id + '" . (int)$o . "'), (CRC32(CONCAT(attribute_id, text)) + '" . (int)$v . "'), language_id, text FROM " . DB_PREFIX . "product_attribute WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY attribute_id, text");

      $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_option_value_to_product WHERE option_id IN(SELECT (attribute_id + '" . (int)$o . "') FROM " . DB_PREFIX . "attribute)");

      $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_option_value_to_product  (option_id, value_id, product_id)        SELECT (attribute_id + '" . (int)$o . "'), (CRC32(CONCAT(attribute_id, text)) + '" . (int)$v . "'), product_id FROM " . DB_PREFIX . "product_attribute WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

      $this->load->model('localisation/language');

      $languages = $this->model_localisation_language->getLanguages();

      foreach ($languages as $language) {
        if ($language['language_id'] != $this->config->get('config_language_id')) {
      		$this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_option_value_description (option_id, value_id, language_id, name)

          SELECT
            (pa.attribute_id + '" . (int)$o . "'),
            (SELECT
              (CRC32(CONCAT(pa2.attribute_id, pa2.text)) + '" . (int)$v . "') FROM " . DB_PREFIX . "product_attribute pa2
              WHERE pa2.language_id = '" . (int)$this->config->get('config_language_id') . "'
              AND pa2.product_id = pa.product_id
              AND pa2.attribute_id = pa.attribute_id LIMIT 1
            ),
            '" . (int)$language['language_id'] . "', pa.text
          FROM " . DB_PREFIX . "product_attribute pa WHERE pa.language_id = '" . (int)$language['language_id'] . "' GROUP BY pa.attribute_id, pa.text");
        }
      }

      // Separate
      if (!empty($data['attribute_separator'])) {
      	$separator = (string)$data['attribute_separator'];

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ocfilter_option_value_description WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' AND name LIKE '%" . $this->db->escape($separator) . "%'");

        foreach ($query->rows as $result) {
        	$values = explode($separator, $result['name']);

          foreach ($values as $value) {
            $value = $this->utf8_ucfirst(trim($value));

            if (!$value) {
              continue;
            }

            $value_query = $this->db->query("SELECT value_id FROM " . DB_PREFIX . "ocfilter_option_value_description WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' AND option_id = '" . (int)$result['option_id'] . "' AND LCASE(TRIM(name)) = '" . $this->db->escape(utf8_strtolower($value)) . "'");

            if ($value_query->num_rows) {
              $value_id = $value_query->row['value_id'];
            } else {
        		  $this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_option_value (option_id) VALUES ('" . (int)$result['option_id'] . "')");

              $value_id = $this->db->getLastId();

              $this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_option_value_description (option_id, value_id, language_id, name) VALUES ('" . (int)$result['option_id'] . "', '" . $this->db->escape($value_id) . "', '" . (int)$this->config->get('config_language_id') . "', '" . $this->db->escape($value) . "')");
            }

            $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_option_value_to_product (product_id, option_id, value_id) SELECT oov2p.product_id, '" . (int)$result['option_id'] . "', '" . $this->db->escape($value_id) . "' FROM " . DB_PREFIX . "ocfilter_option_value_to_product oov2p WHERE oov2p.option_id = '" . (int)$result['option_id'] . "' AND oov2p.value_id = '" . $this->db->escape($result['value_id']) . "'");
          }

          if ($values) {
            $this->db->query("DELETE FROM `" . DB_PREFIX . "ocfilter_option_value` WHERE value_id = '" . $this->db->escape($result['value_id']) . "'");
            $this->db->query("DELETE FROM `" . DB_PREFIX . "ocfilter_option_value_description` WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' AND value_id = '" . $this->db->escape($result['value_id']) . "'");
            $this->db->query("DELETE FROM `" . DB_PREFIX . "ocfilter_option_value_to_product` WHERE option_id = '" . (int)$result['option_id'] . "' AND value_id = '" . $this->db->escape($result['value_id']) . "'");
          }
        }
      }
    }

    if (!empty($data['copy_option']) || !empty($data['copy_filter']) || !empty($data['copy_attribute'])) {
      // Category
      if (!empty($data['copy_category'])) {
        $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_option_to_category (option_id, category_id) SELECT oov2p.option_id, p2c.category_id FROM " . DB_PREFIX . "ocfilter_option_value_to_product oov2p LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p2c.product_id = oov2p.product_id) WHERE p2c.category_id != '0' GROUP BY oov2p.option_id, p2c.category_id");
      }

      // Store
      $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_option_to_store (option_id, store_id) SELECT option_id, '0' FROM " . DB_PREFIX . "ocfilter_option");

  		$this->load->model('setting/store');

  		$results = $this->model_setting_store->getStores();

      foreach ($results as $result) {
        $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_option_to_store (option_id, store_id) SELECT option_id, '" . (int)$result['store_id'] . "' FROM " . DB_PREFIX . "ocfilter_option");
      }

      // Set status = '0' (auto)
      if ($data['copy_status'] < 0) {
        $this->db->query("UPDATE " . DB_PREFIX . "ocfilter_option oo LEFT JOIN " . DB_PREFIX . "ocfilter_option_to_category oo2c ON (oo.option_id = oo2c.option_id) SET oo.status = '0' WHERE oo2c.category_id IS NULL");

   	    $this->db->query("UPDATE " . DB_PREFIX . "ocfilter_option oo LEFT JOIN " . DB_PREFIX . "ocfilter_option_value_to_product oov2p ON (oo.option_id = oov2p.option_id) SET oo.status = '0' WHERE oov2p.product_id IS NULL");

   	    $this->db->query("UPDATE " . DB_PREFIX . "ocfilter_option oo LEFT JOIN " . DB_PREFIX . "ocfilter_option_value oov ON (oo.option_id = oov.option_id) SET oo.status = '0' WHERE oov.value_id IS NULL");

   	    $this->db->query("UPDATE " . DB_PREFIX . "ocfilter_option oo SET oo.status = '0' WHERE (oo.type = 'slide_dual' OR oo.type = 'slide') AND (SELECT COUNT(*) FROM " . DB_PREFIX . "ocfilter_option_value oov WHERE oov.option_id = oo.option_id) > '100'");
      }

      // Convert to slide
      $option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ocfilter_option WHERE status = '1' AND (type = 'slide' OR type = 'slide_dual')");

      foreach ($option_query->rows as $option) {
        $value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ocfilter_option_value_description WHERE option_id = '" . (int)$option['option_id'] . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

        foreach ($value_query->rows as $value) {
          $slide_value_min = (float)preg_replace('![^0-9\.\-]+!s', '', $value['name']);

          if ($slide_value_min) {
    		    $this->db->query("UPDATE IGNORE " . DB_PREFIX . "ocfilter_option_value_to_product SET value_id = '0', slide_value_min = '" . (float)$slide_value_min . "', slide_value_max = '" . (float)$slide_value_min . "' WHERE option_id = '" . (int)$value['option_id'] . "' AND value_id = '" . (string)$value['value_id'] . "'");
          }
        }
      }

      $this->db->query("OPTIMIZE TABLE `" . DB_PREFIX . "ocfilter_option_value_to_product`");
    }

    // Set URL Aliases
    $query = $this->db->query("SELECT oo.option_id, ood.name FROM " . DB_PREFIX . "ocfilter_option oo LEFT JOIN " . DB_PREFIX . "ocfilter_option_description ood ON(oo.option_id = ood.option_id) WHERE ood.language_id = '" . (int)$this->config->get('config_language_id') . "' AND oo.`keyword` = ''");

    foreach ($query->rows as $row) {
    	$this->db->query("UPDATE " . DB_PREFIX . "ocfilter_option SET `keyword` = '" . $this->db->escape($this->translit($row['name'])) . "' WHERE option_id = '" . (int)$row['option_id'] . "'");
    }

    $query = $this->db->query("SELECT oov.value_id, oovd.name FROM " . DB_PREFIX . "ocfilter_option_value oov LEFT JOIN " . DB_PREFIX . "ocfilter_option_value_description oovd ON(oov.value_id = oovd.value_id) WHERE oovd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND oov.`keyword` = ''");

    foreach ($query->rows as $row) {
    	$this->db->query("UPDATE " . DB_PREFIX . "ocfilter_option_value SET `keyword` = '" . $this->db->escape($this->translit($row['name'])) . "' WHERE value_id = '" . $this->db->escape($row['value_id']) . "'");
    }

    $this->cache->delete('ocfilter');
    $this->cache->delete('product');
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

  private function utf8_ucfirst($str) {
    return utf8_strtoupper(utf8_substr($str, 0, 1)) . utf8_substr($str, 1);
  }
}
?>