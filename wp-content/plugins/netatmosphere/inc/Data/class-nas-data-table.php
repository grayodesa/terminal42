<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); 

class NAS_Data_Table {
    
    protected $rows = array();
    
    public function __construct() {
        $this->rows = array();
    }
    
    public function addRow( $row, $calcMissing = false) {
        if ( true === $calcMissing )
            NAS_Tool_Weather::calcMissingValuesInRow( $row );
        
        $this->rows[] = $row;
    }
    
    public function mergeColumns() {
        $columns = array();
        
        foreach ( $this->rows as $row ) {
            
            $new = $row->getColumnNames();
            // make assoc array with combine, otherwise the merge would only use the int index
            $new = array_combine ( $new, $new );

            $columns = array_merge ( $columns, $new );
        }
                
        foreach ( $this->rows as $row ) {
            $row->createColumnsIfNotExists( $columns );
        }
    }
    
    public function htmlTable($cssClass = null, $cssId = null) {
        $html = "<table" . (!empty($cssClass) ? " class='$cssClass'" : "") . (!empty($cssId) ? " id='$cssId'" : "") . ">";
        
        $html .= $this->htmlHeadline();
        $html .= $this->htmlRows();
        
        return $html . "</table>";
    }
    public function htmlHeadline() {
        if( isset( $this->rows ) && count( $this->rows ) > 0 ) {
            return $this->rows[0]->htmlHeadline();
        }
        return '';
    }
    public function htmlRows() {
        $html = "";

        foreach($this->rows as $key => $e) {
            $html .= $e->htmlRow();
        }
        return $html;
    }
}

?>