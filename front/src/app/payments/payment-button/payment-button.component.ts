import { Component, Input, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../environments/environment';

@Component({
    selector: 'app-payment-button',
    standalone: true,
    imports: [CommonModule, MatButtonModule, MatIconModule, MatProgressSpinnerModule],
    template: `
    <div class="payment-container">
      <div class="commission-info">
        <h3>Commission Payment Required</h3>
        <p>To publish your project, you need to pay a 10% commission:</p>
        <div class="amount">
          <span class="currency">{{ currency }}</span>
          <span class="value">{{ commissionAmount | number:'1.2-2' }}</span>
        </div>
        <p class="note">This helps us maintain the platform and support creators like you!</p>
      </div>

      <button 
        mat-raised-button 
        color="primary" 
        (click)="createPayment()"
        [disabled]="loading"
        class="payment-button">
        <mat-icon *ngIf="!loading">payment</mat-icon>
        <mat-spinner *ngIf="loading" diameter="20"></mat-spinner>
        <span *ngIf="!loading">Pay with Mercado Pago</span>
        <span *ngIf="loading">Processing...</span>
      </button>

      <p class="secure-note">
        <mat-icon>lock</mat-icon>
        Secure payment powered by Mercado Pago
      </p>
    </div>
  `,
    styles: [`
    .payment-container {
      padding: 24px;
      text-align: center;
      max-width: 500px;
      margin: 0 auto;
    }

    .commission-info {
      background: #f5f5f5;
      padding: 20px;
      border-radius: 8px;
      margin-bottom: 20px;
    }

    .commission-info h3 {
      margin: 0 0 12px 0;
      color: #333;
    }

    .commission-info p {
      margin: 8px 0;
      color: #666;
    }

    .amount {
      font-size: 36px;
      font-weight: bold;
      color: #1976d2;
      margin: 16px 0;
    }

    .currency {
      font-size: 20px;
      margin-right: 8px;
    }

    .note {
      font-size: 14px;
      font-style: italic;
      color: #999;
    }

    .payment-button {
      width: 100%;
      height: 48px;
      font-size: 16px;
      margin: 16px 0;
    }

    .payment-button mat-icon {
      margin-right: 8px;
    }

    .payment-button mat-spinner {
      display: inline-block;
      margin-right: 8px;
    }

    .secure-note {
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      color: #999;
      margin-top: 8px;
    }

    .secure-note mat-icon {
      font-size: 16px;
      height: 16px;
      width: 16px;
      margin-right: 4px;
    }
  `]
})
export class PaymentButtonComponent {
    @Input() projectId!: string;
    @Input() projectBudget!: number;
    @Input() userId!: string;
    @Input() currency: string = 'COP';

    @Output() paymentSuccess = new EventEmitter<void>();
    @Output() paymentError = new EventEmitter<string>();

    loading = false;
    private apiUrl = `${environment.apiUrl}/payments`;

    constructor(private http: HttpClient) { }

    get commissionAmount(): number {
        return this.projectBudget * 0.10;
    }

    createPayment() {
        this.loading = true;

        const payload = {
            project_id: this.projectId,
            project_budget: this.projectBudget,
            user_id: this.userId,
            currency: this.currency
        };

        this.http.post<any>(`${this.apiUrl}/create-preference`, payload).subscribe({
            next: (response) => {
                this.loading = false;
                // Redirect to Mercado Pago checkout
                window.location.href = response.checkout_url;
            },
            error: (error) => {
                this.loading = false;
                this.paymentError.emit(error.error?.error || 'Payment creation failed');
            }
        });
    }
}
