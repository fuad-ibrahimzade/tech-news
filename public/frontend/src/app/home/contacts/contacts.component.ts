import { Component, OnInit, OnDestroy } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { JwtHelperService } from '@auth0/angular-jwt';
import { HomeScriptsService } from '../services/home-scripts.service';

@Component({
  selector: 'home-contacts',
  templateUrl: './contacts.component.html',
  styleUrls: ['./contacts.component.css']
})
export class ContactsComponent implements OnInit,OnDestroy {
  baseUrl:string;

  constructor( public httpClient: HttpClient,
               public jwtHelper: JwtHelperService,
               private homeScriptService:HomeScriptsService ) { }

  ngOnInit() {
    this.baseUrl=window.location.origin;
    if(window.location.origin.indexOf(window.location.hostname+':')>-1){
      this.baseUrl=window.location.origin.substring(0,window.location.origin.indexOf(window.location.hostname+':'))+window.location.hostname;
    }
    this.homeScriptService.send_user_ip();
    let navsideclose:HTMLElement=document.getElementsByClassName('nav-aside-close')[0] as HTMLElement;
    navsideclose.click()

    this.httpClient.get<{jwt: string}>(this.baseUrl+`/api/update-contacts`)
    .subscribe(
    res => {
        let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
        document.getElementById('summerNoteData').innerHTML=deoodedRES.data.editordata;
    });
  }

  ngOnDestroy() {
    this.homeScriptService.dispose();
  }

}
