import { inject } from '@angular/core';
import { Router, CanActivateFn } from '@angular/router';
import { AuthService } from './auth.service';

export const authGuard: CanActivateFn = (route, state) => {
    const authService = inject(AuthService);
    const router = inject(Router);

    if (authService.currentUserValue) {
        // Check for role restriction on create route
        if (state.url.includes('/projects/create') && authService.currentUserValue.role === 'MEMBER') {
            // Redirect to projects list or show error
            return router.createUrlTree(['/projects']);
        }
        return true;
    }

    // Not logged in, redirect to login page
    return router.createUrlTree(['/auth/login']);
};
