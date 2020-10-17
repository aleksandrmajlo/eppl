<?php
class ControllerCommonHome extends Controller {
	public function index() {
		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

		if (isset($this->request->get['route'])) {
			$this->document->addLink($this->config->get('config_url'), 'canonical');
		}

		//************************
        $this->load->model('design/banner');
        $this->load->model('tool/image');
        $data['banners'] = array();

        $results = $this->model_design_banner->getBanner(7);
        if($results){
            foreach ($results as $result) {
                if (is_file(DIR_IMAGE . $result['image'])) {
                    $title=explode(';',$result['title']);
                    $data['banners'][] = array(
                        'title' => $title[0],
                        'price' => $title[1],
                        'link'  => $result['link'],
                        'image' => $this->model_tool_image->resize($result['image'], 150,200)
                    );
                }
            }
        }
		//************************
        $data['cartLink'] = $this->url->link('checkout/cart');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('common/home', $data));
	}
}
