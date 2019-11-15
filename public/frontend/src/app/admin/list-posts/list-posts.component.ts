import { Component, OnInit } from '@angular/core';
import { JwtHelperService } from '@auth0/angular-jwt';
import { HttpClient } from '@angular/common/http';
declare var jquery:any;
declare var $ :any;

@Component({
  selector: 'app-list-posts',
  templateUrl: './list-posts.component.html',
  styleUrls: ['./list-posts.component.css']
})
export class ListPostsComponent implements OnInit {
  posts=null;
  tempSearchedPosts=null;
  pagination_config:any;

  constructor( public jwtHelper: JwtHelperService,
               public httpClient: HttpClient ) { }

  ngOnInit() {
    this.httpClient.get<{jwt: string}>(`http://`+window.location.hostname+`/api/list-posts`)
    .subscribe(
    res => {
        let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
        console.log(deoodedRES.data.message);
        this.posts=deoodedRES.data.posts;
        this.tempSearchedPosts=this.posts;

        this.pagination_config = {
          itemsPerPage: 10,
          currentPage: 1,
          totalItems: this.posts.length
        };
    });


  }

  click_all(){
    $('input[name="chk_group[]"]').each(function (index, element) {
      if(!$('input[name=chk_main]').prop('checked') && $(element).prop('checked')){
        $(element).prop('checked',false)
      }
      else if($('input[name=chk_main]').prop('checked')){
        $(element).trigger('click');
      }
    });
  }

  set_delete_id(id){
    event.preventDefault();
    $('#del_id').val(id);
    $("#realdelete").click();
  }

  delete_post(evt){
    event.preventDefault();
    $('#confirm-delete-0').modal('hide');
    let idArray=[];
    $('table').find('input[type="checkbox"]:checked').each(function(index, element) {
      let idElementParent=$(element).parent().parent().find('td');
      if(idElementParent && idElementParent.length>1){
        let idOfSelect=$(idElementParent[1]).text();
        idArray.push(idOfSelect);
      }
    });
    for (let index = 0; index < idArray.length; index++) {
      const IDelement = idArray[index];
      
      var myValue=IDelement;
    
      let data={'id':(myValue)};
      this.httpClient.post<{jwt: string}>(`http://`+window.location.hostname+`/api/delete-post`,JSON.stringify(data))
          .subscribe(
          res => {
              let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
              console.log(deoodedRES.data.message);
              window.location.reload();
          });
    }
  }

  filter_search(evt){
    event.preventDefault();
    var filter_col=evt.target.filter_col.value;
    var order_by=evt.target.order_by.value;
    var search_string=evt.target.search_string.value;

    var newPosts = this.tempSearchedPosts.filter(function(post) {
      let pos_categoryMatch=post.post_category.match(new RegExp(search_string, 'i'));
      let post_titleMatch=post.post_title.match(new RegExp(search_string, 'i'));
      let post_tagsMatch=post.post_tags.match(new RegExp(search_string, 'i'));
      let author_nameMatch=post.author_name.match(new RegExp(search_string, 'i'));
      if (pos_categoryMatch||post_titleMatch||post_tagsMatch||author_nameMatch) {
        return true;
      }
      return false;
    });
    this.posts=newPosts;
    this.posts.sort(function (a, b) {
      var Aelement=filter_col=='post_category'?a.post_category:(filter_col=='id'?a.id:(
        filter_col=='post_title'?a.post_title:(filter_col=='post_tags'?a.post_tags:(
          filter_col=='post_title'?a.author_name:(filter_col=='date_add'?a.date_add:a.date_upd)
        ))
      ));
      var Belement=filter_col=='post_category'?b.post_category:(filter_col=='id'?b.id:(
        filter_col=='post_title'?b.post_title:(filter_col=='post_tags'?b.post_tags:(
          filter_col=='post_title'?b.author_name:(filter_col=='date_add'?b.date_add:b.date_upd)
        ))
      ));
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


  }

  pageChanged(event){
    this.pagination_config.currentPage = event;
  }

}
