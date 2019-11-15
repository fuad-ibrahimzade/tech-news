import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { JwtHelperService } from '@auth0/angular-jwt';
import { Router } from '@angular/router';
import { Location } from '@angular/common';

@Component({
  selector: 'app-create-page',
  templateUrl: './create-page.component.html',
  styleUrls: ['./create-page.component.css']
})
export class CreatePageComponent implements OnInit {

  constructor( public jwtHelper: JwtHelperService,
               public httpClient: HttpClient,
               public router: Router,
               private location: Location ) { }

  ngOnInit() {
  }
  create_page(evt){
    event.preventDefault();
    var name=evt.target.name.value;
    var show_at_home=evt.target.show_at_home.value;
    var showing_order=evt.target.showing_order.value;
    var myValue={
      name:name,
      show_at_home:show_at_home,
      showing_order:showing_order
    };
    
      let data={'page':(myValue)};
      this.httpClient.post<{jwt: string}>(`http://`+window.location.hostname+`/api/create-page`,JSON.stringify(data))
          .subscribe(
          res => {
              let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
              console.log(deoodedRES.data.message);
              this.router.navigate(['/dashboard/list-pages']);
          });


  }
  goBack() {
    event.preventDefault();
    this.location.back();
  }

}
