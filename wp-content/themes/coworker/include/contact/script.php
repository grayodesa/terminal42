<?php

$map = array();

$map['lat'] = get_post_meta(get_the_ID(), 'semi_page_contact_latitude', true);
$map['lon'] = get_post_meta(get_the_ID(), 'semi_page_contact_longitude', true);
$map['add'] = get_post_meta(get_the_ID(), 'semi_page_contact_address', true);
$map['html'] = str_replace( "'", '&#039;', get_post_meta(get_the_ID(), 'semi_page_contact_html', true) );
$map['zoom'] = get_post_meta(get_the_ID(), 'semi_page_contact_zoom', true);
$map['type'] = get_post_meta(get_the_ID(), 'semi_page_contact_maptype', true);
$map['scroll'] = get_post_meta(get_the_ID(), 'semi_page_contact_scrollwheel', true);
$map['panc'] = get_post_meta(get_the_ID(), 'semi_page_contact_pancontrol', true);
$map['zoomc'] = get_post_meta(get_the_ID(), 'semi_page_contact_zoomcontrol', true);
$map['mtypec'] = get_post_meta(get_the_ID(), 'semi_page_contact_maptypecontrol', true);
$map['scalec'] = get_post_meta(get_the_ID(), 'semi_page_contact_scalecontrol', true);
$map['svc'] = get_post_meta(get_the_ID(), 'semi_page_contact_streetviewcontrol', true);
$map['ovc'] = get_post_meta(get_the_ID(), 'semi_page_contact_overviewmapcontrol', true);

?>

<script type="text/javascript">
            
                jQuery('#google-map').gMap({
                    
                     <?php if( $map['add'] == '' ): ?>
                     latitude: <?php echo $map['lat']; ?>,
                     longitude: <?php echo $map['lon']; ?>,
                    <?php elseif( $map['add'] != '' ): ?>
                     address: '<?php echo $map['add']; ?>',
                    <?php endif; ?>
                     maptype: '<?php echo $map['type']; ?>',
                     zoom: <?php echo $map['zoom']; ?>,
                     scrollwheel: <?php echo ( $map['scroll'] == 1 ) ? 'true' : 'false'; ?>,
                     markers:[
                		{
      		                <?php if( $map['add'] == '' ): ?>
                             latitude: <?php echo $map['lat']; ?>,
                             longitude: <?php echo $map['lon']; ?>,
                            <?php elseif( $map['add'] != '' ): ?>
                             address: '<?php echo $map['add']; ?>',
                            <?php endif; ?>
                             html: '<?php echo $map['html']; ?>'
                		}
                     ],
                     doubleclickzoom: false,
                     controls: {
                         panControl: <?php echo ( $map['panc'] == 1 ) ? 'true' : 'false'; ?>,
                         zoomControl: <?php echo ( $map['zoomc'] == 1 ) ? 'true' : 'false'; ?>,
                         mapTypeControl: <?php echo ( $map['mtypec'] == 1 ) ? 'true' : 'false'; ?>,
                         scaleControl: <?php echo ( $map['scalec'] == 1 ) ? 'true' : 'false'; ?>,
                         streetViewControl: <?php echo ( $map['svc'] == 1 ) ? 'true' : 'false'; ?>,
                         overviewMapControl: <?php echo ( $map['ovc'] == 1 ) ? 'true' : 'false'; ?>
                     }
                
                });
            
            </script>