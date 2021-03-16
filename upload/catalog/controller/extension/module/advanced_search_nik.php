<?php
class ControllerExtensionModuleAdvancedSearchNik extends Controller {
	public function index() {
		$this->load->language('extension/module/advanced_search_nik');

        $this->load->model('setting/setting');
        $this->load->model('extension/module/advanced_search_nik');

        $data = $this->model_setting_setting->getSetting('module_advanced_search_nik');

        if ($data['module_advanced_search_nik_display_category']) {
            $data['categories'] = $this->model_extension_module_advanced_search_nik->getCategories();
        }

		return $this->load->view('extension/module/advanced_search_nik', $data);
	}

    public function autocomplete() {
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $this->load->model('catalog/product');
            $this->load->model('extension/module/advanced_search_nik');
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

            $results = $this->model_extension_module_advanced_search_nik->getProducts($filter_data);

            $products_categories = array();
            foreach ($results as $result) {
                $product_categories = $this->model_extension_module_advanced_search_nik->getCategoriesByProduct($result['product_id']);

                foreach ($product_categories as $product_category) {
                    if (!in_array($product_category, $products_categories)) {
                        $products_categories[] = $product_category;
                    }
                }
            }

            $categories_info = array();

            foreach ($products_categories as $product_category) {
                $categories_info[] = $this->model_extension_module_advanced_search_nik->getCategory($product_category['category_id']);
            }

            foreach ($categories_info as $category_info) {
                $json[] = array(
                    'product_id' => $category_info['category_id'],
                    'name' => $category_info['name'],
                    'type' => 'category'
                );
            }

            $products_manufacturers = array();

            foreach ($results as $result) {
                $product_manufacturer= $this->model_extension_module_advanced_search_nik->getManufacturer($result['manufacturer_id']);
                if(!in_array($product_manufacturer, $products_manufacturers)) {
                    $products_manufacturers[] = $product_manufacturer;
                }
            }
            
            foreach ($products_manufacturers as $product_manufacturer) {
                $json[] = array(
                    'product_id' => $product_manufacturer['manufacturer_id'],
                    'name' => $product_manufacturer['name'],
                    'type' => 'manufacturer'
                );
            }

            foreach ($results as $result) {
                $json[] = array(
                    'product_id'  => $result['product_id'],
                    'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                    'model'       => $result['model'],
                    'manufacturer'=> $result['manufacturer'],
                    'price'       => $result['price'],
                );
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}