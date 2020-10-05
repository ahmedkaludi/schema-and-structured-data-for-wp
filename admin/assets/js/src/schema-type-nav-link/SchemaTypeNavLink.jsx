import React from 'react';
import './SchemaTypeNavLink.scss';
import {Link} from 'react-router-dom';
import queryString from 'query-string'

const SchemaTypeNavLink = () => {

    const {__}  = wp.i18n;    
    const page  = queryString.parse(window.location.search); 
    let current = 'popular_schema';
    
    if(typeof(page.tab)  != 'undefined' ) { 
      current = page.tab;
    }                            

    return(                      
       <>            
      <div className="tabs saswp-schema-type-tab">                 
      <Link  to={'admin.php?page=saswp&path=schema_add&tab=popular_schema'} className={current == 'popular_schema' ? 'tab-item active' : 'tab-item'}>{__('Popular Schema', 'schema-and-structured-data-for-wp')}</Link>                                                                
      <Link  to={'admin.php?page=saswp&path=schema_add&tab=all_schema'} className={current == 'all_schema' ? 'tab-item active' : 'tab-item'}>{__('All Schema', 'schema-and-structured-data-for-wp')}</Link>                                                                
      </div>
      </>                                                                                                   
    );

}
export default SchemaTypeNavLink;