<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); 

class NAS_Shortcode_Schedules extends NAS_Shortcode_Base {
	protected $format = '';
    
	public function __constructor() {
        parent::__construct();
	}
	
	public function init($attributes) {
		$this->format = '';
        if(isset($attributes)) {
            if(isset($attributes['format']))
            {
                $this->format = $attributes['format'];
            }
        }
	}
	public function render() {
        $html = "";
        switch($this->format) {
            case 'table':
                $html .= "<table><thead><tr><th>" . __("Devices", 'netatmosphere') . "</th><th>" . __("Data", 'netatmosphere') . "</th></tr></thead><tbody>";
                $html .= "<tr><td>" . $this->getNextDeviceRefresh() . "</td><td>" . $this->getNextDataMerge() . "</td></tr>";
                $html .= "</tbody></table>";
                break;
                
            default:
                $html .= "<p>" . sprintf(
                                __("Next schedule is at %s to refresh device list.", 'netatmosphere'), 
                                $this->getNextDeviceRefresh()) 
                            . "</p>";
                $html .= "<p>" . sprintf(
                                __("Next schedule is at %s to get and cache new measure sets.", 'netatmosphere'), 
                                $this->getNextDataMerge()) 
                            . "</p>";
                break;
        }
        
        return $html;
    }
    public function renderExample() {
        return "[" . NAS_Plugin::$shortcode_schedules . "]";
    }
    public function renderOptions() {
        $attr['format'] = array('table');
        
        return $this->array2htmlList($attr);
    }

    private function getNextDeviceRefresh() {
        return NAS_Cron::getNextDeviceRefresh();
    }
    private function getNextDataMerge() {
        return NAS_Cron::getNextDataMerge();
    }
}

?>