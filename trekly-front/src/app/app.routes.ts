import { Routes } from '@angular/router';
import { LoginComponent } from './auth/login/login.component';
import { RegisterComponent } from './auth/register/register.component';
import { ProjectListComponent } from './projects/project-list/project-list.component';
import { ProjectCreateComponent } from './projects/project-create/project-create.component';
import { authGuard } from './auth/auth.guard';

export const routes: Routes = [
    { path: 'auth/login', component: LoginComponent },
    { path: 'auth/register', component: RegisterComponent },
    { path: 'projects', component: ProjectListComponent },
    { path: 'projects/create', component: ProjectCreateComponent, canActivate: [authGuard] },
    { path: '', redirectTo: 'projects', pathMatch: 'full' }
];
