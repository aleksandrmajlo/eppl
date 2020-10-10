<?php

class ControllerExtensionModulecatid extends Controller
{
    public function index($setting)
    {
        if (isset($setting['name'][$this->config->get('config_language_id')])) {

            $data['name'] = $setting['name'];
            $catids = $setting["module_description"][$this->config->get('config_language_id')]['title'];
            $data['categories'] = [];
            if (!empty($catids)) {
                $catids = explode(',', $catids);
                if ($catids) {
                    $this->load->model('catalog/category');
                    $this->load->model('catalog/product');
                    $url = "";

                    foreach ($catids as $catid) {
                        $category = $this->model_catalog_category->getCategory($catid);
                        if ($category) {
                            if ($category['image']) {
                                $thumb = '/image/' . $category['image'];
                            } else {
                                $thumb = '';
                            }
                            $dataFilter = array(
                                'filter_category_id' => $category['category_id'],
                                'filter_sub_category' => false
                            );
                            $price_min = $this->model_catalog_product->getMinMaxPrice($dataFilter, 'min');
                            $price_min = number_format((int)$price_min, 0, ',', ' ');
                            $data['categories'][] = [
                                'price_min' => $price_min,
                                'thumb' => $thumb,
                                'name' => $category['name'],
                                'href' => $this->url->link('product/category', 'path=' . (int)$category['category_id'] . $url)
                            ];
                        }
                    }
                }
            }
            return $this->load->view('extension/module/catid', $data);
        }
    }
}
