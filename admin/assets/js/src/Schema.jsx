import React, { Component} from 'react';
import queryString from 'query-string'
import {Link, Route} from 'react-router-dom';
import Settings from './settings/Settings'

import SchemaAdd from './schema/SchemaAdd';
import SchemaList from './schema/SchemaList'
import SchemaSingle from './schema/SchemaSingle'

class Schema extends Component {

    constructor(props) {  
      
        super(props);
        this.state = {                    
        };            
    }
  
    

  render() {

    return(
    <div>        
    <Route render={props => {                                        
      const page = queryString.parse(window.location.search); 
      let current = 'schema';

          if(typeof(page.path)  != 'undefined' ) { 
            current = page.path;
          }

          if(current  == 'schema' ) {                           
              return <SchemaList  {...props}/>;                         
          }                        
          else if(current == 'schema_add') {
              return <SchemaAdd  {...props}/>;
          }
          else if(current == 'schema_single') {
            return <SchemaSingle  {...props}/>;
          }          
          else{
              return null;
          }                    
      }}/> 
      </div>
      );    
  }
  
}

export default Schema;