import React from 'react';
import './Reviews.scss';

const ReviewsAccept = () => {
  return (
    <div className="card saswp-reviews-list-card">
      <div className="card-body">
      Use Below shortcode to show reviews form in your website. Using this you can accept reviews from your website directly
      </div>
      <div className="card-body">      
      <table>
          <tbody>
              <tr><td>Simple Form</td><td><input type="text" value="[saswp-reviews-form]" readOnly /></td></tr>
              <tr><td>Show form on button tap</td><td><input type="text" value='[saswp-reviews-form onbutton="1"]' readOnly /></td></tr>
          </tbody>
      </table>
      </div>
    </div>
  )
}
export default ReviewsAccept;