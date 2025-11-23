import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { Observable } from 'rxjs';
import { FormsModule } from '@angular/forms';

import { ProjectService } from '../project.service';
import { AuthService } from '../../auth/auth.service';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { StarRatingComponent } from '../../shared/components/star-rating/star-rating.component';
import { RatingService } from '../../shared/services/rating.service';
import { MatDialog, MatDialogModule } from '@angular/material/dialog';
import { RateDialogComponent } from '../../shared/components/rate-dialog/rate-dialog.component';
import { ProjectDetailsDialogComponent } from '../../shared/components/project-details-dialog/project-details-dialog.component';
import { ParticipationService } from '../../shared/services/participation.service';
import { TranslatePipe } from '../../shared/pipes/translate.pipe';

@Component({
    selector: 'app-project-list',
    standalone: true,
    imports: [
        CommonModule,
        RouterModule,
        MatCardModule,
        MatButtonModule,
        MatIconModule,
        StarRatingComponent,
        MatDialogModule,
        FormsModule,
        TranslatePipe
    ],
    templateUrl: './project-list.component.html',
    styleUrls: ['./project-list.component.scss']
})
export class ProjectListComponent implements OnInit {
    projects: any[] = [];
    filteredProjects: any[] = [];
    ratedProjectIds: Set<string> = new Set();
    joinedProjectIds: Set<string> = new Set();
    currentUser$: Observable<any>;

    searchFilters = {
        city: '',
        activity: '',
        date: '',
        price: ''
    };

    constructor(
        private projectService: ProjectService,
        private authService: AuthService,
        private dialog: MatDialog,
        private ratingService: RatingService,
        private participationService: ParticipationService
    ) {
        this.currentUser$ = this.authService.currentUser;
    }

    ngOnInit(): void {
        this.loadProjects();
    }

    loadProjects() {
        this.projectService.getProjects().subscribe(data => {
            this.projects = data;
            this.filteredProjects = data;
            this.loadRatings();
            this.loadMemberRatings();
            this.loadMemberParticipations();
        });
    }

    loadMemberRatings() {
        this.currentUser$.subscribe(user => {
            if (user && user.role === 'MEMBER') {
                this.ratingService.getMemberRatings(user.userId).subscribe(ratings => {
                    console.log('Member ratings fetched:', ratings);
                    this.ratedProjectIds = new Set(ratings.map(r => r.projectId));
                    console.log('Rated project IDs:', this.ratedProjectIds);
                }, error => {
                    console.error('Error fetching member ratings:', error);
                });
            }
        });
    }

    loadMemberParticipations() {
        this.currentUser$.subscribe(user => {
            if (user && user.userId) {
                this.participationService.getUserParticipations(user.userId).subscribe(participations => {
                    this.joinedProjectIds = new Set(participations.map(p => p.projectId));
                }, error => {
                    console.error('Error fetching participations:', error);
                });
            }
        });
    }

    loadRatings() {
        this.projects.forEach(project => {
            if (project.creatorId) {
                this.ratingService.getAverageRating(project.creatorId).subscribe(
                    (ratingData: any) => {
                        project.creatorRating = ratingData.averageRating ? parseFloat(ratingData.averageRating) : 0;
                        project.creatorTotalRatings = ratingData.totalRatings || 0;
                    },
                    (error) => {
                        console.error('Error fetching rating for creator', project.creatorId, error);
                        project.creatorRating = 0;
                        project.creatorTotalRatings = 0;
                    }
                );
            }
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

    openRateDialog(projectId: string, creatorId: string) {
        const dialogRef = this.dialog.open(RateDialogComponent, {
            width: '400px',
            data: { projectId, creatorId }
        });

        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.ratedProjectIds.add(projectId);
                this.loadRatings(); // Reload average ratings
            }
        });
    }

    openDetailsDialog(project: any) {
        this.dialog.open(ProjectDetailsDialogComponent, {
            width: '600px',
            data: { project }
        });
    }

    joinProject(projectId: string) {
        this.participationService.joinProject(projectId).subscribe(
            response => {
                console.log('Successfully joined project:', response);
                this.joinedProjectIds.add(projectId);
            },
            error => {
                console.error('Error joining project:', error);
                alert(error.error?.error || 'Failed to join project');
            }
        );
    }

    getRatedIdsArray() {
        return Array.from(this.ratedProjectIds);
    }

    applyFilters() {
        this.filteredProjects = this.projects.filter(project => {
            // Filter by city (meeting point contains city)
            if (this.searchFilters.city && !project.meetingPoint?.toLowerCase().includes(this.searchFilters.city.toLowerCase())) {
                return false;
            }

            // Filter by activity
            if (this.searchFilters.activity && project.activityType !== this.searchFilters.activity) {
                return false;
            }

            // Filter by date
            if (this.searchFilters.date) {
                const projectDate = new Date(project.startDateTime).toISOString().split('T')[0];
                if (projectDate !== this.searchFilters.date) {
                    return false;
                }
            }

            // Filter by price
            if (this.searchFilters.price) {
                const maxPrice = parseInt(this.searchFilters.price);
                if (this.searchFilters.price === '0') {
                    // Free only
                    if (project.price !== 0) {
                        return false;
                    }
                } else {
                    // Up to max price
                    if (project.price > maxPrice) {
                        return false;
                    }
                }
            }

            return true;
        });
    }
}
