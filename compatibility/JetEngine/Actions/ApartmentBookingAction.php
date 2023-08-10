<?php


namespace JFB\Converter\Compatibility\JetEngine\Actions;

use JFB\Converter\Actions\Interfaces\ActionResolver;
use JFB\Converter\Actions\Traits\BaseActionIterator;
use JFB\Converter\Actions\Traits\BaseActionSettingsResolver;
use JFB\Converter\Compatibility\JetEngine\Actions\Traits\DefaultActionTypeResolver;

class ApartmentBookingAction implements ActionResolver {

	const PROPS = array(
		'disable_wc_integration',
	);

	use BaseActionIterator;
	use BaseActionSettingsResolver;
	use DefaultActionTypeResolver;

	public function is_supported( array $action ): bool {
		return 'apartment_booking' === ( $action['type'] ?? '' );
	}

	public function resolve_settings( array $action ) {
		foreach ( $action as $prop => $value ) {
			if (
				! in_array( $prop, self::PROPS, true ) &&
				0 !== strpos( $prop, 'booking_' ) &&
				0 !== strpos( $prop, 'wc_fields_map__' ) &&
				0 !== strpos( $prop, 'db_columns_map_' )
			) {
				continue;
			}

			$this->settings[ $prop ] = $value;
		}
	}

}
