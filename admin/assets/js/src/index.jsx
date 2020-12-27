import React from 'react';
import ReactDOM from "react-dom";
import { Router, Switch, Route, useHistory } from 'react-router-dom';
import queryString from 'query-string'
import Schema from './Schema';
import Reviews from './Reviews'
import Settings from './settings/Settings'
import './style/common.scss'
import '@duik/it/dist/styles.css'
import '@duik/icon/dist/styles.css'
import { createBrowserHistory } from 'history';

const history = createBrowserHistory();

const HomeComponent = () => {
                    
            return (                                                                                           
                        <Switch>
                            <Route render={props => {                                        

                                const page = queryString.parse(window.location.search); 
                                                                   
                                    if(typeof(page.path)  == 'undefined' || page.path.includes('schema')) {                           
                                        return <Schema  {...props}/>;                         
                                    }                                                            
                                    else if( page.path.includes('reviews') ) {
                                        return <Reviews  {...props}/>;
                                    }
                                    else if(page.path.includes('settings')) {                                        
                                        return <Settings  {...props}/>;
                                    }
                                    else{
                                        return null;
                                    }                    
                                }}/>            
                    </Switch>                                                            
            );
    
}


const MenuComponent = () => {
    
    let history = useHistory();
    
    const handlePushURL = (e) => {
        e.preventDefault();
                
        let data_id = e.currentTarget.dataset.id;

        if(data_id == 'old'){

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

        }else{
            history.push(data_id);
        }
        
    }

    return(<>    
	<a href="admin.php?page=saswp" className="current menu-top toplevel_page_saswp" aria-current="page"><div className="wp-menu-arrow"><div></div></div><div className="wp-menu-image dashicons-before dashicons-admin-settings"><img src="6" alt=""/></div><div className="wp-menu-name">Structured Data</div></a>	
	<ul className="wp-submenu wp-submenu-wrap saswp-sub-menu">
                <li  className=""><a onClick={handlePushURL} href="" className="saswp-sub-menu" data-id={`admin.php?page=saswp&path=schema`} >Schema Types</a></li>
                <li  className=""><a onClick={handlePushURL} href="" className="saswp-sub-menu" data-id={`admin.php?page=saswp&path=reviews`} >Reviews</a></li>
                <li  className=""><a onClick={handlePushURL} href="" className="saswp-sub-menu" data-id={`admin.php?page=saswp&path=settings`}>Settings</a></li>
                <li  className=""><a onClick={handlePushURL} href="" className="saswp-sub-menu" data-id={`old`} >Move to Old Interface</a></li>
                {/* <li  className=""><a onClick={handlePushURL} href="" className="saswp-sub-menu" data-id={`admin.php?page=saswp&path=settings`}>Free Vs Pro</a></li> */}
                <li  className=""><a target="blank" href="https://structured-data-for-wp.com/pricing/" className="btn btn-success saswp-upgrade-btn">UPGRADE TO PRO</a></li>';
            </ul>	    
    </>);
}

ReactDOM.render(    
    <Router history={history}><HomeComponent /></Router>, document.getElementById('saswp-home-page')
);
ReactDOM.render(<Router history={history}><MenuComponent /></Router>, document.getElementById('toplevel_page_saswp'));