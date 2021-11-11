<?php


namespace Jet_Form_Builder_Converter;



class Post_Row_Actions_Jet_Engine extends Post_Row_Actions_Base {

	public function get_provider(): string {
		return 'jet_engine';
	}

	public function get_post_type(): string {
		return 'jet-engine-booking';
	}


}