import { Component, OnInit } from '@angular/core';
import {AuthService} from "../services/auth.service";
import {Router} from "@angular/router";
import { HttpClient } from '@angular/common/http';
import { JwtHelperService } from '@auth0/angular-jwt';
declare var jquery:any;
declare var $ :any;

@Component({
  selector: 'dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.css']
})
export class DashboardComponent implements OnInit {
  authorized:boolean =true;
  userEmail:string='';
  pages=null;
  posts=null;
  baseUrlForAssets:string;

  constructor( public auth: AuthService,
               public router: Router, 
               public httpClient: HttpClient,
               public jwtHelper: JwtHelperService ) {
    this.baseUrlForAssets=window.location.origin;
    if(window.location.origin.indexOf(window.location.hostname+':')>-1){
        this.baseUrlForAssets=window.location.origin.substring(0,window.location.origin.indexOf(window.location.hostname+':'))+window.location.hostname;
    }
    this.loadScripts();
    this.auth.user.subscribe(
      user => {
        console.log(user);
        this.userEmail=user.email;
      }
    );
  }

  ngOnInit() {
      this.authorized=this.auth.loggedIn();
      this.userEmail=sessionStorage.getItem('email');
      this.httpClient.get<{jwt: string}>(`http://`+window.location.hostname+`/api/list-posts`)
      .subscribe(
      res => {
          let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
          this.posts=deoodedRES.data.posts;
      });
      this.httpClient.get<{jwt: string}>(`http://`+window.location.hostname+`/api/list-pages`)
      .subscribe(
      res => {
          let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
          this.pages=deoodedRES.data.pages;
      });

  }
    hasClass(ele,cls) {
        return ele.className.match(new RegExp('(\\s|^)'+cls+'(\\s|$)'));
    }

    removeClass(ele,cls) {
        if (this.hasClass(ele,cls)) {
            var reg = new RegExp('(\\s|^)'+cls+'(\\s|$)');
            ele.className=ele.className.replace(reg,' ');
        }
    }

    getSiblings = function (elem) {

        // Setup siblings array and get the first sibling
        var siblings = [];
        var sibling = elem.parentNode.firstChild;

        // Loop through each sibling and push to the array
        while (sibling) {
            if (sibling.nodeType === 1 && sibling !== elem) {
                siblings.push(sibling);
            }
            sibling = sibling.nextSibling
        }

        return siblings;

    };

  loadScripts() {
      const externalScriptArray = [
        this.baseUrlForAssets+'/assets/admin/js/metisMenu/metisMenu.min.js',
        this.baseUrlForAssets+'/assets/admin/js/sb-admin-2.js',
        this.baseUrlForAssets+'/assets/admin/js/jquery.validate.min.js'
      ];
      let scriptsElement=document.getElementById('scripts');
      while (scriptsElement.firstChild) {
          scriptsElement.removeChild(scriptsElement.firstChild);
      }
      for (let i = 0; i < externalScriptArray.length; i++) {
          const scriptTag = document.createElement('script');
          scriptTag.src = externalScriptArray[i];
          // document.getElementsByTagName('body')[0].appendChild(scriptTag);
          document.getElementById('scripts').appendChild(scriptTag);
      }

  }
  logout(){
      
  }dosomething

  (){

  }

}
