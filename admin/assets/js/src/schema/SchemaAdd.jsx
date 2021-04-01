import React, {useState, useEffect} from 'react';
import queryString from 'query-string'
import SchemaTypeNavLink from '../schema-type-nav-link/SchemaTypeNavLink'
import {Link} from 'react-router-dom';


const SchemaAdd = () => {
        
        const {__} = wp.i18n; 
        const page = queryString.parse(window.location.search);   
        
        const popular_schema_arr = [
          {id: "Article", name:"Article", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/article.png'},
          {id: "BlogPosting", name:"BlogPosting", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/article.png'},
          {id: "Book", name:"Book", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/book.png'},
          {id: "Course", name:"Course", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/course.png'},
          {id: "FAQ", name:"FAQ", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/faq.png'},
          {id: "HowTo", name:"HowTo", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/howto.png'},
          {id: "Movie", name:"Movie", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/movie.png'},
          {id: "NewsArticle", name:"NewsArticle", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/article.png'},
          {id: "Review", name:"Review", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/reviewsnippet.png'},
          {id: "Recipe", name:"Recipe", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/recipe.png'},
          {id: "RealEstateListing", name:"RealEstateListing", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "SoftwareApplication", name:"SoftwareApplication", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/product.png'},
          {id: "TechArticle", name:"TechArticle", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/article.png'},
          {id: "WebPage", name:"WebPage", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/article.png'},
          {id: "Event", name:"Event", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/event.png'},
          {id: "JobPosting", name:"JobPosting", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/jobposting.png'},
          {id: "Service", name:"Service", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/localbusiness.png'},
          {id: "VideoObject", name:"VideoObject", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/videoobject.png'},
          {id: "local_business", name:"Local Business", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/localbusiness.png'},
          {id: "Product", name:"Product", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/product.png'},
          {id: "Person", name:"Person", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "Organization", name:"Organization", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/organization.png'},

        ];

        const all_schema_arr = [
          {id: "Apartment", name:"Apartment",  image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "House", name:"House", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "HotelRoom", name:"HotelRoom", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "SingleFamilyResidence", name:"SingleFamilyResidence", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "Article", name:"Article", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/article.png'},
          {id: "Audiobook", name:"Audiobook", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/article.png'},
          {id: "BlogPosting", name:"BlogPosting", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/article.png'},
          {id: "Book", name:"Book", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/book.png'},
          {id: "Course", name:"Course", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/course.png'},
          {id: "CreativeWorkSeries", name:"CreativeWorkSeries", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "DiscussionForumPosting", name:"DiscussionForumPosting", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/qanda.png'},
          {id: "DataFeed", name:"DataFeed", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "FAQ", name:"FAQ", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/faq.png'},
          {id: "HowTo", name:"HowTo", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/howto.png'},
          {id: "ImageObject", name:"ImageObject", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "MusicPlaylist", name:"MusicPlaylist", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "MusicAlbum", name:"MusicAlbum", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "MusicComposition", name:"MusicComposition", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "Movie", name:"Movie", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/movie.png'},
          {id: "NewsArticle", name:"NewsArticle", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/article.png'},
          {id: "PodcastEpisode", name:"PodcastEpisode", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "PodcastSeason", name:"PodcastSeason", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "Photograph", name:"Photograph", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "qanda", name:"Q&A", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/qanda.png'},
          {id: "Review", name:"Review", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/reviewsnippet.png'},
          {id: "Recipe", name:"Recipe", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/recipe.png'},
          {id: "RealEstateListing", name:"RealEstateListing", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "TVSeries", name:"TVSeries", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "SoftwareApplication", name:"SoftwareApplication", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/product.png'},
          {id: "SpecialAnnouncement", name:"SpecialAnnouncement", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/specialannouncement.png'},
          {id: "MobileApplication", name:"MobileApplication", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/product.png'},
          {id: "TechArticle", name:"TechArticle", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/article.png'},
          {id: "WebPage", name:"WebPage", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/article.png'},
          {id: "Event", name:"Event", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/event.png'},
          {id: "VideoGame", name:"VideoGame", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "ItemList", name:"ItemList", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/itemlist.png'},
          {id: "JobPosting", name:"JobPosting", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/jobposting.png'},
          {id: "Service", name:"Service", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/localbusiness.png'},
          {id: "TaxiService", name:"TaxiService", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/localbusiness.png'},
          {id: "Trip", name:"Trip", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "BoatTrip", name:"BoatTrip", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "AudioObject", name:"AudioObject", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "VideoObject", name:"VideoObject", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/videoobject.png'},
          {id: "MedicalCondition", name:"MedicalCondition", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "local_business", name:"Local Business", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/localbusiness.png'},
          {id: "Product", name:"Product", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/product.png'},
          {id: "Car", name:"Car", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/product.png'},
          {id: "Vehicle", name:"Vehicle", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/product.png'},
          {id: "TouristAttraction", name:"TouristAttraction", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "TouristDestination", name:"TouristDestination", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "LandmarksOrHistoricalBuildings", name:"LandmarksOrHistoricalBuildings", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "BuddhistTemple", name:"BuddhistTemple", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "Church", name:"Church", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "HinduTemple", name:"HinduTemple", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "Mosque", name:"Mosque", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "ApartmentComplex", name:"ApartmentComplex", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "EducationalOccupationalCredential", name:"EducationalOccupationalCredential", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "Person", name:"Person", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "Organization", name:"Organization", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/organization.png'},
          {id: "Project", name:"Project", image:saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "PsychologicalTreatment", name:"PsychologicalTreatment", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          {id: "RentAction", name:"RentAction", image: saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/default.png'},
          
        ];

        const [popularSchema, setpopularSchema] = useState(popular_schema_arr);
        const [allSchema, setAllSchema] = useState(all_schema_arr);
        
        const handleSearch = event => {    
          
          if(event.target.value !=''){
                        
            if(page.tab == 'all_schema'){
              let foundValue = allSchema.filter(obj=>obj.name.toLowerCase().includes(event.target.value.toLowerCase()));      
              setAllSchema(foundValue);
            }else{
              let foundValue = popularSchema.filter(obj=>obj.name.toLowerCase().includes(event.target.value.toLowerCase()));      
              setpopularSchema(foundValue);
            }
                        
          }else{
            setpopularSchema(popular_schema_arr);
            setAllSchema(all_schema_arr);
          }
          
        }

        // useEffect(() => {
        //   setName({firstName: 'Shedrack', surname: 'Akintayo'})
        //   setTitle({'My Full Name'}) //Set Title
        // }, [])// pass in an empty array as a second argument

        return (                                        
          <div className="saswp-addschema-page">

          <div className="saswp-close-div">
          <Link className="btn btn-sm saswp-close"  to={'admin.php?page=saswp'}>{__('x close', 'schema-and-structured-data-for-wp')}</Link>                                                                
          </div>
          <div className="saswp-addschema-heading">
            <div className="saswp-addschema-heading-left">
              <p>60 TYPES</p>
              <h2>Add New Schema Type</h2>
              <p>Choose the schema built for tech products photographers of the great details. Choose from hundreds of high quality effects built by other professional </p>
            </div>
            <div className="saswp-addschema-heading-right">
              {saswp_localize_data.is_pro_active ? '' : <a className="btn btn-success">Upgrade to PRO</a>}              
              </div>
          </div>
          <div className="saswp-addschema-body">
            
            <div className="saswp-addschema-body-tab">

            <div className="saswp-schema-type-tabbar">  

            <div className="saswp-list-grid">
            <i className="uikon">gallery_grid_view</i>
            <div className="saswp-divider-vertical"></div>
            <i className="uikon">view_list</i>
            </div>  

            <SchemaTypeNavLink />
            <div className="saswp-search-box">
            <div className="form-group"><div className="input-group"><div className="input-group-prepend">
            <i className="uikon">search_left</i>
            </div><input onChange={handleSearch} className="form-control" type="text" placeholder="Type to search..."/></div></div>
            </div>
            </div>

            
              <div className="">
              {(() => {

              let current = 'popular_schema';
              if(typeof(page.tab)  != 'undefined' ) { 
                current = page.tab;
              }

              const popular_schema_item = popularSchema.map((item) =>
              <li key={item.id}>                        
                <div className="card saswp-add-card">
                <Link to={`admin.php?page=saswp&path=schema_single&type=${item.id}`}>  
                <img src={item.image}/>
                <h2>{item.name}</h2>
                </Link>
                </div>                                                  
              </li>
              );

              const all_schema_item = allSchema.map((item) =>
              <li key={item.id}>                        
                <div className="card saswp-add-card">
                <Link to={`admin.php?page=saswp&path=schema_single&type=${item.id}`}>  
                <img src={item.image}/>
                <h2>{item.name}</h2>
                </Link>
                </div>                                                  
              </li>
              );
              switch (current) {

                case "popular_schema":
                  return (<div className="saswp-addschema-body-list">
                  <ul>                  
                      {popular_schema_item}                        
                  </ul>
                  </div>);
                case "all_schema":
                  return (<div className="saswp-addschema-body-list">
                  <ul>
                  {all_schema_item}                            
                  </ul>
                  </div>);

              }

                })()}  
              </div>
            </div>
            
          </div>

          </div>                                                                
      );
}
export default SchemaAdd;