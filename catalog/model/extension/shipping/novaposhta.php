<?php
class ModelExtensionShippingNovaposhta extends Model {
    function getQuote($address) {
        $this->load->language('extension/shipping/novaposhta');

        if ($this->config->get('shipping_novaposhta_status')) {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int) $this->config->get('novaposhta_geo_zone_id') . "' AND country_id = '" . (int) $address['country_id'] . "' AND (zone_id = '" . (int) $address['zone_id'] . "' OR zone_id = '0')");

            if (!$this->config->get('shipping_novaposhta_geo_zone_id')) {
                $status = true;
            } elseif ($query->num_rows) {
                $status = true;
            } else {
                $status = false;
            }
			
        } else {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            $quote_data = array();
			
            if ($this->config->get('shipping_novaposhta_min_total_for_free_delivery') > $this->cart->getSubTotal()) {
                $cost = (($this->cart->getWeight() * $this->config->get('shipping_novaposhta_delivery_price')) + $this->config->get('shipping_novaposhta_delivery_order') + ($this->cart->getSubTotal() * $this->config->get('shipping_novaposhta_delivery_insurance') / 100) + ($this->cart->getSubTotal() * $this->config->get('shipping_novaposhta_delivery_nal') / 100));
            } else {
                $cost = 0.00;
            }
			
            if ($this->config->get('shipping_novaposhta_name')) {
                $shipping_name = $this->config->get('shipping_novaposhtacopy_name');
            } else {
                $shipping_name = $this->language->get('text_description');
            }

            $quote_data['novaposhta'] = array(
                'code' => 'novaposhta.novaposhta',
				'title' => $shipping_name,
                'cost' => $cost,
                'tax_class_id' => 0,
                'text' => $this->currency->format($cost, $this->session->data['currency'])
            );

            $method_data = array(
                'code' => 'novaposhta',
				'title' => $this->language->get('text_title'),
                'quote' => $quote_data,
                'sort_order' => $this->config->get('shipping_novaposhta_sort_order'),
                'error'      => false
            );
        }

        return $method_data;
    }

}