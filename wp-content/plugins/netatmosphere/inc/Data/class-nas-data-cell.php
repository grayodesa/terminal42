<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); 

class NAS_Data_Cell {
    
    public $time_stamp = null;
    public $id_module  = null;
    public $location = null;
    public $category = null;
    public $unit = null;
    public $value = null;
    
    public function __construct() {
    }
    public function loadFromResult($row) {
        
        $this->time_stamp = ( !empty($row['time_stamp'] ) ) ? $row['time_stamp'] : null;
        $this->id_module  = ( !empty($row['id_module'] ) ) ? $row['id_module'] : null;
        $this->location = ( !empty($row['meter_location'] ) ) ? $row['meter_location'] : null;
        $this->category = ( !empty($row['value_category'] ) ) ? $row['value_category'] : null;
        $this->value = ( !empty($row['value'] ) ) ? $row['value'] : null;
        $this->unit = ( !empty($row['value_unit'] ) ) ? $row['value_unit'] : null;
    }

    public static function getEmpty($category) {
        $e = new self();
        $e->category = $category;
        $e->value = '-';
        
        return $e;
    }
    
}

?>