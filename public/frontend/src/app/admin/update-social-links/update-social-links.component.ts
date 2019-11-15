import { Component, OnInit } from '@angular/core';
import { JwtHelperService } from '@auth0/angular-jwt';
import { HttpClient } from '@angular/common/http';

@Component({
  selector: 'app-update-social-links',
  templateUrl: './update-social-links.component.html',
  styleUrls: ['./update-social-links.component.css']
})
export class UpdateSocialLinksComponent implements OnInit {
  social_links={facebook:'',twitter:'',telegram:''};

  constructor( public jwtHelper: JwtHelperService,
    public httpClient: HttpClient ) { }

ngOnInit() {

  this.httpClient.get<{jwt: string}>(`http://`+window.location.hostname+`/api/update-social-links`)
    .subscribe(
      res => {
        let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
        this.social_links=deoodedRES.data.social_links;
      });
}

update_social_links(evt){
  event.preventDefault();
  var myValue={facebook:evt.target.facebook.value,twitter:evt.target.twitter.value,telegram:evt.target.telegram.value};
  console.log(myValue);


  let data={'social_links':(myValue)};
  this.httpClient.post<{jwt: string}>(`http://`+window.location.hostname+`/api/update-social-links`,JSON.stringify(data))
  .subscribe(
    res => {
      let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
      console.log(deoodedRES.data.message);
    });
}

}
