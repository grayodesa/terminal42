<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); 

class NAS_Shortcode_Devices extends NAS_Shortcode_Base {
	protected $mode = 'overview';
    protected $module = '';
    protected $device = '';
    protected $ignoreFields = null;
    
	public function __constructor() {
	}
	
	public function init($attributes) {
		$this->mode = 'overview'; $this->ignoreFields = null;
        if(isset($attributes)) {
            if(isset($attributes['mode'])) {
                $this->mode = strtolower($attributes['mode']);
            }
            if(isset($attributes['module'])) {
                $this->module = strtolower($attributes['module']);
            }
            if(isset($attributes['device'])) {
                $this->device = strtolower($attributes['device']);
            }
            if(isset($attributes['ignore'])) {
                $this->ignoreFields = array();
                $this->ignoreFields = explode(";", $attributes['ignore']);
            }
        }
	}
	public function render() {
        $html = "";
        switch($mode) {
            //case "hugo":
                //$this->renderOverview($ignoreFields);
                //break;
                
            default:
                $html .= $this->renderOverview();
                break;
        }
        return $html;
    }
    public function renderExample() {
        return "[" . NAS_Plugin::$shortcode_devices . "]";
    }
    public function renderOptions() {
        $attr['ignore'] = array('column1', 'column2', __('... (seperated by semicolon ";" )', 'netatmosphere') );
        
        return $this->array2htmlList($attr);
    }
    
    protected function renderOverview() {
        $devices = NAS_Devices_Adapter::getAll();
        $ignoreFieldList = $this->ignoreFields;

		if(isset($devices) && count($devices) > 0) {
			$keys = array_keys($devices[0]);
			unset($keys[array_search("id", $keys)]);
			if($ignoreFieldList !== null) {
				foreach($ignoreFieldList as $ignore) {
					unset($keys[array_search($ignore, $keys)]);
				}
			}
			$keys = array_values($keys);

            $html = "<h2>" . __('Devices Overview', 'netatmosphere') . "</h2>";
            $html .= "<div id='table-wrapper' width='100%'>
                        <table id='nas-devices' class='hover stripe display compact'>
                        <thead>
                            <tr>";
    
            foreach($keys as $key) {
                $html .= "<th>" . $key . "</th>";
            }
			
            $html .= "</tr></thead><tbody><tr>";
			
            foreach($devices as $key => $row) {
                foreach($keys as $key) {
                    $html .= "<td>" . $row[$key] . "</td>";
                }
                $html .= "</tr><tr>";
            }
			
            $html .= "</tr>
                    </tbody>
                </table>
            </div>";

        } else { 
            $html .= "<p>" . __("No devices in cache!", 'netatmosphere') . "</p>";
        } 
        
        return $html;
    }
}

?>