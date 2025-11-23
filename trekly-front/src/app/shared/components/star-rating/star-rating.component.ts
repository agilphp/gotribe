import { Component, Input, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatIconModule } from '@angular/material/icon';

@Component({
    selector: 'app-star-rating',
    standalone: true,
    imports: [CommonModule, MatIconModule],
    template: `
    <div class="star-rating">
      <mat-icon *ngFor="let star of stars; let i = index" 
                [class.filled]="i < rating" 
                (click)="rate(i + 1)"
                [style.cursor]="readonly ? 'default' : 'pointer'">
        {{ i < rating ? 'star' : 'star_border' }}
      </mat-icon>
    </div>
  `,
    styles: [`
    .star-rating {
      display: inline-flex;
      align-items: center;
    }
    mat-icon {
      color: #e0e0e0;
      font-size: 20px;
      width: 20px;
      height: 20px;
    }
    mat-icon.filled {
      color: #ffc107;
    }
  `]
})
export class StarRatingComponent {
    @Input() rating: number = 0;
    @Input() readonly: boolean = false;
    @Output() ratingChange = new EventEmitter<number>();

    stars = [1, 2, 3, 4, 5];

    rate(value: number) {
        if (!this.readonly) {
            this.rating = value;
            this.ratingChange.emit(value);
        }
    }
}
