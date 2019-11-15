import { Component, OnInit, OnDestroy, Inject } from '@angular/core';
import { HomeScriptsService } from '../services/home-scripts.service';
import { Subscription, Observable, Subject } from 'rxjs';
import { HttpClient } from '@angular/common/http';
import { JwtHelperService } from '@auth0/angular-jwt';
import { ActivatedRoute, Router } from '@angular/router';
import { IndexComponent } from '../index/index.component';
import { take } from 'rxjs/operators';
declare var jquery:any;
declare var $ :any;

@Component({
  selector: 'app-category',
  templateUrl: './category.component.html',
  styleUrls: ['./category.component.css']
})
export class CategoryComponent implements OnInit,OnDestroy {
  posts=null;
  category=null;
  category_color_id=null;
  loadLimitIndex=3;
  baseUrl:string;

  constructor( @Inject(IndexComponent) public homeIndex: IndexComponent,
               public homeScriptService: HomeScriptsService,
               private httpClient: HttpClient,
               private jwtHelper: JwtHelperService,
               private route: ActivatedRoute,
               private router:Router ) {
    this.router.routeReuseStrategy.shouldReuseRoute = function(){
      return false;
    }
  }

  ngOnInit() {
    this.baseUrl=window.location.origin;
    if(window.location.origin.indexOf(window.location.hostname+':')>-1){
      this.baseUrl=window.location.origin.substring(0,window.location.origin.indexOf(window.location.hostname+':'))+window.location.hostname;
    }
    this.homeScriptService.send_user_ip();
    this.category = this.route.snapshot.paramMap.get('category');
    this.httpClient.get<{jwt: string}>(this.baseUrl+`/api/list-pages`).pipe(take(1))
    .subscribe(
    res => {
        let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
        for (let index = 0; index < deoodedRES.data.pages.length; index++) {
          const page = deoodedRES.data.pages[index];
          if(page.name===this.category){
            this.category_color_id=page.id+4;
            console.log('color found');
            break;
          }
        }
    });
    
    this.httpClient.get<{jwt: string}>(this.baseUrl+`/api/list-posts`,{params:{post_category:this.category}})
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

    let categoryForJquery=this.category;
    $('.post_category').each(function (index, element) {
      $(element).html(categoryForJquery);
    });
  }

  load_more(){
    this.loadLimitIndex+=5;
    if(this.loadLimitIndex>this.posts.length){$('#load_more_button').css('display','none');}
  }

  ngOnDestroy() {
    this.homeScriptService.dispose();
  }

}
