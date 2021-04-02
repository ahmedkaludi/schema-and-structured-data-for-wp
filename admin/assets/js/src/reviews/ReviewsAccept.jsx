import React from 'react';
import './Reviews.scss';

const ReviewsAccept = () => {

  const {__} = wp.i18n; 

  return (
    <div className="card saswp-reviews-list-card">
      <div className="card-body">
        {__(' Use Below shortcode to show reviews form in your website. Using this you can accept reviews from your website directly', 'schema-and-structured-data-for-wp')}
      </div>
      <div className="card-body">      
      <table>
          <tbody>
              <tr><td>{__('Simple Form', 'schema-and-structured-data-for-wp')}</td><td><input type="text" value="[saswp-reviews-form]" readOnly /></td></tr>
              <tr><td>{__('Show form on button tap', 'schema-and-structured-data-for-wp')}</td><td><input type="text" value='[saswp-reviews-form onbutton="1"]' readOnly /></td></tr>
          </tbody>
      </table>
      </div>
    </div>
  )
}
export default ReviewsAccept;