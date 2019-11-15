import { Component, OnInit, OnDestroy } from '@angular/core';
import { Location } from '@angular/common';
import { JwtHelperService } from '@auth0/angular-jwt';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { ActivatedRoute } from '@angular/router';
import { HomeScriptsService } from 'src/app/home/services/home-scripts.service';
declare var jquery:any;
declare var $ :any;

@Component({
  selector: 'app-blog-post',
  templateUrl: './blog-post.component.html',
  styleUrls: ['./blog-post.component.css']
})
export class BlogPostComponent implements OnInit,OnDestroy {
  post=null;
  id=null
  baseUrl:string;

  constructor( public jwtHelper: JwtHelperService,
              public httpClient: HttpClient,
              public router: Router,
              private location: Location,
              private route: ActivatedRoute,
              private homeScriptService: HomeScriptsService ) { 
  }

  ngOnInit() {
    this.baseUrl=window.location.origin;
    if(window.location.origin.indexOf(window.location.hostname+':')>-1){
      this.baseUrl=window.location.origin.substring(0,window.location.origin.indexOf(window.location.hostname+':'))+window.location.hostname;
    }
    this.homeScriptService.send_user_ip();
    this.id = this.route.snapshot.paramMap.get('id');
    this.httpClient.get<{jwt: string}>(this.baseUrl+`/api/read-post`,{params:{id:this.id}})
    .subscribe(
    res => {
        let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
        console.log(deoodedRES.data.message);
        this.post=deoodedRES.data.post;
        this.post.author_social_links=JSON.parse(this.post.author_social_links);
        document.getElementById('summerNoteData').innerHTML=deoodedRES.data.post.content;
        let newHeaderEl=`
        <!-- /Page Header -->
        <div id="post-header" class="page-header" *ngIf="this.router.url.indexOf('/blog-post/')>-1">
          <div class="background-img" style="background-image: url('./img/post-page.jpg');"></div>
          <div class="container">
              <div class="row">
                  <div class="col-md-10">
                      <div class="post-meta">
                          <a class="post-category cat-2" href="category.html">${this.post.post_category}</a>
                          <span class="post-date">${new Date(this.post.date_add.substring(0,this.post.date_add.indexOf(' '))).toDateString()}</span>
                      </div>
                      <h1 class="post-title">${this.post.post_title}</h1>
                  </div>
              </div>
          </div>
        </div>
        <!-- /Page Header -->
        `
        document.getElementById('page-header').innerHTML=newHeaderEl;
        
    });
  }

  send_mail(){
    window.location.href = "mailto:user@example.com?subject=Subject&body=message%20goes%20here";
  }

  ngOnDestroy() {
    document.getElementById('page-header').innerHTML='';
    this.homeScriptService.dispose();
  }

}
