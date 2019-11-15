import { Component, OnInit, Inject } from '@angular/core';
import { IndexComponent } from '../../index/index.component';

@Component({
  selector: 'app-aside-most-read',
  templateUrl: './aside-most-read.component.html',
  styleUrls: ['./aside-most-read.component.css']
})
export class AsideMostReadComponent implements OnInit {

  constructor( @Inject(IndexComponent) public homeIndex: IndexComponent ) { }

  ngOnInit() {
  }

}
