import React from 'react';
import './HomeNavLink.scss';
import queryString from 'query-string'
import {useHistory } from 'react-router-dom';
import {Link} from 'react-router-dom';

const HomeNavLink = () => {
         
    const {__}  = wp.i18n;        
    const page = queryString.parse(window.location.search); 
    let current = 'schema';
    
    if(typeof(page.path)  != 'undefined' ) { 
      current = page.path;
    }
            
    const MoveToOldInterface = (e) => {
         e.preventDefault();            
          const body_json       = {};                        
          body_json.interface   = 'old';                

          let url = saswp_localize_data.rest_url + "saswp-route/change-interface";        

          fetch(url, {
          method: "post",
          headers: {                    
              'Accept': 'application/json',
              'Content-Type': 'application/json',
              'X-WP-Nonce': saswp_localize_data.nonce,
          },
          body: JSON.stringify(body_json)
          })
          .then(res => res.json())
          .then(
          (result) => {    
              console.log(result);
              if(result.status =='t'){
                  window.location.replace(result.url);
              }

          },        
          (error) => {
              
          }
          );      
      
  }
  
    return(                                                         
                  <nav className="nav-panel-dark">
                    <div>                        
                        <span className="nav-title"> <img height="36" width="36" alt="Schema &#38; Structured Data" src={saswp_localize_data.plugin_url+'/admin_section/images/sd-logo-white.png'} />     Schema &#38; Structured Data</span>
                    </div>                    
                    <div className="saswp-divider-horizontal"></div>
                    <div className="nav-section">
                        <span className="nav-section-title">{__('Menu', 'schema-and-structured-data-for-wp')}</span>
                        <Link className={current.includes('schema') ? 'highlighted active nav-link' : 'nav-link'} to={`admin.php?page=saswp&path=schema`} ><span className="nav-link-text">{__('Schema Types', 'schema-and-structured-data-for-wp')}</span></Link>
                        <Link className={current.includes('reviews') ? 'highlighted active nav-link' : 'nav-link'} to={`admin.php?page=saswp&path=reviews`} ><span className="nav-link-text">{__('Reviews', 'schema-and-structured-data-for-wp')}</span></Link>
                        <Link className={current.includes('settings') ? 'highlighted active nav-link' : 'nav-link'} to={`admin.php?page=saswp&path=settings`}><span className="nav-link-text">{__('Settings', 'schema-and-structured-data-for-wp')}</span></Link>
                        <a onClick={MoveToOldInterface} href="" className={"nav-link"} data-id={`old`} ><span className="nav-link-text">{__('Move to Old Interface', 'schema-and-structured-data-for-wp')}</span></a>
                        {/* <a onClick={MoveToOldInterface} href="" className="nav-link " data-id={`admin.php?page=saswp&path=settings`}>Free Vs Pro</a> */}                        
                        {saswp_localize_data.is_pro_active ? '': <a target="blank" href="https://structured-data-for-wp.com/pricing/" className="btn btn-success saswp-upgrade-btn">{__('UPGRADE TO PRO', 'schema-and-structured-data-for-wp')}</a>}
                    </div>
                    <div className="saswp-divider-horizontal"></div>
                    <div className="nav-section">
                        <span className="nav-section-title">{__('Support', 'schema-and-structured-data-for-wp')}</span>
                        <a href="https://structured-data-for-wp.com/contact-us/" target="_blank" className="nav-link">
                        <span className="nav-link-text">{__('Contact Us', 'schema-and-structured-data-for-wp')}</span>
                        </a>
                        <a href="https://structured-data-for-wp.com/docs/" target="_blank" className="nav-link">
                        <span className="nav-link-text">{__('Knowledge Base', 'schema-and-structured-data-for-wp')}</span>
                        </a>
                    </div>

                    <div className="saswp-divider-horizontal"></div>
                    <div className="nav-section">
                        <span className="nav-section-title">SASWP V {saswp_localize_data.saswp_version}</span>
                        <p className="description">adds Google Rich Snippets markup according to Schema.org guidelines to structure your site for SEO. (AMP Compatible)</p>                        
                    </div>

                    </nav>
    );
}
export default HomeNavLink;