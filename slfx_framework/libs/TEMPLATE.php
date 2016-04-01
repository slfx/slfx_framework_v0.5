<?php

class TEMPLATE
{

	/**
	* fills a template with values
	* (static)
	*
	* @param $template
	*		Template to be used
	* @param $values
	*		Array of all the values
	*/
	static function fill($template, $values) {
		$template = TEMPLATE::place($template, $values);
		// remove non matched template-tags
		return TEMPLATE::removeTags($template);
	}


	static function place($template, $values) {
		if (sizeof($values) != 0) {
			// go through all the values
			for(reset($values); $key = key($values); next($values)) {
				$template = str_replace("<%$key%>",$values[$key],$template);
			}
		}
		return $template;
	}


	static function removeTags( $template ) {
		// remove non matched template-tags
		return preg_replace('/<%[a-z_A-Z0-9]+%>/','',$template);
	}


}


?>
