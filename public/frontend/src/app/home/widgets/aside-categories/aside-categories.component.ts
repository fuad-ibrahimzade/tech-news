import { Component, OnInit, Input, SimpleChanges, Inject } from '@angular/core';
import { HomeScriptsService } from '../../services/home-scripts.service';
import { Subscription, Observable } from 'rxjs';
import { HttpErrorResponse } from '@angular/common/http';
import { IndexComponent } from '../../index/index.component';
import {throwError} from "rxjs/internal/observable/throwError";

@Component({
  selector: 'app-aside-categories',
  templateUrl: './aside-categories.component.html',
  styleUrls: ['./aside-categories.component.css']
})
export class AsideCategoriesComponent implements OnInit {
  private subscription: Subscription;
  public pages=null;

  constructor( @Inject(IndexComponent) public homeIndex: IndexComponent,
              private homeScriptService:HomeScriptsService ) {


  }

  ngOnInit() {


    
  }

}
