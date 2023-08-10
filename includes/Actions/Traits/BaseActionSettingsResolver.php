<?php


namespace JFB\Converter\Actions\Traits;

trait BaseActionSettingsResolver {

	private $settings = array();

	public function set_settings( array $settings ) {
		$this->settings = $settings;
	}

	public function get_settings(): array {
		return $this->settings;
	}

}
