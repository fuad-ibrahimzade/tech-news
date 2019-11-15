import { Component, OnInit } from '@angular/core';
import { HomeScriptsService } from '../../services/home-scripts.service';
import { PlatformLocation } from '@angular/common';

@Component({
  selector: 'home-nav',
  templateUrl: './nav.component.html',
  styleUrls: ['./nav.component.css']
})
export class NavComponent implements OnInit {
  pages=null;
  baseUrlForAssets:string;
  social_links={facebook:'',twitter:'',telegram:''};

  constructor( public homeScriptService: HomeScriptsService,
               public location: PlatformLocation ) { 
    this.homeScriptService.get_pages().subscribe(
      res => {
        this.pages=res;
      });
    this.homeScriptService.get_social_links().subscribe(
      res => {
        this.social_links=res;
      });
  }

  ngOnInit() {
    this.baseUrlForAssets=window.location.origin;
    if(window.location.origin.indexOf(window.location.hostname+':')>-1){
      this.baseUrlForAssets=window.location.origin.substring(0,window.location.origin.indexOf(window.location.hostname+':'))+window.location.hostname;
    }
    
  }



}
