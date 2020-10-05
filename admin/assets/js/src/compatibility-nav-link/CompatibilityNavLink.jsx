import React from 'react';
import './CompatibilityNavLink.scss';
import {Link} from 'react-router-dom';
import queryString from 'query-string'

const CompatibilityNavLink = () => {

    const {__} = wp.i18n;    
    const page = queryString.parse(window.location.search); 
    let current = 'active';
    
    if(typeof(page.tab)  != 'undefined' ) { 
      current = page.tab;
    }  
    
      return(                                                         
          <div className="tabs saswp-compatibility-tab">                
          <Link  to={'admin.php?page=saswp&path=settings_compatibility&tab=active'} className={current == 'active' ? 'tab-item active' : 'tab-item'}>{__('Active Plugins', 'schema-and-structured-data-for-wp')}</Link>                                                
          <div className="saswp-divider-vertical"></div>
          <Link  to={'admin.php?page=saswp&path=settings_compatibility&tab=all'} className={current == 'all' ? 'tab-item active' : 'tab-item'}>{__('All Plugins', 'schema-and-structured-data-for-wp')}</Link>                                                
          </div>                                                                                             
      );
}
export default CompatibilityNavLink;