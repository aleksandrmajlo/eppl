<?php

class ControllerCommonAjax extends Controller {

    public function cart(){

        $this->load->model('tool/image');
        $this->load->model('tool/upload');
        $data['products'] = array();
        foreach ($this->cart->getProducts() as $product) {
            if ($product['image']) {
                $image = $this->model_tool_image->resize($product['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_height'));
            } else {
                $image = '';
            }
            $option_data = array();
            foreach ($product['option'] as $option) {
                if ($option['type'] != 'file') {
                    $value = $option['value'];
                } else {
                    $upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

                    if ($upload_info) {
                        $value = $upload_info['name'];
                    } else {
                        $value = '';
                    }
                }

                $option_data[] = array(
                    'name'  => $option['name'],
                    'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value),
                    'type'  => $option['type']
                );
            }
            // Display prices
            if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                $unit_price = $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'));
                $price = $this->currency->format($unit_price, $this->session->data['currency']);
                $total = $this->currency->format($unit_price * $product['quantity'], $this->session->data['currency']);
            } else {
                $price = false;
                $total = false;
            }
            $data['products'][] = array(
                'cart_id'   => $product['cart_id'],
                'thumb'     => $image,
                'name'      => $product['name'],
                'model'     => $product['model'],
                'option'    => $option_data,
                'recurring' => ($product['recurring'] ? $product['recurring']['name'] : ''),
                'quantity'  => $product['quantity'],
                'price'     => $price,
                'total'     => $total,
                'href'      => $this->url->link('product/product', 'product_id=' . $product['product_id'])
            );
        }

        $this->response->setOutput($this->load->view('ajax/ajax_cart', $data));
    }

    public function oneclick(){
        if (($this->request->server['REQUEST_METHOD'] == 'POST') ) {

            $this->load->model('catalog/product');

            $product_id = $this->request->post['product_id'];
            $quantity=1;
            $tel= $this->request->post['tel'];
            $product_info = $this->model_catalog_product->getProduct($product_id);
            $price = isset($product_info['special']) ? $product_info['special'] : $product_info['price'];
            $total = $price*$quantity;
            $currency_id = $this->session->data['currency'];
            $currency_code =  $this->session->data['currency'];
            $name='Купити в 1 клік';
            $nameOne='Oneclick';

            $number = 1;
            $username_length = 5;
            $email_random=$this->generate_emails($number,$username_length);

            $this->db->query("INSERT INTO " . DB_PREFIX . "order SET telephone = '" . $this->db->escape($tel) . "',
                                           firstname = '" . $nameOne. "', 
                                           lastname = '" . $nameOne . "',
                                           total = '" . (float) $total . "', 
                                            date_added = NOW(), date_modified = NOW(),
                                           currency_code = '" . $this->db->escape($currency_code) . "', 
                                           email = '" . $this->db->escape($email_random) . "',
                                           comment='Купити в 1 клік', order_status_id='1'");
            $order_id = $this->db->getLastId();
            $this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET 
                       order_id = '" . (int)$order_id . "', 
                       product_id = '" . (int)$product_id . "', 
                       name = '" . $this->db->escape($product_info['name']) . "', 
                       model = '" . $this->db->escape($product_info['model']) . "', 
                       quantity = '" . (int)$quantity . "', 
                       price = '" . (float)$price . "', 
                       total = '" . (float)$total . "'");
            echo json_encode(['suc'=>true]);
        }
        else{
            echo json_encode(['suc'=>false]);
        }
    }

    public function generate_emails($number, $username_length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $char_count = count($characters);
        $tld = array("com", "net", "biz");
        $randomName = '';
        for($j=0; $j<$username_length; $j++){
            $randomName .= $characters[rand(0, strlen($characters) -1)];
        }
        $k = array_rand($tld);
        $extension = $tld[$k];
        $fullAddress = $randomName . "@" ."oneclick.".$extension;
        return $fullAddress;
    }


    // поиск продукта
    public function livesearch(){
        if (($this->request->server['REQUEST_METHOD'] == 'POST') ) {
            if (isset($this->request->post['search'])) {
                $url="";
                $this->load->model('catalog/category');
                $search=html_entity_decode($this->request->post['search'], ENT_QUOTES, 'UTF-8');
                $sort = 'p.sort_order';
                $order = 'ASC';
                $limit=10;
                $filter_data = array(
                    'filter_name'         => $search,
//                    'filter_tag'          => $tag,
//                    'filter_description'  => $description,
//                    'filter_category_id'  => $category_id,
//                    'filter_sub_category' => $sub_category,
                    'sort'                => $sort,
                    'order'               => $order,
                    'start'               => 0 * $limit,
                    'limit'               => $limit
                );
                $product_total = $this->model_catalog_product->getTotalProducts($filter_data);
                $results = $this->model_catalog_product->getProducts($filter_data);
                $data['products']=[];
                if($results){
                     foreach ($results as $result){
                         $data['products'][]=[
                             'product_id'  => $result['product_id'],
                             'name'        => $result['name'],
                             'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'] . $url)
                         ]  ;
                     }
                }
                echo json_encode($data['products']);
            }
        }

    }

    // загрузка товар с ним покупают в модалку
    public  function related(){
        $product_id = $this->request->post['product_id'];
        if($product_id){
            $this->load->model('catalog/product');
            $this->load->model('tool/image');
            $results = $this->model_catalog_product->getProductRelated($product_id);
            $data['products'] = array();
            foreach ($results as $result) {
                if ($result['image']) {
                    $image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_height'));
                } else {
                    $image="";
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
                    $rating = (int)$result['rating'];
                } else {
                    $rating = false;
                }
                $data['products'][] = array(
                    'product_id'  => $result['product_id'],
                    'thumb'       => $image,
                    'name'        => $result['name'],
                    'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
                    'price'       => $price,
                    'special'     => $special,
                    'tax'         => $tax,
                    'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
                    'rating'      => $rating,
                    'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
                );
            }
            $this->response->setOutput($this->load->view('ajax/ajax_related', $data));
        }

    }

}


