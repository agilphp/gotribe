import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';

import { ProjectService } from '../project.service';
import { AuthService } from '../../auth/auth.service';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';

@Component({
    selector: 'app-project-list',
    standalone: true,
    imports: [
        CommonModule,
        RouterModule,
        MatCardModule,
        MatButtonModule
    ],
    templateUrl: './project-list.component.html',
    styleUrls: ['./project-list.component.scss']
})
export class ProjectListComponent implements OnInit {
    projects: any[] = [];
    isLoggedIn$: Observable<boolean>;

    constructor(private projectService: ProjectService, private authService: AuthService) {
        this.isLoggedIn$ = this.authService.currentUser.pipe(map(user => !!user));
    }

    ngOnInit(): void {
        this.loadProjects();
    }

    loadProjects() {
        this.projectService.getProjects().subscribe(data => {
            this.projects = data;
        });
    }

    getImageForActivity(type: string): string {
        switch (type) {
            case 'HIKING': return '/assets/images/hiking.png';
            case 'RUNNING': return '/assets/images/running.png';
            case 'MTB': return '/assets/images/mtb.png';
            case 'TRIATHLON': return '/assets/images/triathlon.png';
            default: return '/assets/images/hiking.png';
        }
    }

    logout() {
        this.authService.logout();
    }
}
