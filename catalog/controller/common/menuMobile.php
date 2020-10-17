<?php
class ControllerCommonMenuMobile extends Controller {
    public function index() {
        $this->load->language('common/header');
        $this->load->language('common/menu');
        // Menu
        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
        $data['categories'] = array();
        $categories = $this->model_catalog_category->getCategories(0);
        foreach ($categories as $category) {

            if ($category['top']) {
                // Level 2
                $children_data = array();
                $children = $this->model_catalog_category->getCategories($category['category_id']);
                foreach ($children as $child) {
                    $filter_data = array(
                        'filter_category_id'  => $child['category_id'],
                        'filter_sub_category' => true
                    );

                    $children_data[] = array(
                        'name'  => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
                        'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
                    );
                }
                // Level 1
                $data['categories'][] = array(
                    'name'     => $category['name'],
                    'children' => $children_data,
                    'column'   => $category['column'] ? $category['column'] : 1,
                    'href'     => $this->url->link('product/category', 'path=' . $category['category_id'])
                );
            }
        }

        $this->load->model('catalog/information');
        $data['linkBottom']=[];
        $PageMobiles=[4,['information/contact','Контакти'],5,6,9];
        foreach ($PageMobiles as $pageMobile){
            if(is_numeric($pageMobile)){
                $article=$this->model_catalog_information->getInformation($pageMobile);
                $data['linkBottom'][]=[
                    "title"=>$article["title"],
                    'href'  => $this->url->link('information/information', 'information_id=' .$pageMobile)
                ] ;
            }else{
                $data['linkBottom'][]=[
                    "title"=>$pageMobile[1],
                    'href'  => $this->url->link($pageMobile[0])
                ] ;
            }
        }

        $data['logged'] = $this->customer->isLogged();

        $data['account'] = $this->url->link('account/account', '', true);
        $data['register'] = $this->url->link('account/register', '', true);
        $data['login'] = $this->url->link('account/login', '', true);
        $data['order'] = $this->url->link('account/order', '', true);
        $data['transaction'] = $this->url->link('account/transaction', '', true);
        $data['download'] = $this->url->link('account/download', '', true);
        $data['logout'] = $this->url->link('account/logout', '', true);

        $data['dellivery'] = $this->url->link('information/information', 'information_id=' . 6);

        return $this->load->view('common/menuMobile', $data);
    }
}
