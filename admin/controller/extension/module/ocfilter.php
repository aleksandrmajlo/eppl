<?php
class ControllerExtensionModuleOCFilter extends Controller {
	private $error = array();
  private $info = array();

	public function index() {
    // Set language data
		$data = $this->load->language('extension/module/ocfilter');

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateModule()) {
      $ocfilter_data = array();

      foreach ($this->request->post as $key => $value) {
      	$ocfilter_data['module_ocfilter_' . $key] = $value;
      }

      $this->model_setting_setting->editSetting('module_ocfilter', $ocfilter_data);

      $this->cache->delete('ocfilter');

			$this->session->data['success'] = $this->language->get('text_success');

      if (isset($this->request->get['apply'])) {
			  $this->response->redirect($this->url->link('extension/module/ocfilter', 'user_token=' . $this->session->data['user_token'], true));
      } else {
        $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
      }
		}

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

      unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/ocfilter', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['save'] = $this->url->link('extension/module/ocfilter', 'user_token=' . $this->session->data['user_token'], true);
    $data['apply'] = $this->url->link('extension/module/ocfilter', 'user_token=' . $this->session->data['user_token'] . '&apply', true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

    $data['filter_list'] = $this->url->link('extension/module/ocfilter/filter', 'user_token=' . $this->session->data['user_token'], true);
    $data['filter_page_list'] = $this->url->link('extension/module/ocfilter/page', 'user_token=' . $this->session->data['user_token'], true);

    $data['user_token'] = $this->session->data['user_token'];

		$data['status'] = $this->getModuleData('status');
    $data['sub_category'] = $this->getModuleData('sub_category');
    $data['sitemap_status'] = $this->getModuleData('sitemap_status', 1);

    $data['sitemap_link'] = HTTP_CATALOG . 'index.php?route=extension/feed/ocfilter_sitemap';

    $data['search_button'] = $this->getModuleData('search_button', 1);
    $data['show_selected'] = $this->getModuleData('show_selected', 1);
    $data['show_price'] = $this->getModuleData('show_price', 1);
    $data['show_counter'] = $this->getModuleData('show_counter', 1);
    $data['manufacturer'] = $this->getModuleData('manufacturer', 1);
    $data['manufacturer_type'] = $this->getModuleData('manufacturer_type', 'checkbox');
    $data['stock_status'] = $this->getModuleData('stock_status');
    $data['stock_status_type'] = $this->getModuleData('stock_status_type', 'checkbox');
    $data['stock_status_method'] = $this->getModuleData('stock_status_method', 'quantity');
    $data['stock_out_value'] = $this->getModuleData('stock_out_value');
    $data['manual_price'] = $this->getModuleData('manual_price');
    $data['consider_discount'] = $this->getModuleData('consider_discount');
    $data['consider_special'] = $this->getModuleData('consider_special');
    $data['consider_option'] = $this->getModuleData('consider_option');
    $data['show_options_limit'] = $this->getModuleData('show_options_limit');
    $data['show_values_limit'] = $this->getModuleData('show_values_limit');
    $data['hide_empty_values'] = $this->getModuleData('hide_empty_values', 1);

    $data['copy_attribute_separator'] = $this->getModuleData('copy_attribute_separator', '');
    $data['copy_type'] = $this->getModuleData('copy_type', 'checkbox');
    $data['copy_status'] = $this->getModuleData('copy_status', -1);
    $data['copy_attribute'] = $this->getModuleData('copy_attribute', 1);
    $data['copy_filter'] = $this->getModuleData('copy_filter');
    $data['copy_option'] = $this->getModuleData('copy_option');

    $data['types'] = array(
      'checkbox',
      'radio'
    );

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

    $this->response->setOutput($this->load->view('extension/module/ocfilter', $data));
	}

  // Filter
  public function filter() {
    // Set language data
		$data = $this->load->language('extension/module/ocfilter');

    $this->document->setTitle($this->language->get('heading_title_filter'));

    $this->load->model('extension/ocfilter');

    $data['language_id'] = $this->config->get('config_language_id');

    $this->getFilterList($data);
  }

  public function addFilter() {
    // Set language data
		$data = $this->load->language('extension/module/ocfilter');

    $this->document->setTitle($this->language->get('heading_title_filter'));

    $this->load->model('extension/ocfilter');

    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateFilter()) {
      $this->model_extension_ocfilter->addOption($this->request->post);

      $this->session->data['success'] = $this->language->get('text_success');

      $url = $this->getURL();

      $this->response->redirect($this->url->link('extension/module/ocfilter/filter', 'user_token=' . $this->session->data['user_token'] . $url, true));
    }

    $this->getFilterForm($data);
  }

  public function editFilter() {
    // Set language data
		$data = $this->load->language('extension/module/ocfilter');

    $this->document->setTitle($this->language->get('heading_title_filter'));

    $this->load->model('extension/ocfilter');

    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateFilter()) {
      $this->model_extension_ocfilter->editOption($this->request->get['option_id'], $this->request->post);

      $this->session->data['success'] = $this->language->get('text_success');

      $url = $this->getURL();

      $this->response->redirect($this->url->link('extension/module/ocfilter/filter', 'user_token=' . $this->session->data['user_token'] . $url, true));
    }

    $this->getFilterForm($data);
  }

  public function deleteFilter() {
    // Set language data
		$data = $this->load->language('extension/module/ocfilter');

    $this->document->setTitle($this->language->get('heading_title_filter'));

    $this->load->model('extension/ocfilter');

    if (isset($this->request->post['selected'])) {
      foreach ($this->request->post['selected'] as $option_id) {
        $this->model_extension_ocfilter->deleteOption($option_id);
      }

      $this->session->data['success'] = $this->language->get('text_success');

      $url = $this->getURL();

      $this->response->redirect($this->url->link('extension/module/ocfilter/filter', 'user_token=' . $this->session->data['user_token'] . $url, true));
    }

    $this->getFilterList($data);
  }

  protected function getFilterList($data) {
    $this->document->addStyle('view/stylesheet/ocfilter/ocfilter.css');
    $this->document->addScript('view/javascript/ocfilter/ocfilter.js');

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_category_id'])) {
			$filter_category_id = $this->request->get['filter_category_id'];
		} else {
			$filter_category_id = null;
		}

		if (isset($this->request->get['filter_type'])) {
			$filter_type = $this->request->get['filter_type'];
		} else {
			$filter_type = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'ood.name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

    $data['breadcrumbs']   = array();

    $data['breadcrumbs'][] = array(
      'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
      'text' => $this->language->get('text_home')
    );

    $data['breadcrumbs'][] = array(
      'href' => $this->url->link('extension/module/ocfilter/filter', 'user_token=' . $this->session->data['user_token'], true),
      'text' => $this->language->get('heading_title_filter')
    );

    $data['heading_title'] = $this->language->get('heading_title_filter');

    $url = $this->getURL();

    $data['add']  = $this->url->link('extension/module/ocfilter/addFilter', 'user_token=' . $this->session->data['user_token'] . $url, true);
    $data['delete']  = $this->url->link('extension/module/ocfilter/deleteFilter', 'user_token=' . $this->session->data['user_token'] . $url, true);

    $data['options'] = array();

    $filter_data = array(
      'filter_name' => $filter_name,
      'filter_category_id' => $filter_category_id,
      'filter_status' => $filter_status,
      'filter_type' => $filter_type,

      'sort' => $sort,
      'order' => $order,
      'start' => ($page - 1) * $this->config->get('config_limit_admin'),
      'limit' => $this->config->get('config_limit_admin'),
    );

    $option_total = $this->model_extension_ocfilter->getTotalOptions($filter_data);

    $results = $this->model_extension_ocfilter->getOptions($filter_data);

    $visible = 5;

    foreach ($results as $result) {
      $values = array();

      foreach ($result['values'] as $value) {
        $values[] = $value['name'];
      }

      if ($values) {
        if (count($values) > $visible) {
          $values = array_slice($values, 0, $visible);
          $values[$visible - 1] .= $result['postfix'] . sprintf($this->language->get('text_and_more'), (count($result['values']) - $visible));
        } else {
          $values[count($values) - 1] .= $result['postfix'];
        }
      }

      $categories = array();

      foreach ($result['categories'] as $category) {
        $categories[] = $category['name'];
      }

      if (count($categories) > $visible) {
        $categories = array_slice($categories, 0, $visible);

        $categories[$visible - 1] .= sprintf($this->language->get('text_and_more'), (count($result['categories']) - $visible));
      }

      $data['options'][] = array(
        'option_id' => $result['option_id'],
        'name' => $result['name'],
        'type' => $result['type'],
        'sort_order' => $result['sort_order'],
        'selected' => isset($this->request->post['selected']) && in_array($result['option_id'], $this->request->post['selected']),
        'values' => html_entity_decode(implode(' &bull; ', $values), ENT_QUOTES, 'UTF-8'),
        'categories' => implode(' &bull; ', $categories),
        'status' => $result['status'],
        'selectbox' => $result['selectbox'],
        'edit' => $this->url->link('extension/module/ocfilter/editFilter', 'user_token=' . $this->session->data['user_token'] . '&option_id=' . $result['option_id'] . $url, true)
      );
    }

    $data['user_token'] = $this->session->data['user_token'];

    if (isset($this->error['warning'])) {
      $data['error_warning'] = $this->error['warning'];
    } else {
      $data['error_warning'] = '';
    }

    if (isset($this->session->data['success'])) {
      $data['success'] = $this->session->data['success'];

      unset($this->session->data['success']);
    } else {
      $data['success'] = '';
    }

    $url = $this->getURL(array('sort', 'order'));

    if ($order == 'ASC') {
      $url .= '&order=DESC';
    } else {
      $url .= '&order=ASC';
    }

    $data['sort_name']        = $this->url->link('extension/module/ocfilter/filter', 'user_token=' . $this->session->data['user_token'] . '&sort=ood.name' . $url, true);
    $data['sort_type']        = $this->url->link('extension/module/ocfilter/filter', 'user_token=' . $this->session->data['user_token'] . '&sort=oo.type' . $url, true);
    $data['sort_category_id'] = $this->url->link('extension/module/ocfilter/filter', 'user_token=' . $this->session->data['user_token'] . '&sort=oo2c.category_id' . $url, true);
    $data['sort_status']      = $this->url->link('extension/module/ocfilter/filter', 'user_token=' . $this->session->data['user_token'] . '&sort=oo.status' . $url, true);
    $data['sort_order']       = $this->url->link('extension/module/ocfilter/filter', 'user_token=' . $this->session->data['user_token'] . '&sort=oo.sort_order' . $url, true);

    $url = $this->getURL(array('page'));

    $pagination         = new Pagination();
    $pagination->total  = $option_total;
    $pagination->page   = $page;
    $pagination->limit  = $this->config->get('config_limit_admin');
    $pagination->text   = $this->language->get('text_pagination');
    $pagination->url    = $this->url->link('extension/module/ocfilter/filter', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

    $data['pagination'] = $pagination->render();

    $data['results'] = sprintf($this->language->get('text_pagination'), ($option_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($option_total - $this->config->get('config_limit_admin'))) ? $option_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $option_total, ceil($option_total / $this->config->get('config_limit_admin')));

    $data['types'] = array(
      'checkbox' => $this->language->get('text_checkbox'),
      'radio' => $this->language->get('text_radio'),
      'slide' => $this->language->get('text_slide'),
      'slide_dual' => $this->language->get('text_slide_dual'),
    );

    $data['filter_name'] = $filter_name;
    $data['filter_category_id'] = $filter_category_id;
    $data['filter_status'] = $filter_status;
    $data['filter_type'] = $filter_type;

    $data['sort'] = $sort;
    $data['order'] = $order;

    $data['categories']  = $this->model_extension_ocfilter->getCategories(0);

    $data['header']      = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer']      = $this->load->controller('common/footer');

    $this->response->setOutput($this->load->view('extension/module/ocfilter_list', $data));
  }

  protected function getFilterForm($data) {
    $this->document->addStyle('view/stylesheet/ocfilter/ocfilter.css');
    $this->document->addScript('view/javascript/ocfilter/ocfilter.js');

    if (isset($this->error['warning'])) {
      $data['error_warning'] = $this->error['warning'];
    } else {
      $data['error_warning'] = '';
    }

    if (isset($this->error['name'])) {
      $data['error_name'] = $this->error['name'];
    } else {
      $data['error_name'] = '';
    }

    $data['breadcrumbs'] = array();

    $data['breadcrumbs'][] = array(
      'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
      'text' => $this->language->get('text_home')
    );

    $data['breadcrumbs'][] = array(
      'href' => $this->url->link('extension/module/ocfilter/filter', 'user_token=' . $this->session->data['user_token'], true),
      'text' => $this->language->get('heading_title_filter')
    );

    $data['heading_title'] = $this->language->get('heading_title_filter');

    $url = $this->getURL();

    if (!isset($this->request->get['option_id'])) {
      $data['action'] = $this->url->link('extension/module/ocfilter/addFilter', 'user_token=' . $this->session->data['user_token'] . $url, true);
    } else {
      $data['action'] = $this->url->link('extension/module/ocfilter/editFilter', 'user_token=' . $this->session->data['user_token'] . '&option_id=' . $this->request->get['option_id'] . $url, true);
    }

    $data['cancel'] = $this->url->link('extension/module/ocfilter/filter', 'user_token=' . $this->session->data['user_token'] . $url, true);

    $data['user_token'] = $this->session->data['user_token'];

    $this->load->model('localisation/language');

    $data['languages'] = $this->model_localisation_language->getLanguages();

    if (isset($this->request->get['option_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      $option_info = $this->model_extension_ocfilter->getOption($this->request->get['option_id']);

      $this->info = $option_info;
    }

    if (isset($this->request->post['name'])) {
      $data['name'] = $this->request->post['name'];
    } else if (isset($this->request->get['option_id'])) {
      $option_description = $this->model_extension_ocfilter->getOptionDescriptions($this->request->get['option_id']);

      $data['name'] = $option_description;

      $data['breadcrumbs'][] = array(
        'href' => $this->url->link('extension/module/ocfilter/editFilter', 'user_token=' . $this->session->data['user_token'] . '&option_id=' . $this->request->get['option_id'], true),
        'text' => $option_description[$this->config->get('config_language_id')]['name']
      );
    } else {
      $data['name'] = array();
    }

    if (isset($this->request->post['option_values'])) {
      $data['ocfilter_option_values'] = $this->request->post['option_values'];
    } else if (isset($this->request->get['option_id'])) {
      $data['ocfilter_option_values'] = $this->model_extension_ocfilter->getOptionValues($this->request->get['option_id']);
    } else {
      $data['ocfilter_option_values'] = array();
    }

    $this->load->model('tool/image');

    $data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 22, 22);

    foreach ($data['ocfilter_option_values'] as $key => $value) {
      if ($value['image'] && file_exists(DIR_IMAGE . $value['image'])) {
        $data['ocfilter_option_values'][$key]['thumb'] = $this->model_tool_image->resize($value['image'], 22, 22);
      } else {
        $data['ocfilter_option_values'][$key]['thumb'] = $this->model_tool_image->resize('placeholder.png', 22, 22);
      }
    }

    if (isset($this->request->post['option_categories'])) {
      $data['option_categories'] = $this->request->post['option_categories'];
    } else if (isset($option_info)) {
      $data['option_categories'] = $this->model_extension_ocfilter->getOptionCategories($this->request->get['option_id']);
    } else {
      $data['option_categories'] = array();
    }

    $this->load->model('setting/store');

    $data['stores'] = $this->model_setting_store->getStores();

    if (isset($this->request->post['option_store'])) {
      $data['option_store'] = $this->request->post['option_store'];
    } else if (isset($this->request->get['option_id'])) {
      $data['option_store'] = $this->model_extension_ocfilter->getOptionStores($this->request->get['option_id']);
    } else {
      $data['option_store'] = array(0);
    }

    $data['sort_order'] = $this->getFormData('sort_order');
    $data['status'] = $this->getFormData('status', 1);
    $data['type'] = $this->getFormData('type', '');

    $data['types'] = array(
      'checkbox' => $this->language->get('text_checkbox'),
      'radio' => $this->language->get('text_radio'),
      'slide' => $this->language->get('text_slide'),
      'slide_dual' => $this->language->get('text_slide_dual'),
    );

    $data['keyword'] = $this->getFormData('keyword', '');
    $data['selectbox'] = $this->getFormData('selectbox');
    $data['color'] = $this->getFormData('color');
    $data['image'] = $this->getFormData('image');

    $data['header']      = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer']      = $this->load->controller('common/footer');

    $this->response->setOutput($this->load->view('extension/module/ocfilter_form', $data));
  }

  // SEO Page
  public function page() {
    // Set language data
		$data = $this->load->language('extension/module/ocfilter');

    $this->document->setTitle($this->language->get('heading_title_page'));

    $this->load->model('extension/ocfilter_page');

    $this->getPageList($data);
  }

  public function addPage() {
    // Set language data
		$data = $this->load->language('extension/module/ocfilter');

    $this->document->setTitle($this->language->get('heading_title_page'));

    $this->load->model('extension/ocfilter_page');

    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validatePage()) {
      $this->model_extension_ocfilter_page->addPage($this->request->post);

      $this->session->data['success'] = $this->language->get('text_success');

      $url = $this->getURL();

      $this->response->redirect($this->url->link('extension/module/ocfilter/page', 'user_token=' . $this->session->data['user_token'] . $url, true));
    }

    $this->getPageForm($data);
  }

  public function editPage() {
    // Set language data
		$data = $this->load->language('extension/module/ocfilter');

    $this->document->setTitle($this->language->get('heading_title_page'));

    $this->load->model('extension/ocfilter_page');

    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validatePage()) {
      $this->model_extension_ocfilter_page->editPage($this->request->get['ocfilter_page_id'], $this->request->post);

      $this->session->data['success'] = $this->language->get('text_success');

      $url = $this->getURL();

      $this->response->redirect($this->url->link('extension/module/ocfilter/page', 'user_token=' . $this->session->data['user_token'] . $url, true));
    }

    $this->getPageForm($data);
  }

  public function deletePage() {
    // Set language data
		$data = $this->load->language('extension/module/ocfilter');

    $this->document->setTitle($this->language->get('heading_title_page'));

    $this->load->model('extension/ocfilter_page');

    if (isset($this->request->post['selected']) && $this->validateDelete()) {
      foreach ($this->request->post['selected'] as $ocfilter_page_id) {
        $this->model_extension_ocfilter_page->deletePage($ocfilter_page_id);
      }

      $this->session->data['success'] = $this->language->get('text_success');

      $url = $this->getURL();

      $this->response->redirect($this->url->link('extension/module/ocfilter/page', 'user_token=' . $this->session->data['user_token'] . $url, true));
    }

    $this->getPageList($data);
  }

  protected function getPageList($data) {
    if (isset($this->request->get['filter_category_id'])) {
      $filter_category_id = $this->request->get['filter_category_id'];
    } else {
      $filter_category_id = null;
    }

    if (isset($this->request->get['filter_title'])) {
      $filter_title = $this->request->get['filter_title'];
    } else {
      $filter_title = '';
    }

    if (isset($this->request->get['sort'])) {
      $sort = $this->request->get['sort'];
    } else {
      $sort = null;
    }

    if (isset($this->request->get['order'])) {
      $order = $this->request->get['order'];
    } else {
      $order = null;
    }

    if (isset($this->request->get['page'])) {
      $page = $this->request->get['page'];
    } else {
      $page = 1;
    }

    $data['breadcrumbs']   = array();

    $data['breadcrumbs'][] = array(
      'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
      'text' => $this->language->get('text_home')
    );

    $data['breadcrumbs'][] = array(
      'href' => $this->url->link('extension/module/ocfilter/page', 'user_token=' . $this->session->data['user_token'], true),
      'text' => $this->language->get('heading_title_page')
    );

    $data['heading_title'] = $this->language->get('heading_title_page');

    $url = $this->getURL();

    $data['add']  = $this->url->link('extension/module/ocfilter/addPage', 'user_token=' . $this->session->data['user_token'] . $url, true);
    $data['delete']  = $this->url->link('extension/module/ocfilter/deletePage', 'user_token=' . $this->session->data['user_token'] . $url, true);

    $filter_data = array(
      'filter_category_id' => $filter_category_id,
      'filter_title' => $filter_title,

      'sort' => $sort,
      'order' => $order,
      'start' => ($page - 1) * $this->config->get('config_limit_admin'),
      'limit' => $this->config->get('config_limit_admin'),
    );

    $data['pages'] = array();

    $pages_total = $this->model_extension_ocfilter_page->getTotalPages($filter_data);

    $results = $this->model_extension_ocfilter_page->getPages($filter_data);

    foreach ($results as $result) {
      $data['pages'][] = array(
        'ocfilter_page_id' => $result['ocfilter_page_id'],
        'title' => $result['title'],
        'category' => $result['category'],
        'selected' => isset($this->request->post['selected']) && in_array($result['option_id'], $this->request->post['selected']),
        'status' => $result['status'],
        'edit' => $this->url->link('extension/module/ocfilter/editPage', 'user_token=' . $this->session->data['user_token'] . '&ocfilter_page_id=' . $result['ocfilter_page_id'] . $url, true)
      );
    }

    $data['user_token'] = $this->session->data['user_token'];

    if (isset($this->error['warning'])) {
      $data['error_warning'] = $this->error['warning'];
    } else {
      $data['error_warning'] = '';
    }

    if (isset($this->session->data['success'])) {
      $data['success'] = $this->session->data['success'];
      unset($this->session->data['success']);
    } else {
      $data['success'] = '';
    }

    $url = $this->getURL(array('page'));

    $pagination         = new Pagination();
    $pagination->total  = $pages_total;
    $pagination->page   = $page;
    $pagination->limit  = $this->config->get('config_limit_admin');
    $pagination->text   = $this->language->get('text_pagination');
    $pagination->url    = $this->url->link('extension/module/ocfilter/page', 'user_token=' . $this->session->data['user_token'] . '&page={page}' . $url, true);

    $data['pagination'] = $pagination->render();
    $data['results']    = sprintf($this->language->get('text_pagination'), ($pages_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($pages_total - $this->config->get('config_limit_admin'))) ? $pages_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $pages_total, ceil($pages_total / $this->config->get('config_limit_admin')));

    $data['filter_category_id'] = $filter_category_id;
    $data['filter_title'] = $filter_title;
    $data['sort'] = $sort;
    $data['order'] = $order;

    $data['header']      = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer']      = $this->load->controller('common/footer');

    $this->response->setOutput($this->load->view('extension/module/ocfilter_page_list', $data));
  }

  protected function getPageForm($data) {
		$data['text_form'] = !isset($this->request->get['ocfilter_page_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['title'])) {
			$data['error_title'] = $this->error['title'];
		} else {
			$data['error_title'] = array();
		}

		if (isset($this->error['meta_title'])) {
			$data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$data['error_meta_title'] = array();
		}

		if (isset($this->error['keyword'])) {
			$data['error_keyword'] = $this->error['keyword'];
		} else {
			$data['error_keyword'] = '';
		}

		if (isset($this->error['params'])) {
			$data['error_params'] = $this->error['params'];
		} else {
			$data['error_params'] = '';
		}

		if (isset($this->error['category'])) {
			$data['error_category'] = $this->error['category'];
		} else {
			$data['error_category'] = '';
		}

    $data['breadcrumbs']   = array();

    $data['breadcrumbs'][] = array(
      'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
      'text' => $this->language->get('text_home')
    );

    $data['breadcrumbs'][] = array(
      'href' => $this->url->link('extension/module/ocfilter/page', 'user_token=' . $this->session->data['user_token'], true),
      'text' => $this->language->get('heading_title_page')
    );

    $data['heading_title'] = $this->language->get('heading_title_page');

    $url = $this->getURL();

    if (!isset($this->request->get['ocfilter_page_id'])) {
      $data['action'] = $this->url->link('extension/module/ocfilter/addPage', 'user_token=' . $this->session->data['user_token'] . $url, true);
    } else {
      $data['action'] = $this->url->link('extension/module/ocfilter/editPage', 'user_token=' . $this->session->data['user_token'] . $url . '&ocfilter_page_id=' . $this->request->get['ocfilter_page_id'], true);
    }

    $data['cancel'] = $this->url->link('extension/module/ocfilter/page', 'user_token=' . $this->session->data['user_token'] . $url, true);

    if (isset($this->request->get['ocfilter_page_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      $page_info = $this->model_extension_ocfilter_page->getPage($this->request->get['ocfilter_page_id']);

      $this->info = $page_info;
    }

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['page_description'])) {
			$data['page_description'] = $this->request->post['page_description'];
		} else if (isset($page_info)) {
			$data['page_description'] = $this->model_extension_ocfilter_page->getPageDescriptions($this->request->get['ocfilter_page_id']);
		} else {
			$data['page_description'] = array();
		}

    $data['status'] = $this->getFormData('status', 1);
    $data['params'] = $this->getFormData('params', '');
    $data['keyword'] = $this->getFormData('keyword', '');
    $data['category_id'] = $this->getFormData('category_id');
    $data['path'] = $this->getFormData('path', '');

    $data['user_token'] = $this->session->data['user_token'];

    $data['header']      = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer']      = $this->load->controller('common/footer');

    $this->response->setOutput($this->load->view('extension/module/ocfilter_page_form', $data));
  }

  // AJAX
  public function callback() {
    $json = array();

		$this->load->language('extension/module/ocfilter');
    $this->load->model('extension/ocfilter');
    $this->load->model('localisation/language');

    $languages = $this->model_localisation_language->getLanguages();

    $json['message'] = '';
    $json['options'] = array();

    if (isset($this->request->get['category_id'])) {

      if (isset($this->request->get['product_id'])) {
        $product_values = $this->model_extension_ocfilter->getProductValues($this->request->get['product_id']);
      } else {
        $product_values = array();
      }

      if ($results = $this->model_extension_ocfilter->getOptionsByCategoryId($this->request->get['category_id'])) {
        foreach (array_values($results) as $key => $option) {
          if (!$option['status']) {
          	continue;
          }

          $values      = array();
          $description = array();

          foreach ($languages as $language) {
            $description[$language['language_id']] = array(
              'description' => ''
            );
          }

          if ($option['type'] != 'slide' && $option['type'] != 'slide_dual' && $option['type'] != 'text') {
            foreach ($option['values'] as $_key => $value) {
              $values[$_key] = array(
                'value_id' => (string)$value['value_id'],
                'name' => $value['name'],
                'description' => $description,
                'selected' => (bool)false
              );

              if (isset($product_values[$option['option_id']][$value['value_id']])) {
                $values[$_key]['selected']    = (bool)true;
                $values[$_key]['description'] = $product_values[$option['option_id']][$value['value_id']]['description'];
              }
            }
          }

          $json['options'][$key] = array(
            'option_id' => (string)$option['option_id'],
            'name' => $option['name'],
            'postfix' => $option['postfix'],
            'status' => (int) $option['status'],
            'type' => $option['type'],
            'slide_value_min' => '',
            'slide_value_max' => '',
            'description' => $description,
            'values' => $values
          );

          if (isset($product_values[$option['option_id']][0])) {
            $product_value                            = array_shift($product_values[$option['option_id']]);
            $json['options'][$key]['description']     = $product_value['description'];
            $json['options'][$key]['slide_value_min'] = ((float) $product_value['slide_value_min'] ? preg_replace('!(0+?$)|(\.0+?$)!', '', $product_value['slide_value_min']) : '');
            $json['options'][$key]['slide_value_max'] = ((float) $product_value['slide_value_max'] ? preg_replace('!(0+?$)|(\.0+?$)!', '', $product_value['slide_value_max']) : '');
          }
        }
      } else {
        $json['message'] = $this->language->get('text_no_options');
      }
    } else {
      $json['message'] = $this->language->get('text_select_category');
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  public function editImmediately() {
    $json = array();

    if (isset($this->request->post['option_id']) && isset($this->request->post['field']) && isset($this->request->post['value'])) {
      if ($this->request->post['field'] == 'name') {
        $this->db->query("UPDATE " . DB_PREFIX . "ocfilter_option_description SET name = '" . $this->db->escape(urldecode($this->request->post['value'])) . "' WHERE option_id = '" . (int) $this->request->post['option_id'] . "' AND language_id = '" . (int) $this->config->get('config_language_id') . "'");
      } else {
        $this->db->query("UPDATE " . DB_PREFIX . "ocfilter_option SET `" . $this->db->escape($this->request->post['field']) . "` = '" . $this->db->escape($this->request->post['value']) . "' WHERE option_id = '" . (int) $this->request->post['option_id'] . "'");
      }

      if ($this->request->post['field'] == 'type' && ($this->request->post['value'] == 'slide' || $this->request->post['value'] == 'slide_dual')) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ocfilter_option_value_description WHERE option_id = '" . (int)$this->request->post['option_id'] . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

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

      $json['status'] = true;
    } else {
      $json['status'] = false;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  public function copyFilters() {
    $json = array();

    $this->load->language('extension/module/ocfilter');

    if ($this->request->server['REQUEST_METHOD'] == 'POST') {
      $this->load->model('extension/ocfilter');

      $this->model_extension_ocfilter->copyFilters($this->request->post);

      $json['success'] = $this->language->get('text_complete');
    }

		$this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  // Get form data
  protected function getModuleData($key, $default = 0) {
		if (isset($this->request->post[$key])) {
			return $this->request->post[$key];
    } else if ($this->config->has('module_ocfilter_' . $key)) {
			return $this->config->get('module_ocfilter_' . $key);
		} else {
			return $default;
		}
  }

  protected function getFormData($key, $default = 0) {
		if (isset($this->request->post[$key])) {
			return $this->request->post[$key];
    } else if (isset($this->info[$key])) {
			return $this->info[$key];
		} else {
			return $default;
		}
  }

  protected function getURL($ignore = array()) {
    $url = '';

		if (isset($this->request->get['filter_name']) && !in_array('filter_name', $ignore)) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_title']) && !in_array('filter_title', $ignore)) {
			$url .= '&filter_title=' . urlencode(html_entity_decode($this->request->get['filter_title'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_category_id']) && !in_array('filter_category_id', $ignore)) {
      $url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
		}

		if (isset($this->request->get['filter_type']) && !in_array('filter_type', $ignore)) {
			$url .= '&filter_type=' . $this->request->get['filter_type'];
		}

		if (isset($this->request->get['filter_status']) && !in_array('filter_status', $ignore)) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['sort']) && !in_array('sort', $ignore)) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order']) && !in_array('order', $ignore)) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page']) && !in_array('page', $ignore)) {
			$url .= '&page=' . $this->request->get['page'];
		}

    return $url;
  }

  // Validation
	protected function validateModule() {
		if (!$this->user->hasPermission('modify', 'extension/module/ocfilter')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateFilter() {
		if (!$this->user->hasPermission('modify', 'extension/module/ocfilter')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validatePage() {
		if (!$this->user->hasPermission('modify', 'extension/module/ocfilter')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['page_description'] as $language_id => $value) {
			if ((utf8_strlen($value['title']) < 2) || (utf8_strlen($value['title']) > 128)) {
				$this->error['title'][$language_id] = $this->language->get('error_title');
			}

			if ((utf8_strlen($value['meta_title']) < 3) || (utf8_strlen($value['meta_title']) > 255)) {
				$this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
			}
		}

		if (utf8_strlen($this->request->post['keyword']) > 0) {
      $this->load->model('design/seo_url');

			$url_alias_info = $this->model_design_seo_url->getSeoUrlsByKeyword($this->request->post['keyword']);

			if ($url_alias_info) {
				$this->error['keyword'] = $this->language->get('error_keyword_exist');
			} else {
			  $url_alias_info = $this->model_extension_ocfilter_page->getUrlAlias($this->request->post['keyword']);

  			if ($url_alias_info && isset($this->request->get['ocfilter_page_id']) && $url_alias_info['query'] != 'ocfilter_page_id=' . $this->request->get['ocfilter_page_id']) {
  				$this->error['keyword'] = sprintf($this->language->get('error_keyword_exist'));
  			}

  			if ($url_alias_info && !isset($this->request->get['ocfilter_page_id'])) {
  				$this->error['keyword'] = sprintf($this->language->get('error_keyword_exist'));
  			}
      }
		}

    if (!$this->request->post['category_id']) {
			$this->error['category'] = $this->language->get('error_category');
    }

    if (!$this->request->post['params']) {
			$this->error['params'] = $this->language->get('error_params');
    }

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/module/ocfilter')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function install() {
    // DB
    $query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "ocfilter_option'");

    if (!$query->num_rows) {
    	$install = array();

      $install[] = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ocfilter_option`                     (`option_id` INT(11) NOT NULL AUTO_INCREMENT, `type` VARCHAR(16) NOT NULL DEFAULT 'checkbox', `keyword` VARCHAR(255) NOT NULL DEFAULT '', `selectbox` TINYINT(1) NOT NULL DEFAULT '0', `grouping` TINYINT(2) NOT NULL DEFAULT '0', `color` TINYINT(1) NOT NULL DEFAULT '0', `image` TINYINT(1) NOT NULL DEFAULT '0', `status` TINYINT(1) NOT NULL DEFAULT '1', `sort_order` INT(11) NOT NULL DEFAULT '0', PRIMARY KEY (`option_id`), KEY `keyword` (`keyword`), KEY `sort_order` (`sort_order`)) ENGINE=MyISAM DEFAULT CHARSET=utf8";
      $install[] = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ocfilter_option_description`         (`option_id` INT(11) NOT NULL, `language_id` TINYINT(2) NOT NULL, `name` VARCHAR(255) NOT NULL DEFAULT '', `postfix` VARCHAR(32) NOT NULL DEFAULT '', `description` VARCHAR(255) NOT NULL DEFAULT '', PRIMARY KEY (`option_id`,`language_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8";
      $install[] = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ocfilter_option_to_category`         (`option_id` INT(11) NOT NULL, `category_id` INT(11) NOT NULL, PRIMARY KEY (`category_id`,`option_id`), KEY `category_id` (`category_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8";
      $install[] = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ocfilter_option_to_store`            (`option_id` INT(11) NOT NULL, `store_id` INT(11) NOT NULL, PRIMARY KEY (`store_id`,`option_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8";

      $install[] = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ocfilter_option_value`               (`value_id` BIGINT(20) NOT NULL AUTO_INCREMENT, `option_id` INT(11) NOT NULL DEFAULT '0', `keyword` VARCHAR(255) NOT NULL DEFAULT '', `color` VARCHAR(6) NOT NULL DEFAULT '', `image` VARCHAR(255) NOT NULL DEFAULT '', `sort_order` INT(11) NOT NULL DEFAULT '0', PRIMARY KEY (`value_id`), KEY `option_id` (`option_id`), KEY `keyword` (`keyword`)) ENGINE=MyISAM DEFAULT CHARSET=utf8";
      $install[] = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ocfilter_option_value_description`   (`value_id` BIGINT(20) NOT NULL, `option_id` INT(11) NOT NULL, `language_id` TINYINT(2) NOT NULL, `name` VARCHAR(255) NOT NULL DEFAULT '', PRIMARY KEY (`value_id`,`language_id`), KEY `option_id` (`option_id`), KEY `name` (`name`)) ENGINE=MyISAM DEFAULT CHARSET=utf8";
      $install[] = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ocfilter_option_value_to_product`    (`ocfilter_option_value_to_product_id` INT(11) NOT NULL AUTO_INCREMENT, `product_id` INT(11) NOT NULL, `option_id` INT(11) NOT NULL, `value_id` BIGINT(20) NOT NULL, `slide_value_min` DECIMAL(15,4) NOT NULL DEFAULT '0.0000', `slide_value_max` DECIMAL(15,4) NOT NULL DEFAULT '0.0000', PRIMARY KEY (`ocfilter_option_value_to_product_id`), UNIQUE INDEX `option_id_value_id_product_id` (`option_id`, `value_id`, `product_id`), INDEX `slide_value_min_slide_value_max` (`slide_value_min`, `slide_value_max`), INDEX `product_id` (`product_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8";
      $install[] = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ocfilter_option_value_to_product_description` (`product_id` INT(11) NOT NULL, `value_id` BIGINT(20) NOT NULL, `option_id` INT(11) NOT NULL, `language_id` TINYINT(2) NOT NULL, `description` VARCHAR(255) NOT NULL DEFAULT '', PRIMARY KEY (`product_id`,`value_id`,`option_id`,`language_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8";

      $install[] = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ocfilter_page`                       (`ocfilter_page_id` INT(11) NOT NULL AUTO_INCREMENT, `category_id` INT(11) NOT NULL DEFAULT '0', `keyword` VARCHAR(255) NOT NULL, `params` VARCHAR(255) NOT NULL, `over` SET('domain','category') NOT NULL DEFAULT 'category', `status` TINYINT(1) NOT NULL DEFAULT '1', PRIMARY KEY (`ocfilter_page_id`), INDEX `keyword` (`keyword`), INDEX `category_id_params` (`category_id`, `params`)) ENGINE=MyISAM DEFAULT CHARSET=utf8";
      $install[] = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ocfilter_page_description`           (`ocfilter_page_id` INT(11) NOT NULL DEFAULT '0', `language_id` INT(11) NOT NULL DEFAULT '0', `meta_title` VARCHAR(255) NOT NULL, `meta_keyword` VARCHAR(255) NOT NULL, `meta_description` VARCHAR(255) NOT NULL, `description` TEXT NOT NULL, `title` VARCHAR(128) NOT NULL, PRIMARY KEY (`ocfilter_page_id`, `language_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8";

      foreach ($install as $sql) {
        $this->db->query($sql);
      }
    }
	}

	public function uninstall() {

	}
}