import React from 'react';
import queryString from 'query-string'
import {Route} from 'react-router-dom';
import ReviewsCollectionsSingle from './reviews/ReviewsCollectionsSingle';
import ReviewsMain from './reviews/ReviewsMain';
import ReviewsSingle from './reviews/ReviewsSingle';


const Reviews = () => {
  return(
    <>    
    <Route render={props => {                                        
      const page = queryString.parse(window.location.search); 
      let current = 'reviews';

          if(typeof(page.path)  != 'undefined' ) { 
            current = page.path;
          }

          switch (current) {

              case 'reviews':
                return <ReviewsMain/>;  
              case 'reviews_single':
                return <ReviewsSingle/>;  
              case 'reviews_collections_single':
                return <ReviewsCollectionsSingle/>;                
              default:
                return <ReviewsMain/>;              
          }
                             
      }}/>
    </>
      ); 
}
export default Reviews;