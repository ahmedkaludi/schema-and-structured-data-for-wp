import React from 'react';

import ReviewsNavLink from '../reviews-nav-link/ReviewsNavLink';

import queryString from 'query-string'
import {Link, Route} from 'react-router-dom';

import ReviewsAccept from './ReviewsAccept';
import ReviewsCollections from './ReviewsCollections';
import ReviewsFetch from './ReviewsFetch';
import ReviewsList from './ReviewsList';

const ReviewsMain = () => {

    const page = queryString.parse(window.location.search);   

  return (
    <>
      <ReviewsNavLink />

      {(() => {

          switch (page.path) {

            case 'reviews':
              return <ReviewsList/>;              
            case 'reviews_collections':
              return <ReviewsCollections/>;              
            case 'reviews_accept':
              return <ReviewsAccept/>;  
            case 'reviews_fetch':
              return <ReviewsFetch/>;                             
            default:
              return null;              
        }

        })()}

    </>
  )
}
export default ReviewsMain;