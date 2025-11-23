import { Component, Inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MAT_DIALOG_DATA, MatDialogModule } from '@angular/material/dialog';
import { MatButtonModule } from '@angular/material/button';
import { StarRatingComponent } from '../star-rating/star-rating.component';
import { TranslatePipe } from '../../pipes/translate.pipe';

@Component({
    selector: 'app-project-details-dialog',
    standalone: true,
    imports: [
        CommonModule,
        MatDialogModule,
        MatButtonModule,
        StarRatingComponent,
        TranslatePipe
    ],
    template: `
    <h2 mat-dialog-title>{{ data.project.title }}</h2>
    <mat-dialog-content>
        <div class="creator-info" style="display: flex; align-items: center; margin-bottom: 20px; padding: 10px; background: #f5f5f5; border-radius: 8px;">
            <div style="flex: 1;">
                <h3 style="margin: 0;">{{ 'dialog.creatorStats' | translate }}</h3>
                <div style="display: flex; align-items: center; margin-top: 5px;">
                    <span style="font-weight: bold; margin-right: 5px;">{{ data.project.creatorRating | number:'1.1-1' }}</span>
                    <app-star-rating [rating]="data.project.creatorRating" [readonly]="true"></app-star-rating>
                    <span style="color: #666; margin-left: 5px;">({{ data.project.creatorTotalRatings }} {{ 'dialog.reviews' | translate }})</span>
                </div>
            </div>
        </div>

        <div class="project-details">
            <img [src]="data.project.imageUrl || '/assets/images/hiking.png'" style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px; margin-bottom: 15px;">
            
            <p><strong>{{ 'dialog.activity' | translate }}:</strong> {{ 'activities.' + data.project.activityType | translate }}</p>
            <p><strong>{{ 'dialog.date' | translate }}:</strong> {{ data.project.startDateTime | date:'medium' }}</p>
            <p><strong>{{ 'dialog.price' | translate }}:</strong> {{ data.project.price | currency:data.project.currency }}</p>
            <p><strong>{{ 'dialog.description' | translate }}:</strong></p>
            <p>{{ data.project.description }}</p>
        </div>
    </mat-dialog-content>
    <mat-dialog-actions align="end">
        <button mat-button mat-dialog-close>{{ 'common.close' | translate }}</button>
        <button mat-raised-button color="accent">{{ 'project.join' | translate }}</button>
    </mat-dialog-actions>
  `
})
export class ProjectDetailsDialogComponent {
    constructor(@Inject(MAT_DIALOG_DATA) public data: { project: any }) { }
}
