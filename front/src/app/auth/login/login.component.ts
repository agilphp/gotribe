import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { AuthService } from '../auth.service';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { MatCardModule } from '@angular/material/card';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { TranslatePipe } from '../../shared/pipes/translate.pipe';
import { environment } from '../../../environments/environment';

@Component({
    selector: 'app-login',
    standalone: true,
    imports: [
        CommonModule,
        ReactiveFormsModule,
        RouterModule,
        MatCardModule,
        MatInputModule,
        MatButtonModule,
        MatSnackBarModule,
        TranslatePipe,
        MatIconModule
    ],
    templateUrl: './login.component.html',
    styleUrls: ['./login.component.scss']
})
export class LoginComponent {
    loginForm: FormGroup;
    private apiUrl = `${environment.apiUrl}/auth`;

    constructor(
        private fb: FormBuilder,
        private authService: AuthService,
        private router: Router,
        private snackBar: MatSnackBar
    ) {
        this.loginForm = this.fb.group({
            email: ['', [Validators.required, Validators.email]],
            password: ['', Validators.required]
        });
    }

    onSubmit() {
        if (this.loginForm.valid) {
            const { email, password } = this.loginForm.value;
            this.authService.login(email, password).subscribe({
                next: () => {
                    this.router.navigate(['/projects']);
                },
                error: error => {
                    this.snackBar.open('Login failed: ' + (error.error?.error || 'Unknown error'), 'Close', {
                        duration: 3000
                    });
                }
            });
        }
    }

    loginWithGoogle() {
        const width = 500;
        const height = 600;
        const left = (screen.width - width) / 2;
        const top = (screen.height - height) / 2;
        const authWindow = window.open(
            `${this.apiUrl}/google`,
            'GoogleLogin',
            `width=${width},height=${height},top=${top},left=${left}`
        );

        const poll = setInterval(() => {
            try {
                if (authWindow && authWindow.closed) {
                    clearInterval(poll);
                    return;
                }
                if (authWindow && authWindow.location.href.includes('/auth/google/callback')) {
                    // Try to read the response from the popup
                    const urlParams = new URLSearchParams(authWindow.location.search);
                    const token = urlParams.get('token');
                    const userJson = urlParams.get('user');

                    if (token && userJson) {
                        const user = JSON.parse(userJson);
                        this.authService.loginWithGoogleToken(token, user);
                        authWindow.close();
                        clearInterval(poll);
                        this.router.navigate(['/projects']);
                    }
                }
            } catch (e) {
                // Cross-origin errors are expected until redirect lands on our domain
            }
        }, 500);
    }
}
