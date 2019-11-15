import { Component, OnInit, OnDestroy } from '@angular/core';
import { HomeScriptsService } from '../services/home-scripts.service';
import { HttpClient } from '@angular/common/http';
import { JwtHelperService } from '@auth0/angular-jwt';
declare var jquery:any;
declare var $:any;

@Component({
  selector: 'home-popular',
  templateUrl: './popular.component.html',
  styleUrls: ['./popular.component.css']
})
export class PopularComponent implements OnInit,OnDestroy {
  posts=null;
  tempUnsortedPosts=null;
  user_visits=null;
  loadLimitIndex=3;
  pages=null;
  baseUrl:string;

  constructor( private homeScriptService: HomeScriptsService,
               private httpClient: HttpClient,
               private jwtHelper: JwtHelperService ) { }

  ngOnInit() {
    this.baseUrl=window.location.origin;
    if(window.location.origin.indexOf(window.location.hostname+':')>-1){
      this.baseUrl=window.location.origin.substring(0,window.location.origin.indexOf(window.location.hostname+':'))+window.location.hostname;
    }
    this.homeScriptService.send_user_ip();
    this.httpClient.get<{jwt: string}>(this.baseUrl+`/api/list-pages`)
    .subscribe(
    res => {
        let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
        this.pages=deoodedRES.data.pages;
    });

    this.httpClient.get<{jwt: string}>(this.baseUrl+`/api/list-posts`)
    .subscribe(
    res => {
        let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
        console.log(deoodedRES.data.message);
        this.tempUnsortedPosts=deoodedRES.data.posts;
        let postIndex=0;

        this.httpClient.get<{jwt: string}>(this.baseUrl+`/api/list-user-visits`,{params:{analytics_type:'link_click'}})
        .subscribe(
        res => {
            let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
            console.log(deoodedRES.data.message);
            this.user_visits=deoodedRES.data.analytics_data;
            // console.log(this.user_visits);
            this.sort_user_visits();
            this.sort_posts_by_user_visit();
            
        });
    });

  }

  load_more(){

    this.loadLimitIndex+=5;
    if(this.loadLimitIndex>this.posts.length){$('#load_more_button').css('display','none');}
  }

  get_color_id(post_category){
    // console.log(post_category);
    
    for (let index = 0; index < this.pages.length; index++) {
      const page = this.pages[index];
      if(post_category==='News'||post_category==='Popular'){
        return this.pages.length+100;
      }
      if(page.name===post_category){
        return page.id+4;
        break;
      }
    }
  }

  ngOnDestroy() {
    this.homeScriptService.dispose();
  }

  sort_user_visits(){
    // var rockets = [
    //   { country:'Russia', launches:32 },
    //   { country:'US', launches:23 },
    //   { country:'China', launches:16 },
    //   { country:'Europe(ESA)', launches:7 },
    //   { country:'India', launches:4 },
    //   { country:'Japan', launches:3 }
    // ];
    // // rockets.map((itm) => {
    // //     itm.launches += 10
    // //     return itm
    // // })
    // // eyni array deyishir ashagidakinda yox

    // var launchOptimistic = rockets.map(function(elem) {
    //   return {
    //     country: elem.country,
    //     launches: elem.launches+10,
    //   } 
    // });

    var sortedUserVisits = this.sortByFrequencyAndRemoveDuplicatesAndNONposts(this.user_visits);
    this.user_visits=sortedUserVisits;
    // console.log(sortedUserVisits);
  }

  sortByFrequencyAndRemoveDuplicatesAndNONposts(array) {
    array=array.map(function(elem) {
      return JSON.parse(elem.analytics_data).http_referer;
    });
    
    var frequency = {}, value;

    // compute frequencies of each value
    for(var i = 0; i < array.length; i++) {
        value = array[i];
        if(value in frequency) {
            frequency[value]++;
        }
        else {
            frequency[value] = 1;
        }
    }

    // make array from the frequency object to de-duplicate
    var uniques = [];
    for(value in frequency) {
        uniques.push(value);
    }

    // sort the uniques array in descending order by frequency
    function compareFrequency(a, b) {
        return frequency[b] - frequency[a];
    }

    var sorted=uniques.sort(compareFrequency);
    sorted=sorted.filter(function (elem) {
      if(elem.indexOf('/blog-post/')==-1)return false;
      else return true;
    });
    sorted=sorted.map(function(elem) {
      return {
        post_id:elem.substring(elem.indexOf('/blog-post/')).replace('/blog-post/',''),
        http_referer:elem,
        frequency:frequency[elem]
      };
    });
    // console.log(sorted);
    

    return sorted;
  }

  sort_posts_by_user_visit(){
    function mapOrder (array, order, key) {
  
      array.sort( function (a, b) {
        var A = a[key], B = b[key];
        
        if (order.indexOf(A) > order.indexOf(B)) {
          return 1;
        } else {
          return -1;
        }
        
      });

      if(array.length>order.length){
        var sortedOnes=array.slice(array.length-order.length);
        var remaining=array.slice(0,array.length-order.length);
        remaining=remaining.sort((a, b) => {
          return b.id - a.id;
        });
        sortedOnes=sortedOnes.concat(remaining);
        array=sortedOnes;
      }
      
      return array;
    };

    var posts_order = this.user_visits.map(function (elem) {
      return elem.post_id;
    });

    var ordered_posts = mapOrder(this.tempUnsortedPosts, posts_order, 'id');
    ordered_posts=ordered_posts.filter(function (post) {
      if(post.post_category.indexOf('Popular')>-1)return false;
      else return true;
    });
    ordered_posts.forEach(function (post,index) {
      post.index = index;
    });
    this.posts=ordered_posts;
    
    
  }

}
