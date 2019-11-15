import { Injectable } from '@angular/core';
import { HttpRequest, HttpInterceptor, HttpHandler, HttpEvent, HttpErrorResponse } from '@angular/common/http';
import { AuthService } from './auth.service';
import { catchError } from 'rxjs/operators';
import { throwError, Observable } from 'rxjs';

@Injectable()
export class AuthTokenInterceptor implements HttpInterceptor {

  constructor(public authService: AuthService) { }

//   Observable<HttpEvent<any>>
  intercept(request: HttpRequest<any>, next: HttpHandler): Observable<any> {

    if (sessionStorage.getItem('access_token')) {
      request = this.addToken(request, sessionStorage.getItem('access_token'));
    }

    return next.handle(request).pipe(catchError(error => {
      if (error instanceof HttpErrorResponse && error.status === 401) {
        // return this.handle401Error(request, next);//unauthorized
        return throwError(error)
      } else {
        return throwError(error);
      }
    }));
  }

  private addToken(request: HttpRequest<any>, token: string) {
    return request.clone({
      setHeaders: {
        'Authorization': `Bearer ${token}`
      }
    });
  }
}