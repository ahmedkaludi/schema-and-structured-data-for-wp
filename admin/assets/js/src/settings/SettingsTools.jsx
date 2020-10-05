import React, { useState, useEffect, useReducer } from 'react';
import queryString from 'query-string'
import { Button } from '@duik/it';
import { BrowserRouter as Router, Switch, Route, Link, matchPath } from 'react-router-dom';
import './Settings.scss';


const SettingsTools = (props) => {

  const [extensionList, setExtensionList] = useState([]);

  const getPremiumExtensions = () => {

    let url = saswp_localize_data.rest_url + "saswp-route/get-premium-extensions";
      
      fetch(url, {
        headers: {                    
          'X-WP-Nonce': saswp_localize_data.nonce,
        }
      })
      .then(res => res.json())
      .then(
        (result) => {                             
          setExtensionList(result);
        },        
        (error) => {
          // this.setState({
          //   isLoaded: true,
           
          // });
        }
      );            

  }

  useEffect(() => {
    getPremiumExtensions();
  }, [])// pass in an empty array as a second argument

  const extensions =  extensionList.map((item, index) => (      
    <li key={index}>                    
                    <div className="saswp-features-ele">
                        <div className="saswp-ele-ic" style={{background: item.background}}>
                                <img src={item.image}/>
                            </div>
                            <div className="saswp-ele-tlt">
                                    <h3>{item.name}</h3>
                                    <p>{item.description}</p>
                            </div>
                    </div>
                    <div className="saswp-sts-btn">                      
                        <div> Status : <span>{item.status}</span></div>
                        <div>{item.status == 'InActive' ? <a className="btn btn-success">Download</a> : '' }   </div>                                                                
                    </div>
            </li>
  ));

  return(
    <div className="card">
      <div className="card-body">
        <h3>Tools</h3>
      </div>
      <div className="divider-horizontal"></div>
      <div className="card-body">
      <table className="saswp-tools-table">
        <tbody>
        <tr>
          <td>
          Export All Settings And Schema
          </td>
          <td>
            <a href={`${saswp_localize_data.rest_url}saswp-route/export-settings`} className="btn btn-success">Export</a>
          </td>
        </tr>
        <tr>
          <td>
          Import All Settings And Schema
          </td>
          <td>
          <input type="file" name="import_file" onChange={props.handleInputChange}/>
          </td>
        </tr>
        <tr>
          <td>
          Reset Settings
          </td>
          <td>
            <a className="btn btn-success" onClick={props.resetSettings}>Reset</a>
          </td>
        </tr>
        <tr>
          <td>
          Data Tracking Allow
          </td>
          <td>
            <a className="btn btn-success">Allow</a>
          </td>
        </tr>
        <tr>
          <td>
          Remove Data On Uninstall
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