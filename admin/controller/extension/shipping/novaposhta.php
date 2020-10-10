<?php
class ControllerExtensionShippingNovaposhta extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/shipping/novaposhta');

	    $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('shipping_novaposhta', $this->request->post);		
					
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true));
		}

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        // хлебные крошки
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/shipping/novaposhta', 'user_token=' . $this->session->data['user_token'], true)
		);

        // ссылки для кнопок Сохранить и Отменить
		$data['action'] = $this->url->link('extension/shipping/novaposhta', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true);

        if (isset($this->request->post['shipping_novaposhta_name'])) {
            $data['shipping_novaposhta_name'] = $this->request->post['shipping_novaposhta_name'];
        } else {
            $data['shipping_novaposhta_name'] = $this->config->get('shipping_novaposhta_name');
        }

        if (isset($this->request->post['shipping_novaposhta_min_total_for_free_delivery'])) {
            $data['shipping_novaposhta_min_total_for_free_delivery'] = $this->request->post['shipping_novaposhta_min_total_for_free_delivery'];
        } else {
            $data['shipping_novaposhta_min_total_for_free_delivery'] = $this->config->get('shipping_novaposhta_min_total_for_free_delivery');
        }

        if (isset($this->request->post['shipping_novaposhta_delivery_order'])) {
            $data['shipping_novaposhta_delivery_order'] = $this->request->post['shipping_novaposhta_delivery_order'];
        } else {
            $data['shipping_novaposhta_delivery_order'] = $this->config->get('shipping_novaposhta_delivery_order');
        }

        if (isset($this->request->post['shipping_novaposhta_delivery_price'])) {
            $data['shipping_novaposhta_delivery_price'] = $this->request->post['shipping_novaposhta_delivery_price'];
        } else {
            $data['shipping_novaposhta_delivery_price'] = $this->config->get('shipping_novaposhta_delivery_price');
        }

        if (isset($this->request->post['shipping_novaposhta_delivery_insurance'])) {
            $data['shipping_novaposhta_delivery_insurance'] = $this->request->post['shipping_novaposhta_delivery_insurance'];
        } else {
            $data['shipping_novaposhta_delivery_insurance'] = $this->config->get('shipping_novaposhta_delivery_insurance');
        }

        if (isset($this->request->post['shipping_novaposhta_delivery_nal'])) {
            $data['shipping_novaposhta_delivery_nal'] = $this->request->post['shipping_novaposhta_delivery_nal'];
        } else {
            $data['shipping_novaposhta_delivery_nal'] = $this->config->get('shipping_novaposhta_delivery_nal');
        }

		if (isset($this->request->post['shipping_novaposhta_tax_class_id'])) {
			$data['shipping_novaposhta_tax_class_id'] = $this->request->post['shipping_novaposhta_tax_class_id'];
		} else {
			$data['shipping_novaposhta_tax_class_id'] = $this->config->get('shipping_novaposhta_tax_class_id');
		}

		$this->load->model('localisation/tax_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

        if (isset($this->request->post['shipping_novaposhta_geo_zone_id'])) {
            $data['shipping_novaposhta_geo_zone_id'] = $this->request->post['shipping_novaposhta_geo_zone_id'];
        } else {
            $data['shipping_novaposhta_geo_zone_id'] = $this->config->get('shipping_novaposhta_geo_zone_id');
        }

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['shipping_novaposhta_status'])) {
			$data['shipping_novaposhta_status'] = $this->request->post['shipping_novaposhta_status'];
		} else {
			$data['shipping_novaposhta_status'] = $this->config->get('shipping_novaposhta_status');
		}

		if (isset($this->request->post['shipping_novaposhta_sort_order'])) {
			$data['shipping_novaposhta_sort_order'] = $this->request->post['shipping_novaposhta_sort_order'];
		} else {
			$data['shipping_novaposhta_sort_order'] = $this->config->get('shipping_novaposhta_sort_order');
		}
        
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/shipping/novaposhta', $data));
    }

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/shipping/novaposhta')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

}

