import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { JwtHelperService } from '@auth0/angular-jwt';
import { Router } from '@angular/router';
declare var jquery:any;
declare var $ :any;

@Component({
  selector: 'app-update-advertisements',
  templateUrl: './update-advertisements.component.html',
  styleUrls: ['./update-advertisements.component.css']
})
export class UpdateAdvertisementsComponent implements OnInit {
  ads=null;
  id='1';

  constructor( public jwtHelper: JwtHelperService,
               public httpClient: HttpClient,
               private router:Router ) { }

  ngOnInit() {

    this.httpClient.get<{jwt: string}>(`http://`+window.location.hostname+`/api/update-advertisements`,{params:{id:'1'}})
      .subscribe(
        res => {
          let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
          this.ads=deoodedRES.data.ads;

          
        });
  }

  update_ads(evt){
    event.preventDefault();
    var ads_name_1=evt.target.ads_name_1.value
    var ads_name_2=evt.target.ads_name_2.value
    var ads_picture_1=evt.target.ads_picture_1.value
    var ads_picture_2=evt.target.ads_picture_2.value
    


    let formData:FormData = new FormData();
    formData.append('id', '1');
    formData.append('ads_name_1', ads_name_1);
    formData.append('ads_name_2', ads_name_2);
    if(ads_picture_1)formData.append('ads_picture_1', $('input[name="ads_picture_1"]')[0].files[0], ads_picture_1);
    if(ads_picture_2)formData.append('ads_picture_2', $('input[name="ads_picture_2"]')[0].files[0], ads_picture_2);
    
    let data={'post':(formData)};
    this.httpClient.post<{jwt: string}>(`http://`+window.location.hostname+`/api/update-advertisements`,(formData))
        .subscribe(
        res => {
            let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
            console.log(deoodedRES.data.message);
            this.router.navigate(['/dashboard']);
        });
  }

  change_picture(input,which_picture){
    let evvelkiPImage=which_picture==="ads_picture_1"?this.ads.ads_picture_1:this.ads.ads_picture_2;
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

}

interface FileReaderEventTarget extends EventTarget {
  result:string
}

interface FileReaderEvent extends Event {
  target: FileReaderEventTarget;
  getMessage():string;
}
