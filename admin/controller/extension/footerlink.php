<?php
class ControllerExtensionfooterlink extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/footerlink');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/footerlink');

		$this->getList();
	}

	public function add() {
		$this->load->language('extension/footerlink');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/footerlink');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_footerlink->addfooterlink($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/footerlink', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('extension/footerlink');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/footerlink');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_footerlink->editfooterlink($this->request->get['footerlink_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/footerlink', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('extension/footerlink');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/footerlink');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $footerlink_id) {
				$this->model_extension_footerlink->deletefooterlink($footerlink_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/footerlink', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'id.title';
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
		

		$url = '';
		
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/footerlink', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);
		
		$data['insert'] = $this->url->link('extension/footerlink/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('extension/footerlink/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['footerlinks'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$footerlink_total = $this->model_extension_footerlink->getTotalfooterlinks();
		//print_r($footerlink_total);die();

		$results = $this->model_extension_footerlink->getfooterlinks($filter_data);

		foreach ($results as $result) {
			$data['footerlinks'][] = array(
				'footerlink_id' => $result['footerlink_id'],
				'title'          => $result['title'],
				'link'          => $result['link'],
				'sort_order'          => $result['sort_order'],
				'selected'       => isset($this->request->post['selected']) && in_array($result['footerlink_id'], $this->request->post['selected']),
				'edit'        => $this->url->link('extension/footerlink/edit', 'user_token=' . $this->session->data['user_token'] . '&footerlink_id=' . $result['footerlink_id'] . $url, true)
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_title'] = $this->language->get('column_title');
		$data['column_link'] = $this->language->get('column_link');
		$data['column_sort_order'] = $this->language->get('column_sort_order');
		$data['column_action'] = $this->language->get('column_action');	

		$data['button_insert'] = $this->language->get('button_insert');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');

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

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_title'] = $this->url->link('extension/footerlink', 'user_token=' . $this->session->data['user_token'] . '&sort=id.title' . $url, true);
		$data['sort_sort_order'] = $this->url->link('extension/footerlink', 'user_token=' . $this->session->data['user_token'] . '&sort=i.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
												
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $footerlink_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('extension/footerlink', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($footerlink_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($footerlink_total - $this->config->get('config_limit_admin'))) ? $footerlink_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $footerlink_total, ceil($footerlink_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/footerlink_list', $data));
	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_form'] = !isset($this->request->get['footerlink_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_loading'] = $this->language->get('text_loading');
		$data['text_enabled'] = $this->language->get('text_enabled');
    	$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_store'] = $this->language->get('entry_store');
		$data['entry_keyword'] = $this->language->get('entry_keyword');
		$data['entry_bottom'] = $this->language->get('entry_bottom');
		$data['entry_top'] = $this->language->get('entry_top');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['text_select'] = $this->language->get('text_select');
		$data['entry_link'] = $this->language->get('entry_link');
		$data['entry_select_heading'] = $this->language->get('entry_select_heading');
		
		$data['tab_general'] = $this->language->get('tab_general');
    	$data['tab_data'] = $this->language->get('tab_data');
		$data['tab_design'] = $this->language->get('tab_design');

		$data['help_filename'] = $this->language->get('help_filename');
		$data['help_mask'] = $this->language->get('help_mask');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_upload'] = $this->language->get('button_upload');

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
		
	 	if (isset($this->error['description'])) {
			$data['error_description'] = $this->error['description'];
		} else {
			$data['error_description'] = array();
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/footerlink', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);
		
		if (!isset($this->request->get['footerlink_id'])) {
			$data['action'] = $this->url->link('extension/footerlink/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/footerlink/edit', 'user_token=' . $this->session->data['user_token'] . '&footerlink_id=' . $this->request->get['footerlink_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('extension/footerlink', 'user_token=' . $this->session->data['user_token'] . $url, true);
		
		$data['user_token'] = $this->session->data['user_token'];
		
		$this->load->model('localisation/language');
		
		$data['languages'] = $this->model_localisation_language->getLanguages();
		
		///////footertitle/////////////
			$data['footertitle']= array();
			$this->load->model('extension/footertitle');
			$cresults= $this->model_extension_footertitle->getfootertitles();
			foreach ($cresults as $cresult) {
			$data['footertitle'][] = array(
				'footertitle_id'   => $cresult['footertitle_id'],
				'title'        => $cresult['title']
			);	
			}
		///////footertitle/////////////
		
		

		if (isset($this->request->get['footerlink_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$footerlink_info = $this->model_extension_footerlink->getfooterlink($this->request->get['footerlink_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];


		if (isset($this->request->post['footerlink_description'])) {
			$data['footerlink_description'] = $this->request->post['footerlink_description'];
		} elseif (isset($this->request->get['footerlink_id'])) {
			$data['footerlink_description'] =$this->model_extension_footerlink->getfooterlinkDescriptions($this->request->get['footerlink_id']);
		} else {
			$data['footerlink_description'] = array();
		}
		
		
		if (isset($this->request->post['link'])) {
			$data['link'] = $this->request->post['link'];
		} elseif (!empty($footerlink_info)) {
			$data['link'] = $footerlink_info['link'];
		} else {
			$data['link'] = '';
		}
	
		if (isset($this->request->post['selectheading'])) {
			$data['selectheading'] = $this->request->post['selectheading'];
		} elseif (!empty($footerlink_info)) {
			$data['selectheading'] = $footerlink_info['selectheading'];
		} else {
			$data['selectheading'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($footerlink_info)) {
			$data['status'] = $footerlink_info['status'];
		} else {
			$data['status'] = 1;
		}	
		
		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($footerlink_info)) {
			$data['sort_order'] = $footerlink_info['sort_order'];
		} else {
			$data['sort_order'] ='';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/footerlink_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/footerlink')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		foreach ($this->request->post['footerlink_description'] as $language_id => $value) {
			if ((utf8_strlen($value['title']) < 3) || (utf8_strlen($value['title']) > 64)) {
				$this->error['title'][$language_id] = $this->language->get('error_title');
			}
		
		
		}
		
		
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
			
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/footerlink')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}


		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

}