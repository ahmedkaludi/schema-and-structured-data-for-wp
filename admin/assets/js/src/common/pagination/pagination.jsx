import React from 'react';
import ReactDOM from "react-dom";
import './pagination.scss';

const Pagination = (props) => {
           
        const paginate = [];

        for(var i=1; i <= props.pageCount; i++){            
         paginate.push(<a className={props.paginateClicked == i ? 'saswp-page-active' : ''} onClick={props.onPaginate} key={i} data-index={i} data-id={i} href="#">{i}</a>);                 
        }            
                
        return (
        <div className="saswp-schema-pagination">
            <a className={props.paginateClicked == 0 ? 'saswp-page-active' : ''} onClick={props.onPaginate} key={0} data-index={0} data-id="1" href="#">&laquo;</a>
            {paginate}
            <a className={(props.paginateClicked == (props.pageCount+1)) ? 'saswp-page-active' : ''} onClick={props.onPaginate} data-index={(props.pageCount+1)} key={(props.pageCount+1)} data-id={props.pageCount} href="#">&raquo;</a>
        </div>
        
    );
            
    
}
export default Pagination;