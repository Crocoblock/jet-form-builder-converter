<?php


namespace Jet_Form_Builder_Converter;


use Jet_Form_Builder_Converter\Migrations\Migrate_Manager;

class Post_Row_Actions {

	public function __construct() {
		add_filter( 'post_row_actions', array( $this, 'action_links' ), 20, 2 );
	}

	public function check_user_access( $post_id = null ) {
		$res = true;

		if ( ! current_user_can( 'edit_posts' ) ) {
			$res = false;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			$res = false;
		}

		return $res;
	}

	public function action_links( $actions, $post ) {
		if ( ! $this->check_user_access( $post->ID ) ) {
			return $actions;
		}

		if ( 'jet-engine-booking' !== $post->post_type ) {
			return $actions;
		}
		$action_name = Migrate_Manager::instance()->action_name();

		$actions[ $action_name ] = $this->get_converted_url( $post->ID );

		$trash = ! empty( $actions['trash'] ) ? $actions['trash'] : false;

		if ( $trash ) {
			unset( $actions['trash'] );
			$actions['trash'] = $trash;
		}

		return $actions;
	}

	private function get_converted_url( $id ) {
		$action_name = Migrate_Manager::instance()->action_name();
		$admin_url   = esc_url( admin_url() );

		$convert_url = add_query_arg(
			array(
				'action' => $action_name,
				'id'     => $id,
			),
			$admin_url
		);

		return sprintf(
			'<b><a href="%1$s" title="%2$s" rel="permalink">%2$s</a></b>',
			$convert_url,
			__( 'Convert to new builder', 'jet-form-builder-convert' )
		);
	}

}