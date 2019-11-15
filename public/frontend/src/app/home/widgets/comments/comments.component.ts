import { Component, OnInit, Input } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { JwtHelperService } from '@auth0/angular-jwt';
import { Router } from '@angular/router';
declare var jquery:any;
declare var $ :any;

@Component({
  selector: 'home-comments',
  templateUrl: './comments.component.html',
  styleUrls: ['./comments.component.css']
})
export class CommentsComponent implements OnInit {
  @Input() postID: string=null;
  @Input() childCommentID: string=null;

  @Input() commentID: string =null;
  comments=null;
  child_comment=null;
  defaultCommentorProfilePicture=null;
  baseUrl:string;

  constructor( private httpClient:HttpClient,
               private jwtHelper:JwtHelperService,
               private router: Router ) { 
    this.router.routeReuseStrategy.shouldReuseRoute = function(){
      return false;
    }
    
    this.defaultCommentorProfilePicture=window.location.origin.substring(0,window.location.origin.indexOf(':'))+'://'+window.location.hostname+'/assets/img/avatar.png'
  }

  ngOnInit() {
    this.baseUrl=window.location.origin;
    if(window.location.origin.indexOf(window.location.hostname+':')>-1){
      this.baseUrl=window.location.origin.substring(0,window.location.origin.indexOf(window.location.hostname+':'))+window.location.hostname;
    }
    
    if(this.postID){
      this.httpClient.get<{jwt: string}>(this.baseUrl+`/api/list-comments`,{params:{post_id:this.postID}})
      .subscribe(
      res => {
          let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
          this.comments=deoodedRES.data.comments;
          this.comments.forEach(comment => {
            comment.child_comment_ids=(JSON.parse(comment.child_comment_ids));
          });
      });
    }
    else if(this.childCommentID){
      this.httpClient.get<{jwt: string}>(this.baseUrl+`/api/read-comment`,{params:{id:this.childCommentID}})
      .subscribe(
      res => {
          let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
          this.child_comment=deoodedRES.data.comment;
          this.child_comment.child_comment_ids=(JSON.parse(this.child_comment.child_comment_ids));
          this.postID=this.child_comment.post_id;
      });
    }

  }

  create_comment(evt,reply_comment_id,whichElement='form'){
    evt.preventDefault();
    
    if(!reply_comment_id){
      console.log('when reply_comment_id null');
    }
    else if(whichElement!=='form'){
      let closesForm=$(evt.target).parent().parent().find('form').parent();
      
      if($(closesForm).css('display')!=='none'){
        $(closesForm).css('display','none');
        $('#mainform').parent().css('display','initial');
        sessionStorage.setItem('oldCommentReplyFormButton',null);
        return;
      }
      else{
        $(closesForm).css('display','initial');
        if(sessionStorage.getItem('oldCommentReplyFormButton')!==null){
          $(sessionStorage.getItem('oldCommentReplyFormButton')).parent().parent().find('form').parent().css('display','none');
          if(sessionStorage.getItem('oldCommentReplyFormButton')===this.getPath(evt.target)){
            $(closesForm).css('display','inline');
          }
          sessionStorage.setItem('oldCommentReplyFormButton',this.getPath(evt.target));
        }
        if(sessionStorage.getItem('oldCommentReplyFormButton')===null){
          sessionStorage.setItem('oldCommentReplyFormButton',this.getPath(evt.target));
        }

        $([document.documentElement, document.body]).animate({
          scrollTop: $(closesForm).offset().top
        }, 200);
        if($('#mainform').parent().css('display')!=='none'){
          $('#mainform').parent().css('display','none');
        }
      }
    }
    if(whichElement!=='form'){
      return;
    }
    console.log('passed to submit');
    
    
    
    var name=evt.target.name.value
    var email=evt.target.email.value
    var message=evt.target.message.value
    var post_id=this.postID;
    var parent_comment_id=(!reply_comment_id)?'-1':reply_comment_id;
    var child_comment_id=JSON.stringify([]);

    let formData:FormData = new FormData();
    formData.append('name', name);
    formData.append('email', email);
    formData.append('message', message);
    formData.append('post_id', post_id);
    formData.append('parent_comment_id', parent_comment_id);
    formData.append('child_comment_id', child_comment_id);
    
      let data={'post':(formData)};
      this.httpClient.post<{jwt: any}>(this.baseUrl+`/api/create-comment`,(formData))
          .subscribe(
          res => {
              console.log(res.jwt.message);
              
              console.log(res.jwt.comment_received);
              console.log(res.jwt.comment_created);
              this.router.navigate(['/blog-post/'+this.postID]);
          });

  }

  hide_reply_forn(evt){
    $(evt.target).parent().parent().parent().css('display','none');
    $('#mainform').parent().css('display','initial');
  }

  previousElementSibling (element) {
    if (element.previousElementSibling !== 'undefined') {
      return element.previousElementSibling;
    } else {
      // Loop through ignoring anything not an element
      while (element = element.previousSibling) {
        if (element.nodeType === 1) {
          return element;
        }
      }
    }
  }
  getPath (element) {
    // False on non-elements
    if (!(element instanceof HTMLElement)) { return 'false'; }
    var path = [];
    while (element.nodeType === Node.ELEMENT_NODE) {
      var selector = element.nodeName;
      if (element.id) { selector += ('#' + element.id); }
      else {
        // Walk backwards until there is no previous sibling
        var sibling = element;
        // Will hold nodeName to join for adjacent selection
        var siblingSelectors = [];
        while (sibling !== null && sibling.nodeType === Node.ELEMENT_NODE) {
          siblingSelectors.unshift(sibling.nodeName);
          sibling = this.previousElementSibling(sibling);
        }
        // :first-child does not apply to HTML
        if (siblingSelectors[0] !== 'HTML') {
          siblingSelectors[0] = siblingSelectors[0] + ':first-child';
        }
        selector = siblingSelectors.join(' + ');
      }
      path.unshift(selector);
      element = element.parentNode;
    }
    return path.join(' > ');
  }


}
