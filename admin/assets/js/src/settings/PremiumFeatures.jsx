import React, { useState, useEffect, useReducer } from 'react';
import DottedSpinner from './../common/dotted-spinner/DottedSpinner';
import './Settings.scss';


const PremiumFeatures = () => {

  const [extensionList, setExtensionList] = useState([]);
  const [partSpinner, setPartSpinner]     = useState(false);  

  const getPremiumExtensions = () => {
    setPartSpinner(true);
    let url = saswp_localize_data.rest_url + "saswp-route/get-premium-extensions";
      
      fetch(url, {
        headers: {                    
          'X-WP-Nonce': saswp_localize_data.nonce,
        }
      })
      .then(res => res.json())
      .then(
        (result) => {         
          setPartSpinner(false);                    
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
                        <div> {__('Status :', 'schema-and-structured-data-for-wp')} <span>{item.status}</span></div>
                        <div>{item.status == 'InActive' ? <a target="_blank" href={item.download} className="btn btn-success">{__('Download', 'schema-and-structured-data-for-wp')}</a> : '' }   </div>
                    </div>
            </li>
  ));

  return(
    <>    
    {partSpinner ? 
        <DottedSpinner /> :
    <div className="saswp-pre-ftrs-wrap">
      <ul className="saswp-features-blocks">
      {extensions}
      </ul>
    </div>}
    </>
  );

}

export default PremiumFeatures;