import React from 'react';
import './ReviewsNavLink.scss';
import {Link} from 'react-router-dom';
import queryString from 'query-string'

const ReviewsNavLink = () => {

    const {__} = wp.i18n;    
    const page = queryString.parse(window.location.search); 
    let current = 'reviews';
    
    if(typeof(page.path)  != 'undefined' ) { 
      current = page.path;
    }

    return(                                                         
      <div className="saswp-reviews-tabs tabs">
      <Link  to={'admin.php?page=saswp&path=reviews'} className={current == 'reviews' ? 'tab-item active' : 'tab-item'}>{__('Reviews', 'schema-and-structured-data-for-wp')}</Link>                                                
      <Link  to={'admin.php?page=saswp&path=reviews_collections'} className={current == 'reviews_collections' ? 'tab-item active' : 'tab-item'}>{__('Collections', 'schema-and-structured-data-for-wp')}</Link>                                
      <Link  to={'admin.php?page=saswp&path=reviews_fetch'} className={current == 'reviews_fetch' ? 'tab-item active' : 'tab-item'}>{__('Auto Fetch Reviews', 'schema-and-structured-data-for-wp')}</Link>                                
      <Link  to={'admin.php?page=saswp&path=reviews_accept'} className={current == 'reviews_accept' ? 'tab-item active' : 'tab-item'}>{__('Accept Reviews', 'schema-and-structured-data-for-wp')}</Link>                                      
      </div>                                                                                             
    );
}
export default ReviewsNavLink;