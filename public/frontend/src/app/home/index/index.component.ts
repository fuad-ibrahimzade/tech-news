import {Component, OnInit} from '@angular/core';
import {ActivatedRoute, Router} from "@angular/router";
import { HomeScriptsService } from '../services/home-scripts.service';
import { Subject } from 'rxjs';
import { HttpClient } from '@angular/common/http';
import { JwtHelperService } from '@auth0/angular-jwt';

@Component({
  selector: 'home-index',
  templateUrl: './index.component.html',
  styleUrls: ['./index.component.css']
})
export class IndexComponent implements OnInit {
  public pages=null;
  page='index'
  baseUrlForAssets:string;

  tempUnsortedPosts=null;
  public popular_posts=null;
  user_visits=null;

  constructor(  public router: Router,
                public route: ActivatedRoute,
                private homeScriptService:HomeScriptsService,
                private httpClient: HttpClient,
                private jwtHelper: JwtHelperService ) {

      this.baseUrlForAssets=window.location.origin;
      if(window.location.origin.indexOf(window.location.hostname+':')>-1){
        this.baseUrlForAssets=window.location.origin.substring(0,window.location.origin.indexOf(window.location.hostname+':'))+window.location.hostname;
      }
      this.loadScripts();
      this.homeScriptService.get_pages().subscribe(
          res => {
            this.pages=res;
          });
    
    
    this.httpClient.get<{jwt: string}>(this.baseUrlForAssets+`/api/list-posts`)
    .subscribe(
    res => {
        let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
        console.log(deoodedRES.data.message);
        this.tempUnsortedPosts=deoodedRES.data.posts;
        let postIndex=0;

        this.httpClient.get<{jwt: string}>(this.baseUrlForAssets+`/api/list-user-visits`,{params:{analytics_type:'link_click'}})
        .subscribe(
        res => {
            let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
            console.log(deoodedRES.data.message);
            this.user_visits=deoodedRES.data.analytics_data;
            this.sort_user_visits();
            this.sort_posts_by_user_visit();
            
        });
    });
  }

  public get_category_color_id(category){
    this.homeScriptService.get_pages().subscribe(
      res => {
        this.pages=res;
      });
    for (let index = 0; index < this.pages.length; index++) {
      const page = this.pages[index];
      if(page.name===category){
        return (page.id+4);
        break;
      }
    }
  }

  ngOnInit() {

  }

  // swapComponent(pagename){
  //   this.page=pagename;
  // }

  loadScripts() {
      // '/assets/js/jquery.min.js',
      //     '/assets/js/bootstrap.min.js',
      // 'http://'+window.location.hostname+'/frontend/dist/frontend/assets/js/main.js'
      const externalScriptArray = [
          this.baseUrlForAssets+'/assets/js/main.js'
      ];
      let scriptsElement=document.getElementById('scripts');
      while (scriptsElement.firstChild) {
        scriptsElement.removeChild(scriptsElement.firstChild);
      }
      for (let i = 0; i < externalScriptArray.length; i++) {
        const scriptTag = document.createElement('script');
        scriptTag.src = externalScriptArray[i];
        document.getElementById('scripts').appendChild(scriptTag);
      }
  }

  sort_user_visits(){
    var sortedUserVisits = this.sortByFrequencyAndRemoveDuplicatesAndNONposts(this.user_visits);
    this.user_visits=sortedUserVisits;
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
    this.popular_posts=ordered_posts;
    
    
  }

}
