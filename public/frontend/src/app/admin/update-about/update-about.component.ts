import { Component, OnInit } from '@angular/core';
import { JwtHelperService } from '@auth0/angular-jwt';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
declare var jquery:any;
declare var $ :any;

@Component({
  selector: 'app-update-about',
  templateUrl: './update-about.component.html',
  styleUrls: ['./update-about.component.css']
})
export class UpdateAboutComponent implements OnInit {
  defaultContent=null;

  constructor( public jwtHelper: JwtHelperService,
                public httpClient: HttpClient,
                public router: Router ) { }

  ngOnInit() {
    let scriptsElement=document.getElementById('summerNoteScript');
    while (scriptsElement.firstChild) {
        scriptsElement.removeChild(scriptsElement.firstChild);
    }
    let customScriptTag = document.createElement('script');
    customScriptTag.text=`
      $(document).ready(function() {
          $('#summernote').summernote({
            minHeight:300
          }).on("summernote.enter", function(we, e) {
            $(this).summernote("pasteHTML", "<br><br>");
            e.preventDefault();
            });;
      });
    `;
    document.getElementById('summerNoteScript').appendChild(customScriptTag);

    this.httpClient.get<{jwt: string}>(`http://`+window.location.hostname+`/api/update-about`)
    .subscribe(
    res => {
        let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
        $('#summernote').summernote('code',deoodedRES.data.editordata);
    });
    
  }

  update_about(evt){
    event.preventDefault();
    var myValue=evt.target.editordata.value
    var myHtml = $('#summernote').summernote('code');

    
      let data={'editordata':(myValue)};
      this.httpClient.post<{jwt: string}>(`http://`+window.location.hostname+`/api/update-about`,JSON.stringify(data))
          .subscribe(
          res => {
              let deoodedRES=this.jwtHelper.decodeToken(res.jwt);

          });


  }

}
