import { Component, OnInit } from '@angular/core';
import { first } from 'rxjs/operators';

import { User } from '@/_models';
import { AuthenticationService, AlertService } from '@/_services';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';

@Component({ templateUrl: 'home.component.html' })
export class HomeComponent implements OnInit {
    currentUser: User;
    userName;
    userEmail;
    userId;
    loading = false;
    clicked = false;
    user:any = [];
    editForm: FormGroup;
    isEdit = false;
    constructor(
        private authenticationService: AuthenticationService,
        private alertService: AlertService,
        private formBuilder: FormBuilder,

    ) {
        this.currentUser = this.authenticationService.currentUserValue;
    }

    ngOnInit() {
        this.loadAllUsers();
    }

    deleteUser(id: number) {
        this.authenticationService.delete(id)
            .pipe(first())
            .subscribe(() => this.loadAllUsers());
    }

    private loadAllUsers() {
        this.loading = true;
        this.authenticationService.getLogin().pipe(first())
            .subscribe(data => {
                localStorage.setItem('email',data['data'].email);
                localStorage.setItem('name',data['data'].name);
                localStorage.setItem('id',data['data'].id);
            },
            error => {
                    this.alertService.error(error);
                    
                    this.loading = false;
            });
    }

    public showUserData(event){
        this.userEmail = localStorage.getItem('email');
        this.userName = localStorage.getItem('name');
        this.userId = localStorage.getItem('id');
        this.clicked = true;
    }

    public editUserDetail(){
        this.isEdit = true;
        this.loading = false;
        this.editForm = this.formBuilder.group({
            username: [this.userName, Validators.required],
            password: ['', Validators.required]
        });
    }
    // convenience getter for easy access to form fields
    get f() { return this.editForm.controls; }
    onSubmit() {
        
        // reset alerts on submit
        this.alertService.clear();

        // stop here if form is invalid
        if (this.editForm.invalid) {
            return;
        }

        this.loading = true;
        this.authenticationService.edit(this.f.username.value, this.f.password.value)
            .pipe(first())
            .subscribe(
                data => {
                    this.loading = false;
                    this.isEdit = false;
                    this.clicked = false;
                    this.ngOnInit();
                },
                error => {
                    this.alertService.error(error);
                    
                    this.loading = false;
                });
    }
}