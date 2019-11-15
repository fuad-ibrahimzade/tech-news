import {Component} from '@angular/core';
import {ActivatedRoute, NavigationEnd, NavigationStart, Router} from "@angular/router";

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {
  title = 'Technews';
  parent_page='home';
  homecriptsAdded: boolean=false;
  constructor( public router: Router,
               public route: ActivatedRoute ) {
  }

  ngOnInit() {

      this.router.events.subscribe((evt) => {
          if ((evt instanceof NavigationEnd)) {
              if(!evt.url.match('/dashboard*')){this.swapComponent('home');}
              if(evt.url.match('/dashboard*')||evt.url.match('/admin*')){this.swapComponent('dashboard');}
              return;
          }
          var scrollToTop = window.setInterval(function() {
              var pos = window.pageYOffset;
              if ( pos > 0 ) {
                  window.scrollTo( 0, pos - 200 ); // how far to scroll on each step
              } else {
                  window.clearInterval( scrollToTop );
              }
          }, 16); // how fast to scroll
      });
  }

  swapComponent(pageName: string){
      this.parent_page=pageName;
  }
}
