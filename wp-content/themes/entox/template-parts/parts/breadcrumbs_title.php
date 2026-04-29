<?php

 if ( is_home () && is_front_page () ) {
        
    esc_html_e('Home','entox');

} elseif ( is_front_page() ) {
    
    esc_html_e('Home','entox');

}elseif ( is_home () ) {

    esc_html_e('Blog','entox');

} elseif ( is_search () ) {

    esc_html_e('Search','entox');

} else if(is_category () ){

    echo single_cat_title('');

}else if (is_tag ()){

    esc_html_e('Tags','entox');

}else if( is_tax () || is_archive() ){

    echo get_the_archive_title();

}else if( is_singular() ){

    echo get_the_title();

}
