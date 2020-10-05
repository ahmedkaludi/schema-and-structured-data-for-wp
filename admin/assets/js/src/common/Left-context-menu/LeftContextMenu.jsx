import React, { useState, useEffect, useRef } from 'react';
import './LeftContextMenu.scss';
import useOutsideClick from "./../useOutsideClick";
import MainSpinner from './../main-spinner/MainSpinner';

const LeftContextMenu = (props) => {

  const [openMenu, setOpenMenu]             = useState(true);
  const [mainSpinner, setMainSpinner]       = useState(false); 

  const ref = useRef();
  
  useOutsideClick(ref, () => {
    setOpenMenu(false);
    props.onMoreAction({post_id:null, action:''});                                                                     
  });

  const handleMenuAction = (e) => {
                          
    let   process         = false;
    let   body_json       = {};
    let   post_id         = e.currentTarget.dataset.id;    
    let   index           = e.currentTarget.dataset.index;    
    let   action          = e.currentTarget.dataset.action; 
    body_json.post_id     = post_id;
    body_json.action      = action;

    if(action === 'delete'){

      let saswp_confirm = confirm("Are you sure?");

      if(saswp_confirm == true){
        process = true;
      }else{
        props.onMoreAction({post_id:null, action:''});                                                                     
        setOpenMenu(false);
      }
    }else{
      process = true;
    }

    if(process){

    setMainSpinner(true);

    let url = saswp_localize_data.rest_url + 'saswp-route/more-action';
    fetch(url,{
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
            setMainSpinner(false);  
            if(result.status ==='t'){
              props.onMoreAction({post_id:post_id, action:action, index:index});                                                                     
            }            
      },        
      (error) => {                
      }
    ); 

    }
        
  }
  
  useEffect(() => {    

  });
  
  return (
    <>
    {mainSpinner ? <MainSpinner /> : ''}
    {openMenu ?
    <div className="saswp-left-context-menu" ref={ref}>                
      {props.Option ? 
      <ul>
        {
          props.Option.map( (item, i) =>        
          <li onClick={handleMenuAction} data-index={item.index} data-action={item.menu_action} data-id={item.menu_post_id} key={i}><i className="uikon">{item.menu_icon}</i> {item.menu_name} </li>
          )
        }
      </ul>       
      : ''}          
    </div>: ''
     }    
    </>
  );

}

export default LeftContextMenu;