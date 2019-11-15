import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { JwtHelperService } from '@auth0/angular-jwt';

@Component({
  selector: 'app-aside-featured-posts',
  templateUrl: './aside-featured-posts.component.html',
  styleUrls: ['./aside-featured-posts.component.css']
})
export class AsideFeaturedPostsComponent implements OnInit {
  featured_posts=null;
  pages=null;
  baseUrl:string;

  constructor( private httpClient: HttpClient,
               private jwtHelper: JwtHelperService ) { }

  ngOnInit() {
    this.baseUrl=window.location.origin;
    if(window.location.origin.indexOf(window.location.hostname+':')>-1){
      this.baseUrl=window.location.origin.substring(0,window.location.origin.indexOf(window.location.hostname+':'))+window.location.hostname;
    }

    this.httpClient.get<{jwt: string}>(this.baseUrl+`/api/list-pages`)
    .subscribe(
    res => {
        let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
        this.pages=deoodedRES.data.pages;
        this.httpClient.get<{jwt: string}>(this.baseUrl+`/api/list-featured-posts`)
        .subscribe(
        res => {
            let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
            this.featured_posts=deoodedRES.data.posts;
            this.featured_posts.forEach(function (post,index) {
              post.index = index;
            });
        })
    });


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

}
