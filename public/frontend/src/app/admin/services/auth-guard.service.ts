import { Injectable } from '@angular/core';
import {Router, CanActivate} from '@angular/router';
import { AuthService } from './auth.service';

@Injectable({
  providedIn: 'root'
})
export class AuthGuardService implements CanActivate {

    constructor(public auth: AuthService, public router: Router) {}
    canActivate(): boolean {
        // this.router.events.pipe(take(1)).subscribe((evt) => {
        //     if ((evt instanceof NavigationEnd)) {
        //         if(evt.url!='/dashboard/login'){
        //             if(!this.auth.loggedIn()){this.router.navigate(['dashboard/login']);}
        //             return false;
        //         }
        //         if(evt.url=='/dashboard/login'){
        //             if(this.auth.loggedIn()){this.router.navigate(['dashboard']);}
        //             return false;
        //         }
        //         return true;
        //     }
        // });
        if(this.router.getCurrentNavigation().finalUrl.toString()==='/dashboard/logout'){
            if(this.auth.loggedIn()){this.auth.logout();}
            else { this.router.navigate(['/home']);}
            return false;
        }
        else if(this.router.getCurrentNavigation().finalUrl.toString()==='/dashboard/login' && this.auth.loggedIn()){
            this.router.navigate(['/dashboard']);
            return false;
        }
        else if (this.router.getCurrentNavigation().finalUrl.toString().match('/dashboard*') && !this.auth.loggedIn() &&
            this.router.getCurrentNavigation().finalUrl.toString()!=='/dashboard/login') {

            this.router.navigate(['dashboard/login']);
            return false;
        }
        return true;
    }



    private parseUrl(url: string, redirectTo: string) {
        const urlTokens = url.split('/');
        const redirectToTokens = redirectTo.split('/');

        let token = redirectToTokens.shift();

        while (token) {
            if (token !== '.' && token !== '..') {
                redirectToTokens.unshift(token);
                break;
            }

            if (token === '..') {
                urlTokens.pop();
            }

            token = redirectToTokens.shift();
        }

        urlTokens.push(...redirectToTokens);

        return urlTokens.join('/');
    }

}
