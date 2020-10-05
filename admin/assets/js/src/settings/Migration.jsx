import React, { useState, useEffect, useReducer } from 'react';
import queryString from 'query-string'
import { Button } from '@duik/it';
import { BrowserRouter as Router, Switch, Route, Link, matchPath } from 'react-router-dom';
import './Settings.scss';

const Migration = (props) => {

  const [actionMessage, setActionMessage] = useState({
    schema            : '',
    schema_pro        : '',
    wp_seo_schema     : '',
    seo_pressor       : '',
    wpsso_core        : '',
    aiors             : '',
    wp_custom_rv      : '',
    starsrating       : '',
    schema_for_faqs   : '',
  });

  const [importedMessage, setImportedMessage] = useState({
    schema            : 'Imported',
    schema_pro        : '',
    wp_seo_schema     : '',
    seo_pressor       : '',
    wpsso_core        : '',
    aiors             : '',
    wp_custom_rv      : '',
    starsrating       : '',
    schema_for_faqs   : '',
  });

  const handleMigration = (e) => {

    const plugin_name = e.currentTarget.dataset.id;
    
    const body_json       = {};                
                
    body_json.plugin_name  = plugin_name;                 
    
    let url = saswp_localize_data.rest_url + "saswp-route/migration";
      
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
          
          setActionMessage({
            ...actionMessage,
            [plugin_name]: result.message
          });

        },        
        (error) => {
          
        }
      );            

  }

  const getMigrationStatus = () => {

    let url = saswp_localize_data.rest_url + "saswp-route/get-migration-status";
      
      fetch(url, {
        headers: {                    
          'X-WP-Nonce': saswp_localize_data.nonce,
        }
      })
      .then(res => res.json())
      .then(
        (result) => {                   
          setImportedMessage(result);
        },        
        (error) => {          
        }
      );            

  }

  useEffect(() => {
    getMigrationStatus();
  }, [])
  
  return(
    <div className="card">
      <div className="card-body">
        <h3>Migration</h3>
      </div>
      <div className="divider-horizontal"></div>
      <div className="card-body saswp-migration-body">
      <div><span className="saswp-migration-label">Schema Plugin</span><a onClick={handleMigration} data-id="schema" className="btn btn-success saswp-migration-button">Import</a></div>
      <div>        
            <div><span>{actionMessage.schema}</span></div>
            <div><span>{importedMessage.schema}</span></div>
      </div>
      </div>

      <div className="card-body saswp-migration-body">
      <div><span className="saswp-migration-label">Schema Pro</span><a onClick={handleMigration} data-id="schema_pro" className="btn btn-success saswp-migration-button">Import</a></div>
      <div>        
            <div><span>{actionMessage.schema_pro}</span></div>
            <div><span>{importedMessage.schema_pro}</span></div>
      </div>
      </div>

      <div className="card-body saswp-migration-body">
      <div><span className="saswp-migration-label">WP SEO Schema</span><a onClick={handleMigration} data-id="wp_seo_schema" className="btn btn-success saswp-migration-button">Import</a></div>
      <div>        
            <div><span>{actionMessage.wp_seo_schema}</span></div>
            <div><span>{importedMessage.wp_seo_schema}</span></div>
      </div>
      </div>

      <div className="card-body saswp-migration-body">
      <div><span className="saswp-migration-label">SEO Pressor</span><a onClick={handleMigration} data-id="seo_pressor" className="btn btn-success saswp-migration-button">Import</a></div>
      <div>        
            <div><span>{actionMessage.seo_pressor}</span></div>
            <div><span>{importedMessage.seo_pressor}</span></div>
      </div>
      </div>

      <div className="card-body saswp-migration-body">
      <div><span className="saswp-migration-label">WPSSO Core</span><a onClick={handleMigration} data-id="wpsso_core" className="btn btn-success saswp-migration-button">Import</a></div>
      <div>        
            <div><span>{actionMessage.wpsso_core}</span></div>
            <div><span>{importedMessage.wpsso_core}</span></div>
      </div>
      </div>

      <div className="card-body saswp-migration-body">
      <div><span className="saswp-migration-label">Schema – All In One Schema Rich Snippets</span><a onClick={handleMigration} data-id="aiors" className="btn btn-success saswp-migration-button">Import</a></div>
      <div>        
            <div><span>{actionMessage.aiors}</span></div>
            <div><span>{importedMessage.aiors}</span></div>
      </div>
      </div>

      <div className="card-body saswp-migration-body">
      <div><span className="saswp-migration-label">WP Customer Reviews</span><a onClick={handleMigration} data-id="wp_custom_rv" className="btn btn-success saswp-migration-button">Import</a></div>
      <div>        
            <div><span>{actionMessage.wp_custom_rv}</span></div>
            <div><span>{importedMessage.wp_custom_rv}</span></div>
      </div>
      </div>

      <div className="card-body saswp-migration-body">
      <div><span className="saswp-migration-label">Stars Rating</span><a onClick={handleMigration} data-id="starsrating" className="btn btn-success saswp-migration-button">Import</a></div>
      <div>        
            <div><span>{actionMessage.starsrating}</span></div>
            <div><span>{importedMessage.starsrating}</span></div>
      </div>
      </div>

      <div className="card-body saswp-migration-body">
      <div><span className="saswp-migration-label">FAQ Schema Markup – FAQ Structured Data</span><a onClick={handleMigration} data-id="schema_for_faqs" className="btn btn-success saswp-migration-button">Import</a></div>
      <div>        
            <div><span>{actionMessage.schema_for_faqs}</span></div>
            <div><span>{importedMessage.schema_for_faqs}</span></div>
      </div>
      </div>
      
    </div>
  );

}

export default Migration;