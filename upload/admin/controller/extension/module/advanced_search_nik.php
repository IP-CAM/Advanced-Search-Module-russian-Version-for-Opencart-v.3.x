<?php
class ControllerExtensionModuleAdvancedSearchNik extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/advanced_search_nik');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_advanced_search_nik', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/advanced_search_nik', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/advanced_search_nik', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        $data['user_token'] = $this->session->data['user_token'];

        if (isset($this->request->post['module_advanced_search_nik_display_category'])) {
            $data['module_advanced_search_nik_display_category'] = $this->request->post['module_advanced_search_nik_display_category'];
        } else {
            $data['module_advanced_search_nik_display_category'] = $this->config->get('module_advanced_search_nik_display_category');
        }

        if (isset($this->request->post['module_advanced_search_nik_count_category_for_display'])) {
            $data['module_advanced_search_nik_count_category_for_display'] = $this->request->post['module_advanced_search_nik_count_category_for_display'];
        } else {
            $data['module_advanced_search_nik_count_category_for_display'] = $this->config->get('module_advanced_search_nik_count_category_for_display');
        }

        if (isset($this->request->post['module_advanced_search_nik_display_brands'])) {
            $data['module_advanced_search_nik_display_brands'] = $this->request->post['module_advanced_search_nik_display_brands'];
        } else {
            $data['module_advanced_search_nik_display_brands'] = $this->config->get('module_advanced_search_nik_display_brands');
        }

        if (isset($this->request->post['module_advanced_search_nik_count_brands_for_display'])) {
            $data['module_advanced_search_nik_count_brands_for_display'] = $this->request->post['module_advanced_search_nik_count_brands_for_display'];
        } else {
            $data['module_advanced_search_nik_count_brands_for_display'] = $this->config->get('module_advanced_search_nik_count_brands_for_display');
        }

        if (isset($this->request->post['module_advanced_search_nik_count_items_for_display'])) {
            $data['module_advanced_search_nik_count_items_for_display'] = $this->request->post['module_advanced_search_nik_count_items_for_display'];
        } else {
            $data['module_advanced_search_nik_count_items_for_display'] = $this->config->get('module_advanced_search_nik_count_items_for_display');
        }

        if (isset($this->request->post['module_advanced_search_nik_display_product_image'])) {
            $data['module_advanced_search_nik_display_product_image'] = $this->request->post['module_advanced_search_nik_display_product_image'];
        } else {
            $data['module_advanced_search_nik_display_product_image'] = $this->config->get('module_advanced_search_nik_display_product_image');
        }

        if (isset($this->request->post['module_advanced_search_nik_favorite_products'])) {
            $data['module_advanced_search_nik_favorite_products'] = $this->request->post['module_advanced_search_nik_favorite_products'];
        } else {
            $data['module_advanced_search_nik_favorite_products'] = $this->config->get('module_advanced_search_nik_favorite_products');
        }

        if ($data['module_advanced_search_nik_favorite_products']) {
            $products = $data['module_advanced_search_nik_favorite_products'];
        } else {
            $products = array();
        }

        $this->load->model('catalog/product');

        $data['products'] = array();

        foreach ($products as $product_id) {
            $product_info = $this->model_catalog_product->getProduct($product_id);

            if ($product_info) {
                $data['products'][] = array(
                    'product_id' => $product_info['product_id'],
                    'name'       => $product_info['name']
                );
            }
        }

        if (isset($this->request->post['module_advanced_search_nik_module_class'])) {
            $data['module_advanced_search_nik_module_class'] = $this->request->post['module_advanced_search_nik_module_class'];
        } else {
            $data['module_advanced_search_nik_module_class'] = $this->config->get('module_advanced_search_nik_module_class');
        }

		if (isset($this->request->post['module_advanced_search_nik_status'])) {
			$data['module_advanced_search_nik_status'] = $this->request->post['module_advanced_search_nik_status'];
		} else {
			$data['module_advanced_search_nik_status'] = $this->config->get('module_advanced_search_nik_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/advanced_search_nik', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/advanced_search_nik')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}