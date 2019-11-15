import { Component, OnInit } from '@angular/core';
import {AuthService} from "../services/auth.service";
import {Router} from "@angular/router";

@Component({
  selector: 'admin-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {


  constructor(public auth:AuthService, public router: Router) { }

  ngOnInit() {

  }

  login(evt){
      this.auth.login(evt.target.email.value,evt.target.password.value);

  }

}
