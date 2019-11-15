import { Component, OnInit } from '@angular/core';
import { JwtHelperService } from '@auth0/angular-jwt';
import { HttpClient } from '@angular/common/http';
declare var jquery:any;
declare var $ :any;

@Component({
  selector: 'app-update-contacts',
  templateUrl: './update-contacts.component.html',
  styleUrls: ['./update-contacts.component.css']
})
export class UpdateContactsComponent implements OnInit {

  constructor( public jwtHelper: JwtHelperService,
               public httpClient: HttpClient ) { }

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

    this.httpClient.get<{jwt: string}>(`http://`+window.location.hostname+`/api/update-contacts`)
    .subscribe(
    res => {
        let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
        $('#summernote').summernote('code',deoodedRES.data.editordata);
    });
  }

  update_contacts(evt){
    event.preventDefault();
    var myValue=evt.target.editordata.value;
    
      let data={'editordata':(myValue)};
      this.httpClient.post<{jwt: string}>(`http://`+window.location.hostname+`/api/update-contacts`,JSON.stringify(data))
          .subscribe(
          res => {
              let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
              console.log(deoodedRES.data.message);
          });


  }

}
