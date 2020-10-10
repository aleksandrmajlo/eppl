<?php
/**
 * @package		OpenCart
 * @author		Daniel Kerr
 * @copyright	Copyright (c) 2005 - 2017, OpenCart, Ltd. (https://www.opencart.com/)
 * @license		https://opensource.org/licenses/GPL-3.0
 * @link		https://www.opencart.com
*/

/**
* Pagination class
*/
class Pagination {
	public $total = 0;
	public $page = 1;
	public $limit = 20;
	public $num_links = 4;
	public $url = '';
	public $text_first = '|&lt;';
	public $text_last = '&gt;|';
	public $text_next = '&gt;';
	public $text_prev = '&lt;';

	/**
     * 
     *
     * @return	text
     */
	public function render() {
		$total = $this->total;

		if ($this->page < 1) {
			$page = 1;
		} else {
			$page = $this->page;
		}

		if (!(int)$this->limit) {
			$limit = 10;
		} else {
			$limit = $this->limit;
		}

		$num_links = $this->num_links;
		$num_pages = ceil($total / $limit);

		$this->url = str_replace('%7Bpage%7D', '{page}', $this->url);

		$output = '<div class="pagination-wrapper">
                   <ul class="pagination page-numbers">';

		if ($page > 1) {
			$output .= '<li><a  class="page-numbers" href="' . str_replace(array('&amp;page={page}', '?page={page}', '&page={page}'), '', $this->url) . '">' . 1 . '</a></li>';
			
//			if ($page - 1 === 1) {
//				$output .= '<li><a  class="page-numbers" href="' . str_replace(array('&amp;page={page}', '?page={page}', '&page={page}'), '', $this->url) . '">' . $this->text_prev . '</a></li>';
//			} else {
//				$output .= '<li><a  class="page-numbers" href="' . str_replace('{page}', $page - 1, $this->url) . '">' . $this->text_prev . '</a></li>';
//			}
		}
        if ($page == 1) {
            $output .= '<li> <span class="page-numbers current"> ' . 1 . '</span></li>';
        }


		if ($num_pages > 1) {
			if ($num_pages <= $num_links) {
				$start = 1;
				$end = $num_pages;
			} else {
				$start = $page - floor($num_links / 2);
				$end = $page + floor($num_links / 2);
				if ($start < 1) {
					$end += abs($start) + 1;
					$start = 1;
				}
				if ($end > $num_pages) {
					$start -= ($end - $num_pages);
					$end = $num_pages;
				}
			}
			if($start>2){
			    $output.= '<li><a  class="page-numbers" href="' . str_replace('{page}', $start-1, $this->url) . '">....</a></li>';
            }
			for ($i = $start; $i <= $end; $i++) {
			    if($i==$num_pages || $i==1) continue;

				if ($page == $i) {
					$output .= '<li class=""><span class="page-numbers current">' . $i . '</span></li>';
				} else {

				    if ($i === 1) {
						$output .= '<li><a class="page-numbers" href="' . str_replace(array('&amp;page={page}', '?page={page}', '&page={page}'), '', $this->url) . '">' . $i . '</a></li>';
					} else {
						$output .= '<li><a  class="page-numbers" href="' . str_replace('{page}', $i, $this->url) . '">' . $i . '</a></li>';
					}
				}
			}
			if($num_pages-2>$end){
                $output.= '<li><a  class="page-numbers" href="' . str_replace('{page}', $end+1, $this->url) . '">....</a></li>';
            }
		}

//		if ($page < $num_pages) {
//			$output .= '<li><a  class="page-numbers" href="' . str_replace('{page}', $page + 1, $this->url) . '">' . $this->text_next . '</a></li>';
        if($num_pages==$page){
            $output .= '<li><span class="page-numbers current">' . $num_pages . '</span></li>';
        }else{
            $output .= '<li><a  class="page-numbers" href="' . str_replace('{page}', $num_pages, $this->url) . '">' . $num_pages . '</a></li>';
        }

//		}

		$output .= '</ul></div>';

		if ($num_pages > 1) {
			return $output;
		} else {
			return '';
		}
	}
}
