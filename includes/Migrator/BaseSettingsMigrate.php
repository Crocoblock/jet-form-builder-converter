<?php


namespace JFB\Converter\Migrator;


abstract class BaseSettingsMigrate {

	protected $value;

	public function __construct( $value ) {
		$this->value = $this->parse_value( $value );
	}

	abstract protected function parse_value( $value );

	public function value() {
		return $this->value;
	}

}