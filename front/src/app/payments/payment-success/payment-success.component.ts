import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router } from '@angular/router';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';

@Component({
    selector: 'app-payment-success',
    standalone: true,
    imports: [CommonModule, MatCardModule, MatButtonModule, MatIconModule],
    template: `
    <div class="payment-result-container">
      <mat-card class="result-card success">
        <mat-card-header>
          <mat-icon class="result-icon">check_circle</mat-icon>
        </mat-card-header>
        <mat-card-content>
          <h2>Payment Successful!</h2>
          <p>Your project has been published and is now live.</p>
          <p>You will receive a QR code via email shortly.</p>
        </mat-card-content>
        <mat-card-actions>
          <button mat-raised-button color="primary" (click)="goToProjects()">
            View My Projects
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

    .result-card.success {
      border-top: 4px solid #4caf50;
    }

    .result-icon {
      font-size: 64px;
      width: 64px;
      height: 64px;
      color: #4caf50;
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
export class PaymentSuccessComponent {
    constructor(private router: Router) { }

    goToProjects() {
        this.router.navigate(['/projects']);
    }
}
