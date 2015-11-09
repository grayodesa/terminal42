<?php if( semi_option( 'bodyfont' ) != 'none' ): ?>

body,
input,
select,
textarea { font-family: '<?php echo semi_option( 'bodyfont' ) ?>'; }

<?php endif; ?>

<?php if( semi_option( 'primaryfont' ) != 'none' AND semi_option( 'primaryfont' ) != 'Open Sans' ): ?>

h1,
h2,
h3,
h4,
h5,
h6,
#logo,
#primary-menu li a,
#primary-menu li a span,
.slide-caption2,
#lp-contacts li,
#portfolio-filter li a,
.entry_meta li a,
ul.ipost-meta,
.pagination ul,
ul.pager,
.comment-content .comment-author,
.sitemap,
.promo-desc > span,
.promo-action a,
.error404,
.tab_widget ul.tabs li a,
.toggle .togglet,
.toggle .toggleta,
.team-image span,
.team-skills li,
.skills li span,
.simple-button,
.pricing-price .price-tenure,
.pricing-features ul,
.skills,
.acctitle,
.acctitlec,
.testimonial-item .testi-author,
.widget_nav_menu li,
.widget_links li,
.widget_meta li,
.widget_archive li,
.widget_recent_comments li,
.widget_recent_entries li,
.widget_categories li,
.widget_pages li { font-family: "<?php echo semi_option( 'primaryfont' ) ?>"; }

<?php endif; ?>

<?php if( semi_option( 'secondaryfont' ) != 'none' AND semi_option( 'secondaryfont' ) != 'Droid Serif' ): ?>

blockquote,
.entry_content ul,
.entry_content ol,
.slide-caption,
.rs-caption,
.nivo-caption,
.countdown_amount,
.wp-caption,
.sitemap ul,
.well.callout p,
.quote,
.pricing-title h4 span,
.pricing-inner .pricing-price,
.testimonial-item .testi-content,
.testimonial-item .testi-author span { font-family: "<?php echo semi_option( 'secondaryfont' ) ?>"; }

<?php endif; ?>