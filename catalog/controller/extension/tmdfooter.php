<?php
class ControllerExtensionTmdFooter extends Controller {
	public function index() {
		$this->load->language('extension/tmdfooter');
		
		$this->load->model('catalog/information');

		$data['footer_titles'] = $this->model_catalog_information->getFotterTitle();
		
		return $this->load->view('extension/tmdfooter', $data);
	}
}
