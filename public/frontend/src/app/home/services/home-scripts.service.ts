import { Injectable, OnDestroy } from '@angular/core';
import { JwtHelperService } from '@auth0/angular-jwt';
import { HttpClient } from '@angular/common/http';
import { Observable, Subject, Subscription } from 'rxjs';
import { take } from 'rxjs/operators';
import { Router } from '@angular/router';

@Injectable({
  providedIn: 'root'
})
export class HomeScriptsService implements OnDestroy {
  pages=new Subject<any>();
  tempSearchedPages=null;
  social_links=new Subject<any>();
  subscriptions: Subscription[]=[];
  baseUrl:string;

  constructor( public jwtHelper: JwtHelperService,
              public httpClient: HttpClient,
              public router: Router ) { 
      this.baseUrl=window.location.origin;
      if(window.location.origin.indexOf(window.location.hostname+':')>-1){
        this.baseUrl=window.location.origin.substring(0,window.location.origin.indexOf(window.location.hostname+':'))+window.location.hostname;
      }
      let subscription_create_defaults=this.create_defaults()
      .subscribe(
      res => {
          console.log(res.jwt['data']['message']);
          let subscription_pages=this.httpClient.get<{jwt: string}>(this.baseUrl+`/api/list-pages`).pipe(take(1))
          .subscribe(
          res => {
              let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
              console.log(deoodedRES.data.message);
              this.tempSearchedPages=deoodedRES.data.pages;
              this.pages.next(this.sort_pages());
              this.loadPageCSSScripts();
          });
          let subscription_social=this.httpClient.get<{jwt: string}>(this.baseUrl+`/api/update-social-links`).pipe(take(1))
          .subscribe(
          res => {
              let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
              this.social_links.next(deoodedRES.data.social_links)
          });
          this.subscriptions.push(subscription_pages,subscription_social);
      });
      this.subscriptions.push(subscription_create_defaults);
    }

  dispose(): void {
    this.subscriptions.forEach(subscription =>subscription.unsubscribe())
  }

  ngOnDestroy() {
    this.dispose();
  }

  public loadHomeScripts() {
      const externalScriptArray = [
          '/assets/js/main.js'
      ];
      for (let i = 0; i < externalScriptArray.length; i++) {
          const scriptTag = document.createElement('script');
          scriptTag.src = externalScriptArray[i];
          document.getElementsByTagName('body')[0].appendChild(scriptTag);
      }

  }

  sort_pages(){
    var filter_col='showing_order';
    var order_by='Asc';
    var search_string='';

    var newPages = this.tempSearchedPages.filter(function(page) {
      if (page.name.match(new RegExp(search_string, 'i'))) {
        return true;
      }
      return false;
    });
    this.tempSearchedPages=newPages;
    this.tempSearchedPages.sort(function (a, b) {
      var Aelement=filter_col=='name'?a.name:(filter_col=='id'?a.id:a.showing_order);
      var Belement=filter_col=='name'?b.name:(filter_col=='id'?b.id:b.showing_order);
      if (Aelement > Belement) {
        if(order_by=='Asc')return 1;
          return -1;
      }
      if (Belement > Aelement) {
        if(order_by=='Asc')return -1;
          return 1;
      }
      return 0;
    });
    return(this.tempSearchedPages);
  }

  private create_defaults(): Observable<any>{
    if(this.tempSearchedPages){
      let okO = new Subject<any>();
      let jwt={'data':{'message':'ok'}};
      okO.next(JSON.stringify(jwt));
      return okO;
    }
    let subscriptionDefaults=this.httpClient.get<{jwt: string}>(this.baseUrl+`/api/create-defaults`).pipe(take(1))
    return subscriptionDefaults;
  }

  loadPageCSSScripts() {
    for (let index = 0; index < this.tempSearchedPages.length; index++) {
      const page = this.tempSearchedPages[index];
      if(page.name==='News' && page.name==='Popular')continue;
      var cat_number=page.id+4;//cunki artiq cat-1 den cat-4e css styles teyin onub
      var back_color=page.category_color
      var css = `
      .nav-menu li.cat-${cat_number} a:after {
        background-color: #${back_color};
      }
      .nav-menu li.cat-${cat_number} a:hover, .nav-menu li.cat-${cat_number} a:focus {
        color: #${back_color};
      }
      .post-meta .post-category.cat-${cat_number} {
        background-color: #${back_color};
      }
      .category-widget ul li > a.cat-${cat_number} > span {
        background-color: #${back_color};
      }
      .category-widget ul li > a.cat-${cat_number}:hover, .category-widget ul li > a.cat-${cat_number}:focus {
        color: #${back_color};
      }
      `;
      let head = document.head || document.getElementsByTagName('head')[0];
      let style : any = document.createElement('style');

      head.appendChild(style);

      style.type = 'text/css';
      if (style.styleSheet){
        // This is required for IE8 and below.
        style.styleSheet.cssText = css;
      } else {
        style.appendChild(document.createTextNode(css));
      }
    }

  }

  public get_category_color_id(category_color_id,category){
    this.get_pages().subscribe(
      res => {
        for (let index = 0; index < res.length; index++) {
          const page = res[index];
          if(page.name===category){
            category_color_id.next(page.id+4);
            console.log('color found');
            break;
          }
        }
    });
  }

  public send_user_ip(){
    let subscriptionIPIFY=this.httpClient.get<{ip: string}>(`https://api.ipify.org/?format=json`).pipe(take(1))
    .subscribe(
      res => {
          let data={'user_ip':(res.ip),'request_uri':this.router.url};
          let subscriptionUserVisit=this.httpClient.post<{jwt: string}>(this.baseUrl+`/api/create-user-visit`,JSON.stringify(data)).pipe(take(1))
          .subscribe(
          res => {
              let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
              let parsedCA_user_visit=JSON.parse(deoodedRES.data.ca_user_visit.analytics_data);
              console.log('sui_visited_page_link: '+parsedCA_user_visit.visited_page_link);
              console.log('sui_http_referer: '+parsedCA_user_visit.http_referer);
          });
          this.subscriptions.push(subscriptionUserVisit);
      });
    this.subscriptions.push(subscriptionIPIFY);
  }

  public get_pages():Observable<any>{
    let subsPages : Observable<any>=this.pages as Observable<any>;
    return subsPages;
  }
  public get_social_links():Observable<any>{
    return this.social_links.asObservable();
  }
  public random_number(){
    return Math.random().toString();
  }
  

}
