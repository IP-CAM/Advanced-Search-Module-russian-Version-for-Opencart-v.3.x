<?php
class ControllerExtensionModuleAdvancedSearchNik extends Controller {
	public function index() {
		$this->load->language('extension/module/advanced_search_nik');

        $this->load->model('setting/setting');

        $data = $this->model_setting_setting->getSetting('module_advanced_search_nik');

//        var_dump($data);

		return $this->load->view('extension/module/advanced_search_nik', $data);
	}
}