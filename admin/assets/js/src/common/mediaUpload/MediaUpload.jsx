import React, { useState, useEffect } from 'react';
import './MediaUpload.scss';

const MediaUpload = (props) => {

const [imageSrc, setImageSrc] = useState('');    

const handleRemoveImage = (e) => {
    e.preventDefault();

    var image_data = {};

    image_data.id        = '';
    image_data.url       = '';
    image_data.width     = '';
    image_data.height    = '';
    image_data.thumbnail = '';

    setImageSrc('');
    props.onSelection(image_data);
}

const handleImageChoose = (e) => {
    e.preventDefault();

  var image_frame;
  
  if(image_frame){
        image_frame.open();
  }

  // Define image_frame as wp.media object
  image_frame = wp.media({
             library : {
                  type : 'image',
              }
         });

  image_frame.on('close',function() {
              // On close, get selections and save to the hidden input
              // plus other AJAX stuff to refresh the image preview
              var selection =  image_frame.state().get('selection');
              var image_data = {};
              var my_index = 0;
              
              selection.each(function(attachment) {                                            
                
                image_data.id        = attachment['id'];
                image_data.url       = attachment.attributes.sizes.full.url;                
                image_data.width     = attachment.attributes.sizes.full.width;
                image_data.height    = attachment.attributes.sizes.full.height;

                if(typeof(attachment.attributes.sizes.thumbnail)  != 'undefined' ) { 
                    image_data.thumbnail = attachment.attributes.sizes.thumbnail.url;
                }else{
                  image_data.thumbnail = '';
                }

              });
              
              if(props.data_id){
                image_data.data_id = props.data_id;
              }

              setImageSrc(image_data.url);
              props.onSelection(image_data);                            
           });   

  image_frame.on('open',function() {
          // On open, get the id from the hidden input
          // and select the appropiate images in the media manager
          var selection =  image_frame.state().get('selection');

        });
  image_frame.open();

}

  useEffect(() => {    
    setImageSrc(props.src);
  })

  return (
    <div className="saswp-media-upload">
      <div><a onClick={handleImageChoose} className="btn btn-default">Upload</a></div>
      {imageSrc ? <div className="saswp-image-preview"><img src={imageSrc}/><a onClick={handleRemoveImage}>X</a></div> : ''}
    </div>
  );

}

export default MediaUpload;