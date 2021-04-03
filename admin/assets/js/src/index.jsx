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
import HomeComponent from './home';
import SchemaAdd from './schema/SchemaAdd';
const history = createBrowserHistory();

const IndexComponent = () => {
                                                                                                 
        return (<>                                                    
                        <Switch>
                            <Route render={props => {                                        

                                const page = queryString.parse(window.location.search); 
                                                                   
                                if( typeof(page.path) != 'undefined' && page.path.includes('schema_add') ) {
                                     return <SchemaAdd  {...props}/>;
                                }else{
                                    return <HomeComponent  {...props}/>;
                                }
                                                      
                                }}/>            
                    </Switch>                     
                </>);
    
}

ReactDOM.render(    
    <Router history={history}><IndexComponent /></Router>, document.getElementById('saswp-home-page')
);