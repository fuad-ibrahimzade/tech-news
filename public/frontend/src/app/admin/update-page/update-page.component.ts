import { Component, OnInit } from '@angular/core';
import { Location } from '@angular/common';
import { JwtHelperService } from '@auth0/angular-jwt';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-update-page',
  templateUrl: './update-page.component.html',
  styleUrls: ['./update-page.component.css']
})
export class UpdatePageComponent implements OnInit {
  page=null;
  id=null;

  constructor( public jwtHelper: JwtHelperService,
    public httpClient: HttpClient,
    public router: Router,
    private location: Location,
    private route: ActivatedRoute ) { }

  ngOnInit() {
    this.id = this.route.snapshot.paramMap.get('id');
    
    this.httpClient.get<{jwt: string}>(`http://`+window.location.hostname+`/api/read-page`,{params:{id:this.id}})
    .subscribe(
    res => {
        let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
        console.log(deoodedRES.data);
        this.page=deoodedRES.data.page;
    });
  }
  update_page(evt){
    event.preventDefault();
    var id=evt.target.id.value;
    var name=evt.target.name.value;
    var show_at_home=evt.target.show_at_home.value;
    var showing_order=evt.target.showing_order.value;
    var myValue={
      id:id,
      name:name,
      show_at_home:show_at_home,
      showing_order:showing_order
    };

    let data={'page':(myValue)};
    this.httpClient.post<{jwt: string}>(`http://`+window.location.hostname+`/api/update-page`,JSON.stringify(data))
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
