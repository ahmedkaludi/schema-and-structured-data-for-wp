import React, { useState, useEffect } from 'react';
import './Settings.scss';

const SettingsTools = (props) => {
  
  const {__} = wp.i18n; 
        
  return(
    <div className="card">
      <div className="card-body">
        <h3>{__('Tools', 'schema-and-structured-data-for-wp')}</h3>
      </div>
      <div className="divider-horizontal"></div>
      <div className="card-body">
      <table className="saswp-tools-table">
        <tbody>
        <tr>
          <td>
          {__('Export All Settings And Schema', 'schema-and-structured-data-for-wp')}
          </td>
          <td>
            <a href={`${saswp_localize_data.rest_url}saswp-route/export-settings`} className="btn btn-success">{__('Export', 'schema-and-structured-data-for-wp')}</a>
          </td>
        </tr>
        <tr>
          <td>
          {__('Import All Settings And Schema', 'schema-and-structured-data-for-wp')}
          </td>
          <td>
          <input type="file" name="import_file" onChange={props.handleInputChange}/>
          </td>
        </tr>
        <tr>
          <td>
          {__('Reset Settings', 'schema-and-structured-data-for-wp')}
          </td>
          <td>
            <a className="btn btn-success" onClick={props.resetSettings}>{__('Reset', 'schema-and-structured-data-for-wp')}</a>
          </td>
        </tr>
        <tr>
          <td>
          {__('Data Tracking Allow', 'schema-and-structured-data-for-wp')}
          <p>{__('We guarantee no sensitive data is collected ', 'schema-and-structured-data-for-wp')}
                            <a target="_blank" href="https://structured-data-for-wp.com/docs/article/usage-data-tracking/">{__('Learn more', 'schema-and-structured-data-for-wp')}</a>
                        </p>
          </td>
          <td>
            <a href={saswp_localize_data.track_url} className="btn btn-success">{saswp_localize_data.track_text}</a>
          </td>
        </tr>
        <tr>
          <td>
          {__('Remove Data On Uninstall', 'schema-and-structured-data-for-wp')}
          </td>
          <td>
                <label className="form-check form-group toggle">
                <input name="saswp_rmv_data_on_uninstall" checked={props.userInput['saswp_rmv_data_on_uninstall']} onChange={props.handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label"></span>                  
                </label>
          </td>
        </tr>
        </tbody>
      </table>
      </div>
    </div>
  );

}

export default SettingsTools;