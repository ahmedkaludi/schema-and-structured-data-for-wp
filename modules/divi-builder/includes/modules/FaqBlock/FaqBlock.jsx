// External Dependencies
import React, { Component, Fragment } from 'react';
import ReactHtmlParser from 'react-html-parser';

// Internal Dependencies
import './style.css';

class FaqBlock extends Component {
    
  static slug = 'saswp_divi_faqblock';
   
    render() {
        
    const items = [];  
    const item_list  = this.props.content;            
    const order_type = this.props.order_type;
        
        for(var i=0; i < item_list.length; i++){
                        
           if( order_type === ''){
              
               items.push(
                <li className="saswp-divi-faq-li" key={i}>
                <h3 className="saswp-hello-header-heading">{item_list[i].props.attrs.faq_question}</h3>
                <p >{ReactHtmlParser(item_list[i].props.attrs.faq_answer)}</p>
                </li>
              ); 
               
           }else if(order_type === 'order_list'){
              items.push(
               <li className="saswp-divi-faq-li" key={i}>               
                <h3 className="saswp-hello-header-heading"> <span>{i+1} . </span> {item_list[i].props.attrs.faq_question}</h3>
                <p >{ReactHtmlParser(item_list[i].props.attrs.faq_answer)}</p>
                </li>
              ); 
               
           }else{
                items.push(
                <li key={i}>
                <h3 className="saswp-hello-header-heading">{item_list[i].props.attrs.faq_question}</h3>
                <p >{ReactHtmlParser(item_list[i].props.attrs.faq_answer)}</p>
                </li>
              ); 
           } 
                       
        }
     
    return (            
      <Fragment>      
        <ul>
        {items}
        </ul>      
      </Fragment>
    );
  }
}

export default FaqBlock;
