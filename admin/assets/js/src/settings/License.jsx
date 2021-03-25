import React from 'react';
import { useState, useEffect } from 'react';
import './Settings.scss';
import Icon from '@duik/icon'
import { Button } from '@duik/it'

const SASWPLicense = (props) => {

  const {__} = wp.i18n; 

  const [proExtensions, setProExtensions] = useState([]);
  
  const getActiveProExt = () => {

    let url = saswp_localize_data.rest_url + "saswp-route/get-premium-extensions";
      
      fetch(url, {
        headers: {                    
          'X-WP-Nonce': saswp_localize_data.nonce,
        }
      })
      .then(res => res.json())
      .then(
        (result) => {         
          setProExtensions(result);          
        },        
        (error) => {
          
        }
      );  

  }

  useEffect(() => {
    getActiveProExt();
  }, [])
  
  return (
    <>
    <div className="card">
      <div className="card-header">
        <h3>{__('License', 'schema-and-structured-data-for-wp')}</h3>
      </div>
      <div className="divider-horizontal"></div>
      <div className="card-body">
        {proExtensions ?
        <>
          <h4>{__('Activate your license to get updates and support', 'schema-and-structured-data-for-wp')}</h4>
          <table>
          <tbody>
            {proExtensions.map( (item, i) => {              
              return(
              item.status == 'Active' ? 
              <tr key={i}>
              <td>{item.name}</td>
              <td>{props.userInput[`${(item.add_on).toLowerCase()}_addon_license_key_status`] == 'active' ? <Icon>check</Icon> : <Icon>close</Icon>}</td>
              <td>                                              
              <input onChange={props.handleInputChange} value={props.userInput[`${(item.add_on).toLowerCase()}_addon_license_key`]} type="text" placeholder="Enter License Key" name={`${(item.add_on).toLowerCase()}_addon_license_key`} />                              
              </td>
              <td>
              {props.licenseActivationMessage[(item.add_on).toLowerCase()] ?
                <Button  success loading>  Loading success</Button>
                :
               <a data-id={(item.add_on).toLowerCase()} className="btn btn-success" onClick={props.handleLicenseActivation} >
               {props.userInput[`${(item.add_on).toLowerCase()}_addon_license_key_status`] == 'active' ? __('Deactivate', 'schema-and-structured-data-for-wp') : __('Activate', 'schema-and-structured-data-for-wp')}
               </a> 
              }
              </td>
                <td>{props.userInput[`${(item.add_on).toLowerCase()}_addon_license_key_message`] ? props.userInput[`${(item.add_on).toLowerCase()}_addon_license_key_message`] : null }</td>
              </tr>                
              : null
              )
            })}
          </tbody>
          </table>
          </>
        : null}                
      </div>
    </div>
    </>
  )
}
export default SASWPLicense;