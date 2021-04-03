import React from 'react';
import './SettingsNavLink.scss';
import {Link} from 'react-router-dom';
import queryString from 'query-string'

const SettingsNavLink = () => {

    const {__} = wp.i18n;    
    const page = queryString.parse(window.location.search); 
    let current = 'settings';
    
    if(typeof(page.path)  != 'undefined' ) { 
      current = page.path;
    }

    return(                                                         
      <div className="tabs">
      <Link  to={'admin.php?page=saswp&path=settings'} className={current == 'settings' ? 'tab-item active' : 'tab-item'}>{__('Setup', 'schema-and-structured-data-for-wp')}</Link>                                                
      <Link  to={'admin.php?page=saswp&path=settings_general'} className={current == 'settings_general' ? 'tab-item active' : 'tab-item'}>{__('General', 'schema-and-structured-data-for-wp')}</Link>                                
      <Link  to={'admin.php?page=saswp&path=settings_compatibility'} className={current == 'settings_compatibility' ? 'tab-item active' : 'tab-item'}>{__('Compatibility', 'schema-and-structured-data-for-wp')}
      {(saswp_localize_data.active_plugins_count > 0) ? <span className="tab-item-right-el tab-link-round-circle">{saswp_localize_data.active_plugins_count}</span> : ''}
      </Link>                                
      <Link  to={'admin.php?page=saswp&path=settings_advanced'} className={current == 'settings_advanced' ? 'tab-item active' : 'tab-item'}>{__('Advanced', 'schema-and-structured-data-for-wp')}</Link>                                
      <Link  to={'admin.php?page=saswp&path=settings_tools'} className={current == 'settings_tools' ? 'tab-item active' : 'tab-item'}>{__('Tools', 'schema-and-structured-data-for-wp')}</Link>                                
      <Link  to={'admin.php?page=saswp&path=settings_premium'} className={current == 'settings_premium' ? 'tab-item active' : 'tab-item'}>{__('Premium Features', 'schema-and-structured-data-for-wp')}</Link>                                
      <Link  to={'admin.php?page=saswp&path=settings_service'} className={current == 'settings_service' ? 'tab-item active' : 'tab-item'}>{__('Services', 'schema-and-structured-data-for-wp')}</Link>                                
      <Link  to={'admin.php?page=saswp&path=settings_support'} className={current == 'settings_support' ? 'tab-item active' : 'tab-item'}>{__('Support', 'schema-and-structured-data-for-wp')}</Link>                                                
      </div>                                                                                             
    );
}
export default SettingsNavLink;