<?php


namespace Jet_Form_Builder_Converter;

abstract class Post_Row_Actions_Base {

	public function __construct() {
		add_filter( 'post_row_actions', array( $this, 'action_links' ), 20, 2 );
	}

	abstract public function get_provider(): string;

	abstract public function get_post_type(): string;

	public function check_user_access( $post_id = null ): bool {
		$res = true;

		if ( ! current_user_can( 'edit_posts' ) ) {
			$res = false;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			$res = false;
		}

		return $res;
	}

	public function check_access( $post ): bool {
		return ( $this->check_user_access( $post->ID ) && 'jet-engine-booking' === $post->post_type );
	}

	public function action_links( $actions, $post ) {
		if ( ! $this->check_access( $post ) ) {
			return $actions;
		}

		$actions[ $this->get_provider() ] = $this->get_converted_url( $post->ID );

		$trash = ! empty( $actions['trash'] ) ? $actions['trash'] : false;

		if ( $trash ) {
			unset( $actions['trash'] );
			$actions['trash'] = $trash;
		}

		return $actions;
	}

	private function get_converted_url( $id ): string {
		$admin_url = esc_url_raw( admin_url() );

		$convert_url = add_query_arg(
			array(
				'action' => 'migrate_' . $this->get_provider(),
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