$white:    #fff !default;
$gray-100: #f8f9fa !default;
$gray-200: #f7f7f9 !default;
$gray-300: #eceeef !default;
$gray-400: #ced4da !default;
$gray-500: #aaa !default;
$gray-600: #888 !default;
$gray-700: #5a5a5a !default;
$gray-800: #343a40 !default;
$gray-900: #212529 !default;
$black:    #000 !default;

$primary:       {{layout_design_theme_primary_button}};
$secondary:     {{layout_design_theme_secondary_button}};
$success:       {{layout_design_theme_success_button}};
$info:          {{layout_design_theme_info_button}};
$warning:       {{layout_design_theme_warning_button}};
$danger:        {{layout_design_theme_danger_button}};
$light:         {{layout_design_theme_light_button}};
$dark:          {{layout_design_theme_dark_button}};

{% if (layout_design_theme_header_font != 'System UI') %}
$headings-font-family: {{layout_design_theme_header_font}};
$font-family-base: {{layout_design_theme_base_font}};
{% endif %}

$headings-color:    lighten({{layout_design_theme_text_color}}, 5%);
$body-bg: {{layout_design_theme_background_color}};
$body-color: {{layout_design_theme_text_color}};
$link-color: {{layout_design_theme_link_color}};

$card-color: $body-color;
$card-bg: lighten($body-bg, 5%);
$modal-content-bg: lighten($body-bg, 10%);
$jumbotron-bg: lighten($body-bg, 20%);

$enable-responsive-font-sizes: true;

$footer-bg: {{layout_design_theme_footer_color}};
$footer-text: ({{layout_design_theme_footertext_color}});

$border-radius:               {{layout_design_theme_border_radius}}rem;
$border-radius-lg:            {{layout_design_theme_border_radius}}rem;
$border-radius-sm:            {{layout_design_theme_border_radius}}rem;

$headings-color:              darken($body-color, 10%);

$yiq-contrasted-threshold: {{layout_design_theme_yi_contrast}};

$enable-caret: true;
$enable-rounded: {% if (layout_design_theme_enable_rounded == 1) %}true{% else %}false{% endif %};
$enable-shadows: true;
$enable-gradients: {% if (layout_design_theme_enable_gradients == 1) %}true{% else %}false{% endif %};
$enable-transitions: true;
$enable-prefers-reduced-motion-media-query: true;
$enable-grid-classes: true true;
$enable-print-styles: true true;
$enable-validation-icons: true;

@import "functions";
@import "variables";
@import "mixins";
@import "root";
@import "reboot";
@import "type";
@import "images";
@import "code";
@import "grid";
@import "tables";
@import "forms";
@import "buttons";
@import "transitions";
@import "dropdown";
@import "button-group";
@import "input-group";
@import "custom-forms";
@import "nav";
@import "navbar";
@import "card";
@import "breadcrumb";
@import "pagination";
@import "badge";
@import "jumbotron";
@import "alert";
@import "progress";
@import "media";
@import "list-group";
@import "close";
@import "toasts";
@import "modal";
@import "tooltip";
@import "popover";
@import "carousel";
@import "spinners";
@import "utilities";
@import "print";

div.logo a{
color: $light;
}

{% if (layout_design_theme_enable_gradients == 1) %}
.card {
    -webkit-box-shadow: 0 1px 2.94px 0.06px rgba(4,26,55,0.16);
    box-shadow: 0 1px 2.94px 0.06px rgba(4,26,55,0.16);
    -webkit-transition: all 0.3s ease-in-out;
    transition: all 0.3s ease-in-out;
}
{% endif %}

/* div where the default menu bar is */
.top-menu-box {
    background-color: {{layout_design_theme_topnav_color}};
}

/* site menu with logo and dropdowns */
div#top-menu-bar ul li a.nav-link  {
    color: {{layout_design_theme_topnavtext_color}};
}

/* top nav with login and register */
.bg-top-nav {
    background-color: rgba( darken($primary, 10%), .8 );
}

.bg-top-nav a.nav-link i, a.navbar-brand, .bg-top-nav ul li a.nav-link {
    color: color-yiq($primary) !important;
}

div#top-menu-bar ul li a.nav-link:hover {
    color: $light;
}

/* off-canvas menu */
.sidebar h3 {
background-color: $light;
border: 1px dotted darken($light, 30%);
}

.sidebar-offcanvas {
background-color: lighten($body-bg, 10%);
}

.sidebar-offcanvas ul li {
    background: transparent;
}

.sidebar-offcanvas .dropdown-item{
    color: $body-color;
}

header .navbar-white {
margin: 0;
padding: 1rem 0;
border: 0;
border-radius: 0;
-webkit-box-shadow: 0 1px 10px rgba(0, 0, 0, 0.1);
-moz-box-shadow: 0 1px 10px rgba(0, 0, 0, .1);
box-shadow: 0 1px 10px rgba(0, 0, 0, 0.1);

}

.navbar-white .nav-link {
color: $dark;
padding: 13px 19px !important;
margin-right: 4px;
}

.navbar-white .nav-link:hover {
color: $white;
background-color: lighten($dark, 30%);
@if $enable-rounded {
    border-radius: 3px;
    }
}
.navbar-white .dropdown-menu {
min-width: 180px;
background: $light;
margin-top: 17px;
border: 0;
background-color: $dark;
@if $enable-rounded {
    border-radius: 3px;
    }
}

.navbar-white .dropdown-menu a {
color: $light;
}

.navbar-white .dropdown-menu:after, .arrow_box:before {
bottom: 100%;
left: 16%;
border: solid transparent;
content: " ";
height: 0;
width: 0;
position: absolute;
pointer-events: none;
}

.navbar-white .dropdown-menu:after {
border-color: rgba(255, 255, 255, 0);
border-bottom-color: $dark;
border-width: .5rem;
margin-left: -.5rem;
}

.navbar-white .dropdown-form h4 {
margin: 0;
padding: 15px 15px 5px 15px;
color: $light;
}

.dropdown-item:hover, .dropdown-item:focus {
color: $dark !important;
text-decoration: none;
background-color: $light;
}

/* default page headline div */
.page-header {
background: {{layout_design_theme_pageheader_color }};
{% if (layout_design_theme_enable_gradients == 1) %}
background: -moz-linear-gradient(top, darken( {{layout_design_theme_pageheader_color }}, 2% ) 0%, darken( {{layout_design_theme_pageheader_color }}, 5% ) 100%);
background: -webkit-linear-gradient(top, darken( {{layout_design_theme_pageheader_color }}, 2% ) 0%, darken( {{layout_design_theme_pageheader_color }}, 5% ) 100%);
background: linear-gradient(to bottom, darken( {{layout_design_theme_pageheader_color }}, 2% ) 0%, darken( {{layout_design_theme_pageheader_color }}, 5% ) 100%);
{% endif %}
color: {{layout_design_theme_pageheadertext_color }};
}

.page-header h2.headline {
color: {{layout_design_theme_pageheadertext_color }};
}

/* for the default home page tag line */
div.tag-line.default {
background: $dark;
{% if (layout_design_theme_enable_gradients == 1) %}
background: -moz-linear-gradient(top, lighten( $dark, 20% ) 0%, $dark 100%);
background: -webkit-linear-gradient(top, lighten( $dark, 20% ) 0%, $dark 100%);
background: linear-gradient(to bottom, lighten( $dark, 20% ) 0%, $dark 100%);
{% endif %}
color: $light;
}

div.tag-line.default a {
color: $light;
}

div.tag-line.default div.row>div {
border-right: 1px solid $dark;
}

/* Title headline for default home page template */
div.section .title h2:before {
background: -moz-linear-gradient(left,  lighten( $dark, 20% ) 0%, $dark 100%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right top, color-stop(0%,lighten( $dark, 20% )), color-stop(100%,$dark)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(left,  lighten( $dark, 20% ) 0%,$dark 100%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(left,  lighten( $dark, 20% ) 0%,$dark 100%); /* Opera 11.10+ */
background: -ms-linear-gradient(left,  lighten( $dark, 20% ) 0%,$dark 100%); /* IE10+ */
background: linear-gradient(to right,  lighten( $dark, 20% ) 0%,$dark 100%); /* W3C */
left: 0;
}

div.section .title h2:after {
background: -moz-linear-gradient(left,  $dark 0%, lighten( $dark, 20% ) 100%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right top, color-stop(0%,$dark), color-stop(100%,lighten( $dark, 20% ))); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(left,  $dark 0%,lighten( $dark, 20% ) 100%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(left,  $dark 0%,lighten( $dark, 20% ) 100%); /* Opera 11.10+ */
background: -ms-linear-gradient(left,  $dark 0%,lighten( $dark, 20% ) 100%); /* IE10+ */
background: linear-gradient(to right,  $dark 0%,lighten( $dark, 20% ) 100%); /* W3C */
right: 0;
left: auto;
}

.list-group-item, list-group-item a {
background-color: darken($body-bg, 3%);
color: $body-color;
}

div.section .title h2 span {
    color: $dark;
}

/* Blog */
.box-meta, .box-meta a {
color: lighten($body-color, 20%);
}

.comment-body {
background-color: $light;
}

.comment-body:after {
border-color: $light transparent;
}

/* Footer stuff */
.footer {
background-color:  darken($footer-bg, 15%) ;
}

.footer-menu{
border-top: 5px solid lighten( $footer-bg, 10% );
background-color: $footer-bg;
{% if (layout_design_theme_enable_gradients == 1) %}
background: -moz-linear-gradient(left,  $footer-bg 0%, darken( $footer-bg, 20% ) 100%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right top, color-stop(0%,$footer-bg), color-stop(100%,darken( $footer-bg, 20% ))); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(left,  $footer-bg 0%,darken( $footer-bg, 20% ) 100%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(left,  $footer-bg 0%,darken( $footer-bg, 20% ) 100%); /* Opera 11.10+ */
background: -ms-linear-gradient(left,  $footer-bg 0%,darken( $footer-bg, 20% ) 100%); /* IE10+ */
background: linear-gradient(to right,  $footer-bg 0%,darken( $footer-bg, 20% ) 100%); /* W3C */
{% endif %}
color: $footer_text;
}

.footer a {
color: $footer-text;
}

.footer-menu h4 {
border-bottom: 1px solid lighten( $footer-bg, 20% );
color: lighten( $footer-text, 10% );
}

.footer-menu li {
border-bottom: 1px dotted $footer-text;
}

.cpp {
background-color:darken($footer-bg, 20%);
color: $footer-text;
}

.cpp a {
color: $footer-text;
}

.cpp a:hover {
color: darken($footer-text, 20%);
}

.cpp ul li {
border-right: 1px solid $footer-text;
}

/* error message stuff */
textarea.error, input.error {
border: 1px solid $danger
}

label.error {
color: $danger;
}

.star-rating {
    color: $warning;
}

textarea.error, input.error {
background-color: lighten($danger, 50%) !important;
border: 1px solid $danger;
-moz-box-shadow: 1px 1px 2px 2px darken($light, 30%);
-webkit-box-shadow: 1px 1px 2px 2px darken($light, 30%);
box-shadow: 1px 1px 2px 2px darken($light, 30%);
}

.required.error {
border: 1px solid $danger;
}

/* affiliate downline */
.level-div {
border-top: 1px solid darken($light, 30%);
}
.downline-box {
border: 1px solid darken($light, 30%);
}

.thumb {
border: 1px solid darken($light, 30%);
}

/* breadcrumb */
.breadcrumb {
background-color: lighten($body-bg, 5%);
}

ol.breadcrumb li, ol.breadcrumb li a {
color: lighten($body-color, 30%);
}

ol.breadcrumb li a {
color: lighten($body-color, 30%);
}

ol.breadcrumb li a:hover {
color: lighten($dark, 10%);
}

.page-not-found {
color: $light;
}

/* extra buttons */

.btn-facebook {
background: #45619D;
border-color: #4D6CAD;
color: #fff
}

.btn-facebook:hover {
background: #395289;
border-color: #4D6CAD;
color: #fff
}

.btn-twitter {
background: #00ACEE;
border-color: #00B7FC;
color: #fff
}

.btn-twitter:hover {
background: #03A0DE;
border-color: #00B7FC;
color: #fff
}

.btn-pinterest {
background-color: #CC2127;
border-color: #CC2127;
color: #fff
}

.btn-pinterest:hover, .btn-pinterest:focus, .btn-pinterest:active, .btn-pinterest.active, .open .dropdown-toggle.btn-pinterest {
background-color: #B70F12;
border-color: #B70F12;
color: #fff
}

.btn-pinterest:active, .btn-pinterest.active, .open .dropdown-toggle.btn-pinterest {
background-image: none
}

.btn-pinterest.disabled, .btn-pinterest[disabled], fieldset[disabled] .btn-pinterest, .btn-pinterest.disabled:hover, .btn-pinterest[disabled]:hover, fieldset[disabled] .btn-pinterest:hover, .btn-pinterest.disabled:focus, .btn-pinterest[disabled]:focus, fieldset[disabled] .btn-pinterest:focus, .btn-pinterest.disabled:active, .btn-pinterest[disabled]:active, fieldset[disabled] .btn-pinterest:active, .btn-pinterest.disabled.active, .btn-pinterest[disabled].active, fieldset[disabled] .btn-pinterest.active {
background-color: #E53B3E;
border-color: #E53B3E;
color: #fff
}


.btn-youtube {
background-color: #D92623;
border-color: #D92623;
color: #fff
}

.btn-instagram {
background: #4E3D35;
border-color: #392C24;
color: #fff
}

.btn-instagram:hover {
background: #483931;
border-color: #392C24;
color: #fff
}


.btn-linkedin {
background-color: #0085AE;
border-color: #0085AE;
color: #fff
}

.btn-tumblr {
background-color: #001935;
border-color: #001935;
color: #fff
}

.btn-linkedin:hover, .btn-linkedin:focus, .btn-linkedin:active, .btn-linkedin.active, .open .dropdown-toggle.btn-linkedin {
background-color: #036C8E;
border-color: #036C8E;
color: #fff
}

.btn-linkedin:active, .btn-linkedin.active, .open .dropdown-toggle.btn-linkedin {
background-image: none
}

.btn-icon {
/*background-color: #222;
border-color: #666*/
margin-bottom: 8px;
}

.btn-google-plus {
background-color: #D24333;
border-color: #D24333;
color: #fff
}

.btn-google-plus:hover, .btn-google-plus:focus, .btn-google-plus:active, .btn-google-plus.active, .open .dropdown-toggle.btn-google-plus {
background-color: #BC2C1F;
border-color: #BC2C1F;
color: #fff
}

.btn-google-plus:active, .btn-google-plus.active, .open .dropdown-toggle.btn-google-plus {
background-image: none
}

.btn-google-plus.disabled, .btn-google-plus[disabled], fieldset[disabled] .btn-google-plus, .btn-google-plus.disabled:hover, .btn-google-plus[disabled]:hover, fieldset[disabled] .btn-google-plus:hover, .btn-google-plus.disabled:focus, .btn-google-plus[disabled]:focus, fieldset[disabled] .btn-google-plus:focus, .btn-google-plus.disabled:active, .btn-google-plus[disabled]:active, fieldset[disabled] .btn-google-plus:active, .btn-google-plus.disabled.active, .btn-google-plus[disabled].active, fieldset[disabled] .btn-google-plus.active {
background-color: #F0675A;
border-color: #F0675A;
color: #fff
}

.btn-google-plus .badge {
color: #BC2C1F
}

.badge>span.total {
background: darken($light, 20%);
border: 1px solid darken($light, 20%);
color: lighten($dark, 20%);
}

.badge>.name {
border: 1px solid darken($light, 20%);
background: darken($light, 10%);
}

.shadow-text {
text-shadow: 1px 1px lighten($dark, 30%);
}

.shopping-cart {
background: $white;
color: $gray-800;
}

.shopping-cart .shopping-cart-header {
border-bottom: 1px solid $gray-300;
}

.shopping-cart .shopping-cart-items .item-price {
color: darken($primary, 30%);
}
.shopping-cart .shopping-cart-items .item-quantity {
color: $gray-600;
}

.shopping-cart:after {
border-bottom-color: lighten($light, 10%);
}

h1.slide-headline, .slide-item .list li > span {
color: #fff;
}

div.card.cursor:hover {
background-color: darken($light, 10%);
}

.show-code pre{
background-color: $light;
}

.ltr {
color: $light;
}
.ltr-avatar {
background: $secondary;
}

.calendar {
border: 1px solid darken($body-bg, 5%);
}

.caldaycells, .calendar td {
    background-color: lighten($body-bg, 10%);
    border: 1px dotted darken($body-bg, 5%);
}

table tr td.day {
    color: {{layout_design_theme_background_color}};
}

.table-condensed {
color: {{layout_design_theme_background_color}};
}

.blockquote {
background-color: darken($body-bg, 5%);
padding: 1em;
font-size: 1rem;
}