import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { JwtHelperService } from '@auth0/angular-jwt';
import { Location } from '@angular/common';
import { Router } from '@angular/router';
declare var jquery:any;
declare var $ :any;

@Component({
  selector: 'app-create-post',
  templateUrl: './create-post.component.html',
  styleUrls: ['./create-post.component.css']
})
export class CreatePostComponent implements OnInit {;
  pages=null;
  defaultPictureSRC='http://'+window.location.hostname+'/frontend/dist/frontend/assets/img/author.png';

  constructor( public jwtHelper: JwtHelperService,
              public httpClient: HttpClient,
              private location: Location,
              public router: Router ) { }

  ngOnInit() {
    this.initSummerNote(this.jwtHelper);

    this.httpClient.get<{jwt: string}>(`http://`+window.location.hostname+`/api/list-pages`)
    .subscribe(
    res => {
        let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
        console.log(deoodedRES.data);
        this.pages=deoodedRES.data.pages;
    });
    let myImageUploadUrl='http://'+window.location.hostname+'/api/upload-image';
    let myImageDeleteUrl='http://'+window.location.hostname+'/api/delete-image';
  }

  change_picture(input,which_picture){
    let evvelkiPImage=this.defaultPictureSRC;
    if (input.target.files && input.target.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            let e_target:FileReaderEventTarget=e.target as FileReaderEventTarget;
            $('.thumb_'+which_picture).attr('src', e_target.result);
        }
        reader.readAsDataURL(input.target.files[0]);
    }
    else {
        $('.thumb_'+which_picture).attr('src', evvelkiPImage);
    }
  }

  initSummerNote(jwtHelperProp){
    
    let jwtHelper=jwtHelperProp;
    let myImageUploadUrl='http://'+window.location.hostname+'/api/upload-image';
    let myImageDeleteUrl='http://'+window.location.hostname+'/api/delete-image';
    $(document).ready(function() {
      $('#summernote').summernote({
        minHeight:300,
        callbacks: {
          onImageUpload: function(files) {
              for(let i=0; i < files.length; i++) {
                  $.upload(files[i]);
              }
          },
          onMediaDelete : function(target) {
            // alert(target[0].src) 
            deleteFile(target[0].src);
          }
        },
      }).on("summernote.enter", function(we, e) {
          $(this).summernote("pasteHTML", "<br><br>");
          e.preventDefault();
        });

      $.upload = function (file) {
        $('progress').css('display','initial');
        let out = new FormData();
        out.append('file', file, file.name);
        $.ajax({
            method: 'POST',
            url: myImageUploadUrl,
            headers: {
              'Authorization': `Bearer ${sessionStorage.getItem('access_token')}`,
            },
            xhr: function() {
              var myXhr = $.ajaxSettings.xhr();
              if (myXhr.upload) myXhr.upload.addEventListener('progress',progressHandlingFunction, false);
              return myXhr;
            },
            contentType: false,
            cache: false,
            processData: false,
            data: out,
            success: function (img) {
              $('progress').css('display','none');
              let deoodedRES=jwtHelper.decodeToken(img.jwt);
              console.log(deoodedRES.data.message);
              console.log(deoodedRES.data.src);
              $('#summernote').summernote('insertImage', deoodedRES.data.src);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error(textStatus + " " + errorThrown);
            }
        });
      };
  
      function progressHandlingFunction(e){
        if(e.lengthComputable){
            $('progress').attr({value:e.loaded, max:e.total});

            if (e.loaded == e.total) {
                $('progress').attr('value','0.0');
            }
        }
      }
  
      function deleteFile(src) {
        $.ajax({
            headers: {
              'Authorization': `Bearer ${sessionStorage.getItem('access_token')}`,
            },
            data: JSON.stringify({src : src}),
            type: "POST",
            url: myImageDeleteUrl,
            cache: false,
            success: function(resp) {
                let deoodedRES=jwtHelper.decodeToken(resp.jwt);
                console.log(deoodedRES.data.message);
                // console.log(resp);
            }
        });
      }
    });
  }

  create_post(evt){
    event.preventDefault();
    var post_category=evt.target.post_category.value
    var post_title=evt.target.post_title.value
    var post_tags=evt.target.post_tags.value
    var editordata=evt.target.editordata.value
    var post_desc=evt.target.post_desc.value

    var author_name=evt.target.author_name.value
    var author_desc=evt.target.author_desc.value
    var author_picture=evt.target.author_picture.value
    

    var author_social_links={facebook:evt.target.asl_facebook.value,twitter:evt.target.asl_twitter.value,instagram:evt.target.asl_instagram.value};

    let formData:FormData = new FormData();
    formData.append('post_category', post_category);
    formData.append('post_title', post_title);
    formData.append('post_tags', post_tags);
    formData.append('content', editordata);
    formData.append('post_desc', post_desc);
    formData.append('author_name', author_name);
    formData.append('author_desc', author_desc);
    formData.append('author_picture', $('input[name="author_picture"]')[0].files[0], author_picture);
    formData.append('author_social_links', JSON.stringify(author_social_links));
    var myHtml = $('#summernote').summernote('code');
    
      let data={'post':(formData)};
      this.httpClient.post<{jwt: string}>(`http://`+window.location.hostname+`/api/create-post`,(formData))
          .subscribe(
          res => {
              let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
              console.log(deoodedRES.data.message);
              this.router.navigate(['/dashboard/list-posts']);
          });


  }

  goBack() {
    event.preventDefault();
    this.location.back();
  }

}

interface FileReaderEventTarget extends EventTarget {
  result:string
}

interface FileReaderEvent extends Event {
  target: FileReaderEventTarget;
  getMessage():string;
}
