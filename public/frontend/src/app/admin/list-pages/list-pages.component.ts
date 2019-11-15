import { Component, OnInit } from '@angular/core';
import { JwtHelperService } from '@auth0/angular-jwt';
import { HttpClient } from '@angular/common/http';
declare var jquery:any;
declare var $ :any;

@Component({
  selector: 'app-list-pages',
  templateUrl: './list-pages.component.html',
  styleUrls: ['./list-pages.component.css']
})
export class ListPagesComponent implements OnInit {
  pages=null;
  tempSearchedPages=null;

  constructor( public jwtHelper: JwtHelperService,
               public httpClient: HttpClient ) { }

  ngOnInit() {
    this.httpClient.get<{jwt: string}>(`http://`+window.location.hostname+`/api/list-pages`)
    .subscribe(
    res => {
        let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
        console.log(deoodedRES.data);
        this.pages=deoodedRES.data.pages;
        this.tempSearchedPages=this.pages;
    });
  }
  set_delete_id(id){
    event.preventDefault();
    $('#del_id').val(id);
    $("#realdelete").click();
    // $('#confirm-delete-0').modal('show');
  }
  delete_page(evt){
    event.preventDefault();
    $('#confirm-delete-0').modal('hide');
    console.log('asdasd');
    var myValue=evt.target.del_id.value;
    
      let data={'id':(myValue)};
      this.httpClient.post<{jwt: string}>(`http://`+window.location.hostname+`/api/delete-page`,JSON.stringify(data))
          .subscribe(
          res => {
              let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
              console.log(deoodedRES.data.message);
              window.location.reload();
          });
  }
  filter_search(evt){
    event.preventDefault();
    var filter_col=evt.target.filter_col.value;
    var order_by=evt.target.order_by.value;
    var search_string=evt.target.search_string.value;

    var newPages = this.tempSearchedPages.filter(function(page) {
      if (page.name.match(new RegExp(search_string, 'i'))) {
        return true;
      }
      return false;
    });
    this.pages=newPages;
    this.pages.sort(function (a, b) {
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
    // for(var i=0; i < nameOfArray.length; i++){
    //   if(nameOfArray[i].search('Tom H') > -1){
    //      resultArray.push(nameOfArray[i]); 
    //   }
    // }

  }

}
