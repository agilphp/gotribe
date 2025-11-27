import { Component, Inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatDialogRef, MAT_DIALOG_DATA, MatDialogModule } from '@angular/material/dialog';
import { MatButtonModule } from '@angular/material/button';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { FormsModule } from '@angular/forms';
import { StarRatingComponent } from '../star-rating/star-rating.component';
import { RatingService } from '../../services/rating.service';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';

@Component({
  selector: 'app-rate-dialog',
  standalone: true,
  imports: [
    CommonModule,
    MatDialogModule,
    MatButtonModule,
    MatFormFieldModule,
    MatInputModule,
    FormsModule,
    StarRatingComponent,
    MatSnackBarModule
  ],
  template: `
    <h2 mat-dialog-title>Rate Adventure</h2>
    <mat-dialog-content>
      <p>How was your experience with this adventure?</p>
      <div style="display: flex; justify-content: center; margin: 20px 0;">
        <app-star-rating [rating]="rating" [readonly]="false" (ratingChange)="rating = $event"></app-star-rating>
      </div>
      <mat-form-field appearance="fill" style="width: 100%;">
        <mat-label>Comment (Optional)</mat-label>
        <textarea matInput [(ngModel)]="comment" rows="4"></textarea>
      </mat-form-field>
    </mat-dialog-content>
    <mat-dialog-actions align="end">
      <button mat-button mat-dialog-close>Cancel</button>
      <button mat-raised-button color="primary" [disabled]="rating === 0" (click)="submitRating()">Submit</button>
    </mat-dialog-actions>
  `
})
export class RateDialogComponent {
  rating: number = 0;
  comment: string = '';

  constructor(
    public dialogRef: MatDialogRef<RateDialogComponent>,
    @Inject(MAT_DIALOG_DATA) public data: { projectId: string, creatorId: string },
    private ratingService: RatingService,
    private snackBar: MatSnackBar
  ) { }

  submitRating() {
      this.ratingService.rateCreator(this.data.creatorId, this.data.projectId, this.rating, this.comment).subscribe({
      next: () => {
        this.snackBar.open('Rating submitted successfully!', 'Close', { duration: 3000 });
        this.dialogRef.close(true);
      },
      error: (error) => {
        this.snackBar.open('Failed to submit rating: ' + (error.error?.error || 'Unknown error'), 'Close', { duration: 3000 });
      }
    });
  }
}
