import React from 'react';
import './FieldGenerator.scss';
import MediaUpload from '../mediaUpload/MediaUpload';

const FieldGenerator = (props) => {  

  return (
    <>
    <table className="form-table saswp-field-generator-table">
      <tbody>
        {
          props.fielddata.map( (item) => {
            
            let meta_value = item.default;

            if(props.postMeta[item.id]){
              meta_value = props.postMeta[item.id];
            }
            
            switch (item.type) {

              case 'textarea':
              
                return(
                  <tr key={item.id}>
                   <td>
                   <label>{item.label}</label>
                   </td> 
                   <td> 
                     <textarea rows="5" placeholder={( typeof item.attributes != 'undefined') ? item.attributes.placeholder : '' } onChange={props.handleInputChange} type="text" name={item.id} value={meta_value} /> 
                     <p>{item.note}</p>
                   </td>
                  </tr>);

                break;

              case 'media':

              let media_key   = item.id+'_detail';
              let img_src     = '';
              
              if(typeof props.postMeta[media_key] != 'undefined'){
                img_src = props.postMeta[media_key]['thumbnail'];
              }
              

                return(
                  <tr key={item.id}>
                   <td>
                   <label>{item.label}</label>
                   </td> 
                   <td> 
                     <MediaUpload data_id={`${item.id}_detail`} onSelection={props.handleManualFieldImage} src={img_src} />
                   </td>
                  </tr>
                  ); 
                
                break;
              case 'checkbox':
                
                  break;  
              case 'select':
                
                  break;  
              case 'radio':
                
                  break;          
            
              default:

              return(
              <tr key={item.id}>
               <td>
               <label>{item.label}</label>
               </td> 
               <td> 
                 <input placeholder={( typeof item.attributes != 'undefined') ? item.attributes.placeholder : '' } onChange={props.handleInputChange} type="text" name={item.id} value={meta_value} /> 
               </td>
              </tr>);                
            }

          })
        }
      </tbody>
    </table>
    </>
  )

}
export default FieldGenerator;