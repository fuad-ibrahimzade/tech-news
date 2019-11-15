import { Component, OnInit, OnDestroy } from '@angular/core';
import {ActivatedRoute, Router} from "@angular/router";
import { HomeScriptsService } from '../services/home-scripts.service';
import { Subscription } from 'rxjs';
import { HttpClient } from '@angular/common/http';
import { JwtHelperService } from '@auth0/angular-jwt';

@Component({
  selector: 'home-main',
  templateUrl: './main.component.html',
  styleUrls: ['./main.component.css']
})
export class MainComponent implements OnInit,OnDestroy {
  posts=null;
  pages=null;
  featured_posts=null;
  baseUrl:string;

  constructor(  public router: Router,
                public route: ActivatedRoute,
                public homeScriptService: HomeScriptsService,
                public httpClient: HttpClient,
                private jwtHelper: JwtHelperService ) {
  }

  ngOnInit() {
    this.baseUrl=window.location.origin;
    if(window.location.origin.indexOf(window.location.hostname+':')>-1){
      this.baseUrl=window.location.origin.substring(0,window.location.origin.indexOf(window.location.hostname+':'))+window.location.hostname;
    }
    this.homeScriptService.send_user_ip();
    let navsideclose:HTMLElement=document.getElementsByClassName('nav-aside-close')[0] as HTMLElement;
    navsideclose.click()

    this.httpClient.get<{jwt: string}>(this.baseUrl+`/api/list-posts`)
    .subscribe(
    res => {
        let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
        console.log(deoodedRES.data.message);
        this.posts=deoodedRES.data.posts;
        let postIndex=0;
        this.posts.forEach(function (post,index) {
          post.index = index;
        });
    });

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

}
