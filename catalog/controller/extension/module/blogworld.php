<?php

class ControllerExtensionModuleBlogworld extends Controller
{
    public function index($setting)
    {
        $this->load->model('setting/module');
        $data['articles'] = [];
        $url = '';
        $articles = $setting["module_description"][2]["title"];
        if (!empty($articles)) {
            $articles = explode(',', $articles);
            $this->load->model('extension/d_blog_module/post');
            $this->load->model('extension/d_blog_module/category');
            $this->load->model('tool/image');
            if ($articles) {
                foreach ($articles as $article) {
                    $post_info = $this->model_extension_d_blog_module_post->getPost($article);
                    if ($post_info['image']) {
                        $thumb = $this->model_tool_image->resize($post_info['image'], 570, 340);
                    } else {
                        $thumb = '';
                    }
                    $categories = $this->model_extension_d_blog_module_category->getCategoryByPostId($post_info['post_id']);
                    $categoriesData = array();
                    foreach ($categories as $category) {
                        $categoriesData[] = array(
                            'title' => $category['title'],
                            'href' => $this->url->link('extension/d_blog_module/category', 'category_id=' . $category['category_id'] . $url, 'SSL')
                        );
                    }
                    $data['articles'][] = [
                        'thumb' => $thumb,
                        'title' => $post_info["title"],
                        'categories' =>$categoriesData,
                        "short_description" => utf8_substr(strip_tags(html_entity_decode($post_info['short_description'], ENT_QUOTES, 'UTF-8')), 0, 100).'...',
                        'href' => $this->url->link('extension/d_blog_module/post', 'post_id=' . $post_info['post_id'] . $url, 'SSL'),

                    ];
                }
            }
        }
        return $this->load->view('extension/module/blogworld', $data);
    }
}
