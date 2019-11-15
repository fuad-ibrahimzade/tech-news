import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-richtext',
  templateUrl: './richtext.component.html',
  styleUrls: ['./richtext.component.css']
})
export class RichtextComponent implements OnInit {

  constructor() { }

  ngOnInit() {
  }

  loadScripts(){
    const externalScriptArray = [
      'https://cdnjs.cloudflare.com/ajax/libs/summernote/0.6.6/summernote.min.js',
      '//production-assets.codepen.io/assets/common/stopExecutionOnTimeout-b2a7b3fe212eaa732349046d8416e00a9dec26eb7fd347590fbced3ab38af52e.js',
      '/assets/admin/js/jquery.validate.min.js'
    ];
    let scriptsElement=document.getElementById('scripts');
    while (scriptsElement.firstChild) {
        scriptsElement.removeChild(scriptsElement.firstChild);
    }
    for (let i = 0; i < externalScriptArray.length; i++) {
        const scriptTag = document.createElement('script');
        scriptTag.src = externalScriptArray[i];
        // document.getElementsByTagName('body')[0].appendChild(scriptTag);
        document.getElementById('scripts').appendChild(scriptTag);
    }
    let scriptTag = document.createElement('script');
    scriptTag.type = 'text/javascript';
    scriptTag.text = `
      $(document).ready(function() {
        $('#code_preview0').summernote({height: 300});
        });
    `;
    document.getElementById('scripts').appendChild(scriptTag);
    scriptTag = document.createElement('script');
    scriptTag.text = `
      var content_row = 1;

      function addContent() {
        html = '<div id="content-row">';
        html += '<div class="form-group">';
        html += '<label class="col-sm-2">Page Content</label>';
        html += '<div class="col-sm-10">';
        html += '<textarea class="form-control" id="code_preview' + content_row + '" name="page_code[' + content_row + '][code]" style="height: 300px;"></textarea>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        $('#content-row').append(html);
        $('#code_preview' + content_row).summernote({height: 300});

        content_row++;
      }
      //# sourceURL=pen.js
    `;
    document.getElementById('scripts').appendChild(scriptTag);
  }

}
