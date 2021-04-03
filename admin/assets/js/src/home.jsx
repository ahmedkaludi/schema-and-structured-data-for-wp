import React from 'react';
import ReactDOM from "react-dom";
import { Router, Switch, Route } from 'react-router-dom';
import queryString from 'query-string'
import Schema from './Schema';
import Reviews from './Reviews'
import Settings from './settings/Settings'
import './style/common.scss'
import '@duik/it/dist/styles.css'
import '@duik/icon/dist/styles.css'
import { createBrowserHistory } from 'history';
import HomeNavLink from './home-nav-link/HomeNavLink';

const history = createBrowserHistory();

const HomeComponent = () => {
                                                                                                 
        return (<>            
                    <div className="saswp-main-nav-panel">
                    <Switch>
                         <HomeNavLink />
                    </Switch>
                    </div>
                    <div className="saswp-main-nav-content">
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
                    </div>                                                                                                           
                </>);
    
}
export default HomeComponent;