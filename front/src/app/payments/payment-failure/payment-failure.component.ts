import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';

@Component({
    selector: 'app-payment-failure',
    standalone: true,
    imports: [CommonModule, MatCardModule, MatButtonModule, MatIconModule],
    template: `
    <div class="payment-result-container">
      <mat-card class="result-card failure">
        <mat-card-header>
          <mat-icon class="result-icon">error</mat-icon>
        </mat-card-header>
        <mat-card-content>
          <h2>Payment Failed</h2>
          <p>Unfortunately, your payment could not be processed.</p>
          <p>Please try again or contact support if the problem persists.</p>
        </mat-card-content>
        <mat-card-actions>
          <button mat-raised-button color="primary" (click)="goToProjects()">
            Back to Projects
          </button>
        </mat-card-actions>
      </mat-card>
    </div>
  `,
    styles: [`
    .payment-result-container {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 60vh;
      padding: 20px;
    }

    .result-card {
      max-width: 500px;
      text-align: center;
    }

    .result-card.failure {
      border-top: 4px solid #f44336;
    }

    .result-icon {
      font-size: 64px;
      width: 64px;
      height: 64px;
      color: #f44336;
      margin: 0 auto;
    }

    h2 {
      margin: 16px 0;
      color: #333;
    }

    p {
      color: #666;
      margin: 8px 0;
    }

    mat-card-actions {
      padding: 16px;
    }
  `]
})
export class PaymentFailureComponent {
    constructor(private router: Router) { }

    goToProjects() {
        this.router.navigate(['/projects']);
    }
}
