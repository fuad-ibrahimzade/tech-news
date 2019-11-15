import { Component, OnInit } from '@angular/core';
import { HomeScriptsService } from '../../services/home-scripts.service';

@Component({
  selector: 'home-footer',
  templateUrl: './footer.component.html',
  styleUrls: ['./footer.component.css']
})
export class FooterComponent implements OnInit {
  social_links={facebook:'',twitter:'',telegram:''};
  pages=null;

  constructor( public homeScriptService: HomeScriptsService ) {
    this.homeScriptService.get_pages().subscribe(
      res => {
        this.pages=res;
      });
    this.homeScriptService.get_social_links().subscribe(
      res => {
        this.social_links=res;
      });
  }

  ngOnInit() {
    // social_links
  }

}
