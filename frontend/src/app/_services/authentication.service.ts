import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { BehaviorSubject, Observable } from 'rxjs';
import { map } from 'rxjs/operators';

import { User } from '@/_models';

@Injectable({ providedIn: 'root' })
export class AuthenticationService {
    
    private currentUserSubject: BehaviorSubject<User>;
    public currentUser: Observable<User>;

    constructor(private http: HttpClient) {
        this.currentUserSubject = new BehaviorSubject<User>(JSON.parse(localStorage.getItem('currentUser')));
        this.currentUser = this.currentUserSubject.asObservable();
    }

    public get currentUserValue(): User {
        return this.currentUserSubject.value;
    }

    register(name,email,password) {
        return this.http.post<any>(`${config.apiUrl}/api/auth/signup`, { name, email, password })
    }

    login(email, password) {
        let headers = new HttpHeaders();
        
        return this.http.post<any>(`${config.apiUrl}/api/auth/login`, { email, password })
            .pipe(map(user => {
                // store user details and jwt token in local storage to keep user logged in between page refreshes
                localStorage.setItem('currentUser', JSON.stringify(user));
                localStorage.setItem('Authorization',user.Authorization);
                this.currentUserSubject.next(user);
                return user;
            }));
    }

    logout() {
        // remove user from local storage and set current user to null
        localStorage.removeItem('currentUser');
        this.currentUserSubject.next(null);
    }

    getLogin(){
        let authorization = localStorage.getItem("Authorization");
        const headers= new HttpHeaders()
                .set('content-type', 'application/json')
                .set('Authorization',authorization);
        
        return this.http.get(`${config.apiUrl}/api/auth/user`, { 'headers': headers });
    }

    edit(name, password) {
        let authorization = localStorage.getItem("Authorization");
        const headers= new HttpHeaders()
                .set('content-type', 'application/json')
                .set('Authorization',authorization);
        return this.http.post<any>(`${config.apiUrl}/api/auth/edit`, { name, password },{headers});
    }

    delete(id: number) {
        let authorization = localStorage.getItem("Authorization");
        const headers= new HttpHeaders()
                .set('content-type', 'application/json')
                .set('Authorization',authorization);
        
        return this.http.get(`${config.apiUrl}/api/auth/delete`, { 'headers': headers });
    }
}