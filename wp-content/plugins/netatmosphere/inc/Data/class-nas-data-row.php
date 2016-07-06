<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); 

class NAS_Data_Row {
    
    protected $category = null;
    protected $categoryHead = null;
    protected $elements = array();
    //protected $keys = array();
    
    public function __construct($category, $categoryHead = '') {
        $this->category = $category;
        $this->categoryHead = $categoryHead;
        $this->elements = array();
    }
    
    public function addElement($element) {
        $key = $element->category;
        // store keys for later indexed access
        //$this->keys[] = $key;
        $this->elements[$key] = $element;
        
        ksort($this->elements);
    }
    
    public function getElementByColumn( $column ) {
        //NAS_Plugin::debugFile('start', __FILE__, __LINE__, __FUNCTION__);
        if ( empty( $column ) )
            return null;
        
        foreach ( $this->elements as $ele ) {
            
            if ( $column === $ele->category )
                return $ele;
        
        }
        
        return null;
    }
    
    public function getColumnNames() {
        return array_keys( $this->elements );
    }
    public function createColumnsIfNotExists($columns) {
        if ( is_array($columns) && count($columns) > 0 ) {
        
            $existingColumns = $this->getColumnNames();
                    
            foreach ( $columns as $col ) {
                if ( !in_array ( $col, $existingColumns ) ) {
                    
                    $emptyCell = NAS_Data_Cell::getEmpty($col);
                    
                    $this->addElement($emptyCell);
                }
            }
        }
    }
    
    public function htmlHeadline() {
        $html = "";
        
        $html .= "<th><big>{$this->categoryHead}</big></th>";
        foreach($this->elements as $key => $e)
            $html .= "<th>$key</th>";
        
        return "<tr>" . $html . "</tr>";
    }
    public function htmlRow() {
        $html = "<th>{$this->category}</th>";

        foreach($this->elements as $key => $e) {
            $html .= "<td title='" . $e->time_stamp . "'>" . $e->value . $e->unit . "</td>";
        }
        return "<tr>" . $html . "</tr>";
    }
}

?>