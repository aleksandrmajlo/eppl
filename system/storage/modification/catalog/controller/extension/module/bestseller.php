<?php
class ControllerExtensionModuleBestSeller extends Controller {
	public function index($setting) {
		$this->load->language('extension/module/bestseller');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$data['products'] = array();

		$results = $this->model_catalog_product->getBestSellerProducts($setting['limit']);

		if ($results) {
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = $result['rating'];
				} else {
					$rating = false;
				}





                     //*********************************************************
                $Colir_Array=['color'=>[],'array'=>[]];
                $product_attribute_group_query = $this->db->query("SELECT ag.attribute_group_id, agd.name FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_group ag ON (a.attribute_group_id = ag.attribute_group_id) LEFT JOIN " . DB_PREFIX . "attribute_group_description agd ON (ag.attribute_group_id = agd.attribute_group_id) WHERE pa.product_id = '" . (int)$result['product_id'] . "' AND agd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY ag.attribute_group_id ORDER BY ag.sort_order, agd.name");
                foreach ($product_attribute_group_query->rows as $product_attribute_group) {
                    if($product_attribute_group["attribute_group_id"]=="1"){
                        $product_attribute_query = $this->db->query("SELECT a.attribute_id, ad.name, pa.text FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE pa.product_id = '" . (int)$result['product_id'] . "' AND a.attribute_group_id = '" . (int)$product_attribute_group['attribute_group_id'] . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pa.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY a.sort_order, ad.name");
                        foreach ($product_attribute_query->rows as $product_attribute) {
                            if($product_attribute['attribute_id']=="11"){
                                $Colir_Array['color']=$product_attribute["text"];
                            }
                            if($product_attribute['attribute_id']=="12"){
                                $Colir_Array['array']=$product_attribute["text"];
                            }
                        }
                    }
                }


                $Tresults = $this->model_catalog_product->getProductRelatedColor($result['product_id']);
                $color_products=[];
                foreach ($Tresults as $Tresult){
                    $product_attribute_query = $this->db->query("SELECT a.attribute_id, ad.name, pa.text FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE pa.product_id = '" . (int)$Tresult['product_id'] . "' AND a.attribute_group_id = '" . 1 . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pa.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY a.sort_order, ad.name");
                    $color="";
                    foreach ($product_attribute_query->rows as $product_attribute) {
                        if($product_attribute['attribute_id']=="11"){
                            $color=$product_attribute["text"];
                            break;
                        }
                    }
                    $color_products[]=[
                        "color"=>$color,
                        'href'        => $this->url->link('product/product', 'product_id=' . $Tresult['product_id'])
                    ];
                }

                $Tresults = $this->model_catalog_product->getProductRelatedArray($result['product_id']);
                $array_products=[];
                foreach ($Tresults as $Tresult){
                    $product_attribute_query = $this->db->query("SELECT a.attribute_id, ad.name, pa.text FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE pa.product_id = '" . (int)$Tresult['product_id'] . "' AND a.attribute_group_id = '" . 2 . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pa.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY a.sort_order, ad.name");
                    $array="";
                    foreach ($product_attribute_query->rows as $product_attribute) {
                        if($product_attribute['attribute_id']=="12"){
                            $array=$product_attribute["text"];
                            break;
                        }
                    }
                    $array_products[]=[
                        "array"=>$array,
                        'href'        => $this->url->link('product/product', 'product_id=' . $Tresult['product_id'])
                    ];
                }
                //*********************************************************


	             
				$data['products'][] = array(
					'product_id'  => $result['product_id'],

                     'array_products'=>$array_products,
                    'color_products'=>$color_products,
                    'Colir_Array'=>$Colir_Array,
	             

					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'rating'      => $rating,
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
			}

			return $this->load->view('extension/module/bestseller', $data);
		}
	}
}
