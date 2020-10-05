import React, { useState, useEffect } from 'react';
import './Settings.scss';

const TranslationPanel = (props) => {

  const [translation, setTranslation] = useState([]);

  const getTranslations = () => {

    let url = saswp_localize_data.rest_url + "saswp-route/get-translations";
      
      fetch(url, {
        headers: {                    
          'X-WP-Nonce': saswp_localize_data.nonce,
        }
      })
      .then(res => res.json())
      .then(
        (result) => {                             
          setTranslation(result);
        },        
        (error) => {          
        }
      );            

  }

  useEffect(() => {
    getTranslations();
  }, [])

  const renderData =  translation.map((item, index) => (  
    <tr key={index}><td>{item.name}</td><td><input value={props.userInput[item.key] ? props.userInput[item.key] : item.value } onChange={props.handleInputChange} type="text" name={item.key} /></td></tr>     
  ));
       
  return (
    <>
    <div className="card">
      <div className="card-header">
        <h3>Translation Panel</h3>
      </div>
      <div className="divider-horizontal"></div>
      <div className="card-body">
        <table className="saswp-translation-table">
          <tbody>
            {renderData}
          </tbody>
        </table>
      </div>
    </div>
    </>
  );

}

export default TranslationPanel;