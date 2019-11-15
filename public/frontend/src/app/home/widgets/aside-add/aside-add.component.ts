import { Component, OnInit, Input } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { JwtHelperService } from '@auth0/angular-jwt';

@Component({
  selector: 'app-aside-add',
  templateUrl: './aside-add.component.html',
  styleUrls: ['./aside-add.component.css']
})
export class AsideAddComponent implements OnInit {
  @Input() adNumber: string;
  ads=null;
  adUrl=null;
  baseUrl:string;

  constructor( private httpClient:HttpClient,
               private jwtHelper:JwtHelperService ) { }

  ngOnInit() {
    console.log('adNumber: '+this.adNumber);
    this.baseUrl=window.location.origin;
    if(window.location.origin.indexOf(window.location.hostname+':')>-1){
      this.baseUrl=window.location.origin.substring(0,window.location.origin.indexOf(window.location.hostname+':'))+window.location.hostname;
    }

    this.httpClient.get<{jwt: string}>(this.baseUrl+`/api/update-advertisements`,{params:{id:'1'}})
    .subscribe(
      res => {
        let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
        this.ads=deoodedRES.data.ads;
        // console.log(this.ads);
        if(this.adNumber==='1'){this.adUrl=this.ads.ads_picture_1}
        else if(this.adNumber==='2'){this.adUrl=this.ads.ads_picture_2}
        
      });
  }

}
