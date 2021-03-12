<?php
class ControllerExtensionModuleAdvancedSearchNik extends Controller {
	public function index() {
		$this->load->language('extension/module/advanced_search_nik');

        $this->load->model('setting/setting');
        $this->load->model('extension/module/advanced_search_nik');

        $data = $this->model_setting_setting->getSetting('module_advanced_search_nik');

//        var_dump($data);

        if ($data['module_advanced_search_nik_display_category']) {
            $data['categories'] = $this->model_extension_module_advanced_search_nik->getCategories();
        }

		return $this->load->view('extension/module/advanced_search_nik', $data);
	}

    public function autocomplete() {
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $this->load->model('catalog/product');
            $this->load->model('setting/setting');

            $data = $this->model_setting_setting->getSetting('module_advanced_search_nik');

            if(!$data['module_advanced_search_nik_count_items_for_display']) {
                $data['module_advanced_search_nik_count_items_for_display'] = 10;
            }

            if (isset($this->request->get['filter_name'])) {
                $filter_name = $this->request->get['filter_name'];
            } else {
                $filter_name = '';
            }

            if (isset($this->request->get['filter_category'])) {
                $filter_category = $this->request->get['filter_category'];
            } else {
                $filter_category = '';
            }

            if ($data['module_advanced_search_nik_count_items_for_display']) {
                $limit = (int)$data['module_advanced_search_nik_count_items_for_display'];
            } else {
                $limit = 10;
            }

            $filter_data = array(
                'filter_name'        => $filter_name,
                'filter_category_id' => $filter_category,
                'start'              => 0,
                'limit'              => $limit
            );

            $results = $this->model_catalog_product->getProducts($filter_data);

            foreach ($results as $result) {
                $option_data = array();

                $product_options = $this->model_catalog_product->getProductOptions($result['product_id']);

                foreach ($product_options as $product_option) {
                    $option_info = $this->model_catalog_option->getOption($product_option['option_id']);

                    if ($option_info) {
                        $product_option_value_data = array();

                        foreach ($product_option['product_option_value'] as $product_option_value) {
                            $option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);

                            if ($option_value_info) {
                                $product_option_value_data[] = array(
                                    'product_option_value_id' => $product_option_value['product_option_value_id'],
                                    'option_value_id'         => $product_option_value['option_value_id'],
                                    'name'                    => $option_value_info['name'],
                                    'price'                   => (float)$product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
                                    'price_prefix'            => $product_option_value['price_prefix']
                                );
                            }
                        }

                        $option_data[] = array(
                            'product_option_id'    => $product_option['product_option_id'],
                            'product_option_value' => $product_option_value_data,
                            'option_id'            => $product_option['option_id'],
                            'name'                 => $option_info['name'],
                            'type'                 => $option_info['type'],
                            'value'                => $product_option['value'],
                            'required'             => $product_option['required']
                        );
                    }
                }

                $json[] = array(
                    'product_id' => $result['product_id'],
                    'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                    'model'      => $result['model'],
                    'option'     => $option_data,
                    'price'      => $result['price']
                );
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}