<?php


namespace JFB\Converter\Compatibility\JetEngine\Actions;

use JFB\Converter\Actions\Interfaces\ActionResolver;
use JFB\Converter\Actions\Traits\BaseActionIterator;
use JFB\Converter\Actions\Traits\BaseActionSettingsResolver;
use JFB\Converter\Compatibility\JetEngine\Actions\Traits\DefaultActionTypeResolver;

class InsertAppointmentAction implements ActionResolver {

	use BaseActionIterator;
	use BaseActionSettingsResolver;
	use DefaultActionTypeResolver;

	public function is_supported( array $action ): bool {
		return 'insert_appointment' === ( $action['type'] ?? '' );
	}

	public function resolve_settings( array $action ) {
		foreach ( $action as $prop => $value ) {
			if (
				0 !== strpos( $prop, 'appointment_' ) &&
				0 !== strpos( $prop, 'wc_fields_map__' ) &&
				0 !== strpos( $prop, 'appointment_custom_field_' )
			) {
				continue;
			}

			$this->settings[ $prop ] = $value;
		}
	}

}
