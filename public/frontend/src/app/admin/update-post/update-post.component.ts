import { Component, OnInit } from '@angular/core';
import { Location } from '@angular/common';
import { JwtHelperService } from '@auth0/angular-jwt';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { ActivatedRoute } from '@angular/router';
import { HomeScriptsService } from 'src/app/home/services/home-scripts.service';
declare var jquery:any;
declare var $ :any;

@Component({
  selector: 'app-update-post',
  templateUrl: './update-post.component.html',
  styleUrls: ['./update-post.component.css']
})
export class UpdatePostComponent implements OnInit {
  post=null;
  id=null
  pages=null;
  initedPictures=null;

  constructor( public jwtHelper: JwtHelperService,
               public httpClient: HttpClient,
               public router: Router,
               private location: Location,
               private route: ActivatedRoute ) { 
  }

  ngOnInit() {
    this.initSummerNote(this.jwtHelper);
    // this.initPictures();
    
    this.httpClient.get<{jwt: string}>(`http://`+window.location.hostname+`/api/list-pages`)
    .subscribe(
    res => {
        let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
        this.pages=deoodedRES.data.pages;
    });

    this.id = this.route.snapshot.paramMap.get('id');
    this.httpClient.get<{jwt: string}>(`http://`+window.location.hostname+`/api/read-post`,{params:{id:this.id}})
    .subscribe(
    res => {
        let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
        console.log(deoodedRES.data.message);
        this.post=deoodedRES.data.post;
        this.post.author_social_links=JSON.parse(this.post.author_social_links)
        $('#summernote').summernote('code',deoodedRES.data.post.content);
        

    });
  }

  change_picture(input,which_picture){
    let evvelkiPImage=which_picture==="post_picture"?this.post.post_picture:this.post.author_picture;
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

  initPictures(){
    let scriptsElement=document.getElementById('summerNoteScript');
    while (scriptsElement.firstChild) {
        scriptsElement.removeChild(scriptsElement.firstChild);
    }
    let customScriptTag = document.createElement('script');
    customScriptTag.text=
    `
    $(document).ready(function() {
      let evvelkiPImage=$('.thumb_post_picture').attr('src');
      function readURL(input) {
          if (input.files && input.files[0]) {
              var reader = new FileReader();

              reader.onload = function (e) {
                  $('.thumb_post_picture').attr('src', e.target.result);
              }
              reader.readAsDataURL(input.files[0]);
          }
          else {
              $('.thumb_post_picture').attr('src', evvelkiPImage);
          }
      }
      $(".post_picture").change(function () {
        readURL(this);
      });

      let evvelkiAImage=$('.thumb_author_picture').attr('src');
      function readURL2(input) {
          if (input.files && input.files[0]) {
              var reader = new FileReader();

              reader.onload = function (e) {
                  $('.thumb_author_picture').attr('src', e.target.result);
              }
              reader.readAsDataURL(input.files[0]);
          }
          else {
              $('.thumb_author_picture').attr('src', evvelkiAImage);
          }
      }
      $(".author_picture").change(function () {
        readURL2(this);
      });
    });
    `
  }

  initSummerNote(jwtHelperProp){

    
    let jwtHelper=jwtHelperProp;
    let myImageUploadUrl='http://'+window.location.hostname+'/api/upload-image';
    let myImageDeleteUrl='http://'+window.location.hostname+'/api/delete-image';
    // let summerNoteScript=
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

  update_post(evt){
    event.preventDefault();
    var post_category=evt.target.post_category.value
    var post_title=evt.target.post_title.value
    var post_tags=evt.target.post_tags.value
    var editordata=evt.target.editordata.value
    var post_desc=evt.target.post_desc.value
    var post_picture=evt.target.post_picture.value

    var author_name=evt.target.author_name.value
    var author_desc=evt.target.author_desc.value
    var author_picture=evt.target.author_picture.value
    


    var author_social_links={facebook:evt.target.asl_facebook.value,twitter:evt.target.asl_twitter.value,instagram:evt.target.asl_instagram.value};
    var myValue={
      post_category:post_category,
      post_title:post_title,
      post_tags:post_tags,
      post_picture:$('input[name="post_picture"]')[0].files[0],
      editordata:editordata,
      author_name:author_name,
      author_desc:author_desc,
      author_picture:$('input[name="author_picture"]')[0].files[0],
      author_social_links:author_social_links
    };
    let formData:FormData = new FormData();
    formData.append('id', this.id);
    formData.append('post_category', post_category);
    formData.append('post_title', post_title);
    formData.append('post_tags', post_tags);
    formData.append('content', editordata);
    formData.append('post_desc', post_desc);
    if(post_picture)formData.append('post_picture', $('input[name="post_picture"]')[0].files[0], post_picture);
    formData.append('author_name', author_name);
    formData.append('author_desc', author_desc);
    if(author_picture)formData.append('author_picture', $('input[name="author_picture"]')[0].files[0], author_picture);
    formData.append('author_social_links', JSON.stringify(author_social_links));
    var myHtml = $('#summernote').summernote('code');
    
    let data={'post':(formData)};
    this.httpClient.post<{jwt: string}>(`http://`+window.location.hostname+`/api/update-post`,(formData))
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
