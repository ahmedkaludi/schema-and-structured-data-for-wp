import React,{useState, useEffect} from 'react';
import queryString from 'query-string';
import {Link} from 'react-router-dom';
import Pagination from './../common/pagination/pagination';
import DottedSpinner from './../common/dotted-spinner/DottedSpinner';
import MainSpinner from './../common/main-spinner/MainSpinner';
import Icon from '@duik/icon'
import LeftContextMenu from './../common/Left-context-menu/LeftContextMenu';

import './Schema.scss';

const SchemaList = () => {

    const {__} = wp.i18n; 
    
    const [isLoaded, setIsLoaded]               = useState(true);  
    const [mainSpinner, setMainSpinner]         = useState(false);  
    const [partSpinner, setPartSpinner]         = useState(false);  
        
    const [postsData, setPostsData]             = useState([]);
    const [postsCount, setPostsCount]           = useState(0);
    const [pageCount, setPageCount]             = useState(0);
    const [paginateClicked, setPaginateClicked] = useState(1);
    const [currentPage, setCurrentPage]         = useState(1);
    const [moreBoxId, setMoreBoxId]             = useState(null);
    const [moreBoxIndex, setMoreBoxIndex]       = useState(null);

    const showMoreIconBox = (index, post_id) => {
                              
      if(moreBoxIndex != index || moreBoxId == null)  {
          setMoreBoxId(post_id);
          setMoreBoxIndex(index);
      }else{
          setMoreBoxId(null);
      }
     
    }
         
    const getSchemaList = (search_text, page) => { 
      
      let url = saswp_localize_data.rest_url + "saswp-route/get-schema-list?search_param="+search_text+"&page="+page;
      
      fetch(url, {
        headers: {                    
          'X-WP-Nonce': saswp_localize_data.nonce,
        }
      })
      .then(res => res.json())
      .then(
        (result) => {     
          console.log(result);
          setMainSpinner(false); 
          setPartSpinner(false);        
          setPostsData(result.posts_data);
          setPostsCount(result.posts_found);                     
        },        
        (error) => {
          
        }
      );            
  }

  const paginateSchema =(e) => { 
    e.preventDefault();                     
    setPartSpinner(true);               
    getSchemaList('', e.currentTarget.dataset.id);
    setPaginateClicked(e.currentTarget.dataset.index);
    setCurrentPage(e.currentTarget.dataset.id);    
  }

  const handleMoreClickAction =(data) => {
    
    setMoreBoxId(null);
    setMoreBoxIndex(null);
            
    if(data.post_id && data.action === 'delete'){       

      let newarr = [ ...postsData ];    
            newarr.splice(data.index,1);
            setPostsData(newarr);
            setPostsCount(postsCount -1);      
      
    } else if (data.post_id && data.action) {

      let newarr = [ ...postsData ];    
      let change_arr = newarr[data.index];
      change_arr.post.post_status = data.action;      
      newarr[data.index] = change_arr;
      setPostsData(newarr);

    }
    
  }
  
  useEffect(() => {                
    setMainSpinner(true);            
    getSchemaList('', 1);      
  }, [])

  useEffect(() => {                    

    if(postsCount > 10){            
      let page_count = Math.ceil(postsCount / 10);
      setPageCount(page_count)
    }

  }, [postsCount])

  

    return (                                        
      <div className="saswp-schema-list-container">        
        {mainSpinner ? <MainSpinner /> : ''}
        <div className="card-container">
          <div className="card">
          <div className="card-body">

            <div className="saswp-heading-top">
            <div>
              <h3>{__('Schema Types', 'schema-and-structured-data-for-wp')}</h3>
              <p>{postsCount} {__('types', 'schema-and-structured-data-for-wp')}</p>
            </div>
            <div>
            <Link className="btn btn-success"  to={'admin.php?page=saswp&path=schema_add&tab=popular_schema'}> <Icon style={{'marginRight':'7px'}}>plus_rounded</Icon>{__('Add New Schema', 'schema-and-structured-data-for-wp')}</Link>
            </div>
            </div>

            <div className="saswp-schema-list">
                <div><p>{__('NAME', 'schema-and-structured-data-for-wp')}</p></div>
                <div><p>{__('LOCATION', 'schema-and-structured-data-for-wp')}</p></div>
                <div></div>
            </div>                          
          </div>
          <div className="divider-horizontal"></div>  
          
          {partSpinner ? 
            <DottedSpinner />
          : 
          <div>
            { postsData ?   
          <div className="saswp-schema-list-body">
          <div className="card-body">
            <ul>
          { postsData.map((item, index) => (  

            <li key={index} className="saswp-schema-list">
            <div>
            <Link to={`admin.php?page=saswp&path=schema_single&type=${item.post_meta.schema_type}&id=${item.post.post_id}`} className="quads-edit-btn"> <strong>{item.post_meta.schema_type}</strong>{item.post.post_status == 'draft' ? <span> ( Draft )</span> : ''}</Link>               
            </div>
            <div>
            
            {
              ( typeof(item.post_meta) != 'undefined' && typeof(item.post_meta.target_enable) != 'undefined' && item.post_meta.target_enable) ?  
              <div>                
                {
                  
                  item.post_meta.target_enable.map( (ktem, j) => {
                  return(<span className="saswp-location-label" key={j}>{ktem}<span>, </span></span>)
                })
                }              
              </div>
              : null
            }

            
            {
              ( typeof(item.post_meta) != 'undefined' && typeof(item.post_meta.target_exclude) != 'undefined' && item.post_meta.target_exclude) ?  
              <div>                
                {
                  
                  item.post_meta.target_exclude.map( (ktem, j) => {
                  return(<span className="saswp-location-label" key={j}>{ktem}<span>, </span></span>)
                })
                }              
              </div>
              : null
            }

            <p>{item.post.post_modified}</p></div>
            <div className="saswp-more-action">
              {moreBoxId === item.post.post_id ? 
                <LeftContextMenu onMoreAction={handleMoreClickAction} Option={
                [{menu_name:(item.post.post_status == 'draft' ? 'Publish' : 'Draft'), menu_post_id:item.post.post_id, menu_action:(item.post.post_status == 'draft' ? 'publish' : 'draft'), index:index},
                {menu_name:'Delete', menu_post_id:item.post.post_id, menu_action:'delete', index:index},
                ]} />
              :''
              }               
               <Icon onClick={() =>showMoreIconBox(index, item.post.post_id)} style={{'fontSize': '20px', 'cursor': 'pointer'}}>more</Icon>
             </div>
            </li>
            ))} 
            </ul>
          </div>          
          </div>
          : <div className="saswp-not-found">{__('Schema not found', 'schema-and-structured-data-for-wp')} 
          <Link className="btn btn-success"  to={'admin.php?page=saswp&path=schema_add&tab=popular_schema'}><Icon style={{'marginRight':'7px'}}>plus_rounded</Icon>{__('Add New Schema', 'schema-and-structured-data-for-wp')}</Link>
          </div>  
          
           }
            </div>
          }            
          
          </div>
        </div>
        {postsCount > 10 ? 
        <div className="saswp-list-pagination">
          <Pagination pageCount={pageCount} postsCount={postsCount} paginateClicked={paginateClicked} onPaginate = {paginateSchema} />
        </div> : ''}
      </div>
      
);
}
export default SchemaList;