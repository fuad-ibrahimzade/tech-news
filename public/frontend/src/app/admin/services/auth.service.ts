import { Injectable } from '@angular/core';
import {HttpClient, HttpErrorResponse, HttpHeaders} from '@angular/common/http';
import { JwtHelperService } from '@auth0/angular-jwt';
import { Observable,Subject  } from  'rxjs';
import {User} from "../../models/User";
// import {RequestOptions} from "http";
import {catchError, tap} from "rxjs/operators";
import {throwError} from "rxjs/internal/observable/throwError";
import {Meta} from "@angular/platform-browser";
import {Router} from "@angular/router";

@Injectable({
    providedIn: 'root'
})
export class AuthService {

    // private user: Subject<User>;
    public user = new Subject<any>();
    // user: User;

    constructor(private httpClient: HttpClient, public jwtHelper: JwtHelperService, private meta: Meta,
                public router:Router) {
        // this.user=new Subject<User>();
        this.user.next(new User());
        // this.user.asObservable().subscribe(next => {
        //     console.log(next);
        // });
    }

    public isAuthenticated(): boolean {
        const token = sessionStorage.getItem('token');
        // Check whether the token is expired and return
        // true or false
        return !this.jwtHelper.isTokenExpired(token);
    }

    public login(email:string, password:string){
        // let headers = new Headers({ 'Content-Type': 'application/json' });
        // let options = new isAuthenticatedRequestOptions({ headers: headers });
        // let object=  json.strategies('email','password');
        // const formData: FormData = new FormData();
        // formData.append('data', JSON.stringify(resource));
        let headers = new HttpHeaders({
            "Content-Type": "application/json; charset=UTF-8",
            "Access-Control-Allow-Headers": "Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With",
        });
        // "Access-Control-Allow-Headers": "Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With",
        // if(!this.loggedIn()){
        //     sessionStorage.setItem('access_token', '-1');
        // }
        let options = { headers: headers };
        // access_token: sessionStorage.getItem('access_token')
        let data={
            email: email,
            password: password
        };
        // headers.append('Access-Control-Allow-Origin', 'http://localhost:3000');
        // headers.append('Access-Control-Allow-Credentials', 'true');

        // <{access_token:  string, email: string}>
        this.httpClient.post<{jwt: string}>(`http://`+window.location.hostname+`/api/login`,JSON.stringify(data))
            .subscribe(
                res => {
            let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
            let expiration=deoodedRES.exp;
            sessionStorage.setItem('access_token', deoodedRES.data.access_token);
            sessionStorage.setItem('email', deoodedRES.data.email);
            // this.user=new User({'email':res.email});
            // this.user= new Subject();
            let responseUser=new User();
            responseUser.email=deoodedRES.data.email;
            responseUser.access_token=deoodedRES.data.access_token;
            this.user.next(responseUser);
            if(this.loggedIn()){
                this.router.navigate(['dashboard']);
            }
            // this.user.subscribe(next => {
            //     console.log(next);
            // });
            // this.user.next(responseUser);
            },
                this.handleError);
            // .pipe(
            // tap(res => {
            //     console.log('All: ' + JSON.stringify(res));
            //     sessionStorage.setItem('access_token', res.access_token);
            //     sessionStorage.setItem('email', res.email);
            //     // this.user=new User({'email':res.email});
            //     // this.user= new Subject();
            //     let responseUser=new User();
            //     responseUser.email=res.email;
            //     responseUser.access_token=res.access_token;
            //     // this.user.subscribe(next => {
            //     //     console.log(next);
            //     // });
            //     this.user.next(responseUser);
            // }),
            // catchError(this.handleError)
        // );
        // return this.httpClient.post<User>(`/login`,data).pipe(
        //     tap(res => {
        //         console.log('All: ' + JSON.stringify(res));
        //         sessionStorage.setItem('access_token', res.access_token);
        //         sessionStorage.setItem('email', res.email);
        //         let tempUser=new User();
        //         tempUser.email=res.email;
        //         tempUser.access_token=res.access_token;
        //         this.user=tempUser;
        //     }),
        //     catchError(this.handleError)
        // );
    }

    public logout(): void{

        // this.user.next(null);
        // sessionStorage.removeItem('access_token');
        // sessionStorage.removeItem('email');
        // this.user.next();
        let data={'email':sessionStorage.getItem('email')};
        this.httpClient.post<{jwt: string}>(`http://`+window.location.hostname+`/api/logout`,JSON.stringify(data))
            .subscribe(
            res => {
                // let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
                let deoodedRES=this.jwtHelper.decodeToken(res.jwt);
                console.log(deoodedRES.data.message);
                sessionStorage.removeItem('access_token');
                sessionStorage.removeItem('email');
                this.user.next();
                if(!this.loggedIn()){
                    this.router.navigate(['/zxczxc']);
                    // element.click();
                    // location.reload();
                }
            },
            this.handleError);
        // ()=>{
        //     sessionStorage.removeItem('access_token');
        //     sessionStorage.removeItem('email');
        // }
        // sessionStorage.removeItem('access_token');
        // sessionStorage.removeItem('email');
    }

    public loggedIn(): any{
        // let datasi=this.httpClient.get<any>(`http://localhost/api/dashboard`);
        //
        //     // .pipe(catchError(this.handleError));
        // // console.log(datasi+'zzzzzzzzzzzz');
        // // datasi.subscribe(res => {
        // //     console.log(res+'zzzzz');
        // // });
        //
        // datasi.subscribe(
        //         res => console.log(res.email+'zzzzz'),
        //         error => console.warn('Problem getting people: ' + error),
        //          () => console.log('done'),
        //         );
        // console.log(sessionStorage.getItem('email'));
        // return this.user.asObservable().subscribe(next => {
        //     console.log(next);
        //     return next.access_token;
        // });
        return sessionStorage.getItem('access_token') !==  null && sessionStorage.getItem('access_token')!=='-1';
    }

    public csrf_token(): string{
        // let token = document.head.querySelector('meta[name="csrf-token"]');
        // return token.content;
        return this.meta.getTag('name="csrf-token"').content;
    }
    public loggedInStatus(): boolean{
        // $("meta[name=login-status]").attr('content')     pox cixdi yuklemir arada
        // return  document.head.querySelector("meta[name=login-status]").content;
        return this.meta.getTag('name="login-status"').content=='1';
    }

    // public getUser(): Observable<boolean> {
    //
    //     if (this.user !== null) {
    //
    //         return Observable.of(this.user);
    //
    //     } else {
    //
    //         return this.http.get('/authenticate?')
    //             .map((session: Response) => session.json())
    //             .catch(e => {
    //                 // If a server-side login gate returns an HTML page...
    //                 return Observable.of(false);
    //             });
    //
    //     }
    // }

    private handleError(error: HttpErrorResponse) {
        if (error.error instanceof ErrorEvent) {
            // A client-side or network error occurred. Handle it accordingly.
            console.error('An error occurred:', error.error.message);
        } else {
            // The backend returned an unsuccessful response code.
            // The response body may contain clues as to what went wrong,
            console.error(
                `Backend returned code ${error.status}, ` +
                `body was: ${error.error}`);
        }
        // return an observable with a user-facing error message
        return throwError(
            'Something bad happened; please try again later.');
    };
}
