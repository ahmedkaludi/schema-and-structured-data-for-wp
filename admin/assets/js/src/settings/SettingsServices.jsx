import React, { useState, useEffect, useReducer } from 'react';
import './Settings.scss';

const SettingsServices = () => {

	const {__}  = wp.i18n; 

  return(
    
    <div className="saswp-pre-ftrs-wrap">
		<ul className="saswp-features-blocks">
                        <li>
				<div className="saswp-features-ele">
					<div className="saswp-ele-ic saswp-ele-4" style={{background: '#69e781'}}>
                                            <img src="http://localhost/wordpress/wp-content/plugins/schema-and-structured-data-for-wp//admin_section/images/support-1.png"/>
					</div>
					<div className="saswp-ele-tlt">
						<h3>{__('Priority Support', 'schema-and-structured-data-for-wp')}</h3>
						<p>{__('We get more than 100 technical queries a day but the Priority support plan will help you skip that and get the help from a dedicated team.', 'schema-and-structured-data-for-wp')}</p>
					</div>
				</div>
                                <a className="btn btn-success" target="_blank" href="https://structured-data-for-wp.com/priority-support//">
								{__('Try it', 'schema-and-structured-data-for-wp')}   
                                </a>
				
			</li>
			<li>
				<div className="saswp-features-ele">
					<div className="saswp-ele-ic saswp-ele-3" style={{background:'#cacaca'}}>
                                            <img src="http://localhost/wordpress/wp-content/plugins/schema-and-structured-data-for-wp//admin_section/images/news.png"/>
					</div>
					<div className="saswp-ele-tlt">
						<h3>{__('Google News Schema Setup', 'schema-and-structured-data-for-wp')}</h3>
						<p>{__('Get quick approval to Google News with our service. Our structured data experts will set up the Google News schema properly on your website.', 'schema-and-structured-data-for-wp')}</p>
					</div>
				</div>
                            <a className="btn btn-success" target="_blank" href="http://structured-data-for-wp.com/services/google-news-schema-setup/">
							{__('Try it', 'schema-and-structured-data-for-wp')}  
                            </a>
				
			</li>
			<li>
				<div className="saswp-features-ele">
					<div className="saswp-ele-ic saswp-ele-4" style={{background: '#9c56cc'}}>
                                            <img src="http://localhost/wordpress/wp-content/plugins/schema-and-structured-data-for-wp//admin_section/images/schema-setup-icon.png" />
					</div>
					<div className="saswp-ele-tlt">
						<h3>{__('Structured Data Setup', 'schema-and-structured-data-for-wp')} &amp; {__('Error Clean Up', 'schema-and-structured-data-for-wp')}</h3>
						<p>{__('We will help you setup Schema and Structured data on your website as per your requirements and as per recommendation by our expert developers.', 'schema-and-structured-data-for-wp')}</p>
					</div>
				</div>
                                <a className="btn btn-success" target="_blank" href="http://structured-data-for-wp.com/services/structured-data-setup-error-clean-up/">
								{__('Try it', 'schema-and-structured-data-for-wp')} 
                                </a>
				
			</li>                        
		</ul>
	</div>
    
  );

}

export default SettingsServices;