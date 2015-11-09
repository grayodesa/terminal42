<?php if( semi_option( 'colorscheme' ) != '' AND checkhexcolor( semi_option( 'colorscheme' ) ) ): ?>

a,
h1 span,
h2 span,
h3 span,
h4 span,
h5 span,
h6 span,
#top-menu li a:hover,
#lp-contacts li span,
#portfolio-filter li a:hover,
#portfolio-filter li.activeFilter a,
.portfolio-item:hover h3 a,
.entry_date div.post-icon,
.entry_meta li a:hover,
.ipost .ipost-title a:hover,
.comment-content .comment-author a:hover,
.promo h3 > span,
.error-404,
.tab_widget ul.tabs li.active a,
.product-feature3:hover span,
.team-skills li span,
.best-price .pricing-title h4,
.best-price .pricing-price,
.mini-checkout-wrap span.mini-checkout-price,
.mini-cart-item-desc a:hover,
.order_details li strong,
.product-title h3 a:hover,
.product-price,
.product-single .product-price,
.widget_shopping_cart p.total span.amount,
.price_slider_amount span { color: <?php echo semi_option( 'colorscheme' ); ?>; }

.border-button:hover,
.border-button.inverse { color: <?php echo semi_option( 'colorscheme' ); ?> !important; }

.pricing-style2 .pricing-price { color: #FFF !important; }

#top-menu li.top-menu-em a,
#primary-menu > ul > li:hover,
#primary-menu ul li.current,
#primary-menu > div > ul > li:hover,
#primary-menu div ul li.current,
#primary-menu > ul > li.current-menu-ancestor,
#primary-menu > ul > li.current-menu-parent,
#primary-menu > ul > li.current-menu-item,
#primary-menu > div > ul > li.current-menu-ancestor,
#primary-menu > div > ul > li.current-menu-parent,
#primary-menu > div > ul > li.current-menu-item,
.sticky-menu-wrap > ul > li:hover,
.sticky-menu-wrap ul li.current,
.sticky-menu-wrap > ul > li.current-menu-ancestor,
.sticky-menu-wrap > ul > li.current-menu-parent,
.sticky-menu-wrap > ul > li.current-menu-item,
#primary-menu ul ul li,
.sticky-menu-wrap ul ul li,
.lp-subscribe input[type="submit"],
.portfolio-overlay,
#portfolio-navigation a:hover,
.entry_date div.month,
.entry_date div.day,
.sidenav > .active > a,
.sidenav > .active > a:hover,
.promo-action a:hover,
.error-404-meta input[type="submit"],
.gallery-item img:hover,
.product-feature img,
.product-feature > span,
.team-image span,
.icon-rounded:hover,
.icon-circled:hover,
.simple-button.inverse,
.simple-button:hover,
.pricing-style2 .best-price .pricing-price,
#twitter-panel,
#gotoTop:hover,
a.twitter-follow-me:hover,
#footer.footer-dark a.twitter-follow-me:hover,
.sposts-list .spost-image:hover,
#footer.footer-dark .sposts-list .spost-image:hover,
.tagcloud a:hover,
#footer.footer-dark .tagcloud a:hover,
.widget-scroll-prev:hover,
.widget-scroll-next:hover,
#footer.footer-dark .widget-scroll-prev:hover,
#footer.footer-dark .widget-scroll-next:hover,
.quantity .plus:hover,
.quantity .minus:hover,
#fshop-cart-trigger #fshop-cart-qty,
.product-sale,
.product-overlay a:hover,
.ui-slider .ui-slider-range,
.widget_layered_nav li.chosen small { background-color: <?php echo semi_option( 'colorscheme' ); ?>; }

.ei-title h2 span,
.ei-title h3 span,
.ei-slider-thumbs li.ei-slider-element,
.flex-prev:hover,
.flex-next:hover,
.rs-prev:hover,
.rs-next:hover,
.nivo-prevNav:hover,
.nivo-nextNav:hover,
.camera_prev:hover,
.camera_next:hover,
.camera_commands:hover,
.tp-leftarrow.large:hover,
.tp-rightarrow.large:hover,
.ls-noskin .ls-nav-prev:hover,
.ls-noskin .ls-nav-next:hover { background-color: <?php echo semi_option( 'colorscheme' ); ?> !important; }

.ei-title h3 span { background-color: rgba(11,11,11,0.8) !important; }

#top-menu li a:hover,
.comment-content .comment-author a:hover,
.our-clients li:hover,
.mini-cart-item-image a:hover,
.cart-product-thumbnail img:hover,
.product_list_widget li img:hover { border-color: <?php echo semi_option( 'colorscheme' ); ?>; }


.border-button:hover,
.border-button.inverse { border-color: <?php echo semi_option( 'colorscheme' ); ?> !important; }


#header.header2,
.flex-control-thumbs li img.flex-active,
.rs-thumb-wrap a.active,
.tab_widget ul.tabs li.active,
#footer,
#copyrights { border-top-color: <?php echo semi_option( 'colorscheme' ); ?>; }


span.page-divider span,
#portfolio-filter li.activeFilter,
.portfolio-item:hover .portfolio-title,
#footer.footer-dark .portfolio-item:hover .portfolio-title { border-bottom-color: <?php echo semi_option( 'colorscheme' ); ?>; }


.slide-caption,
.rs-caption,
.nivo-caption,
.promo,
.side-tabs ul.tabs li.active { border-left-color: <?php echo semi_option( 'colorscheme' ); ?>; }

.ei-title h3 span { border-left-color: <?php echo semi_option( 'colorscheme' ); ?> !important; }

::selection,
::-moz-selection,
::-webkit-selection { background-color: <?php echo semi_option( 'colorscheme' ); ?>; }

<?php endif; ?>