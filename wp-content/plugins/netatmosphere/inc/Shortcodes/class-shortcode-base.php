<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); 

abstract class NAS_Shortcode_Base {
	protected $pagename = '';
	protected $url = '';
	
	public function __constructor() {
		$this->pagename = get_query_var('pagename');		
		$this->url = preg_replace("/" . preg_quote($this->pagename).".*/i", $this->pagename, esc_url( $_SERVER['REQUEST_URI'] ));
	}
	
	abstract public function init($attributes);
	abstract public function render();
    abstract public function renderExample();
    abstract public function renderOptions();
    
    protected function renderNotImplemented($class = null, $func = null) {
        $html = __('Shortcode (or its parameter) not implemented!', 'netatmosphere');
        if( null !== $class )
            $html .= "<br/>\n( " . $class . "::" . $func . " )";
        return $html;
    }
    protected function array2htmlList($attr) {
        $html = "";
        
        foreach($attr as $key => $value) {
            
            $html .= "<li><strong>" . $key . "</strong>: ";
            
            if( is_array( $value ) ) {
                $html .= implode( "|", $value );
            } else {
                $html .= $value;
            }
            
            $html .= "</li>";
        }
        
        return "<ul>" . $html . "</ul>";
    }
	/*protected function getParameter($paramName, $attr, $default = null) {
		
		//HSL_Plugin::debugBar('HSL_Shortcode_Base-getParameter-attr', $attr, __FILE__, __LINE__);
		//HSL_Plugin::debugBar('HSL_Shortcode_Base-getParameter-default', $default, __FILE__, __LINE__);
		
		$res = $default;
		// 1st: by shortcode init
		if(isset($attr) && isset($attr[$paramName]))
			$res = $attr[$paramName];
		// 2nd: by _GET
		if(isset($_GET) && isset($_GET[$paramName]))
			$res = $_GET[$paramName];
		// 3rd: by query var
		$tmp = get_query_var($paramName, $res);
		if(!empty($tmp))
			$res = $tmp;

		return $res;
	}*/
}

?>