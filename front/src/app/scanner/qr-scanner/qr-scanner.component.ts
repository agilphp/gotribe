import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatChipsModule } from '@angular/material/chips';
import { Html5Qrcode } from 'html5-qrcode';
import { ParticipationService, ValidationResult } from '../../shared/services/participation.service';
import { TranslatePipe } from '../../shared/pipes/translate.pipe';

interface QRData {
    participationId?: string;
    projectId?: string;
    creatorId?: string;
    userId?: string;
    action: string;
}

@Component({
    selector: 'app-qr-scanner',
    standalone: true,
    imports: [
        CommonModule,
        MatCardModule,
        MatButtonModule,
        MatIconModule,
        MatProgressSpinnerModule,
        MatChipsModule,
        TranslatePipe
    ],
    templateUrl: './qr-scanner.component.html',
    styleUrls: ['./qr-scanner.component.scss']
})
export class QrScannerComponent implements OnInit, OnDestroy {
    private html5QrCode?: Html5Qrcode;
    isScanning = false;
    validationResult: ValidationResult | null = null;
    error: string | null = null;
    isValidating = false;
    qrType: 'member' | 'creator' | null = null;

    constructor(private participationService: ParticipationService) { }

    ngOnInit(): void {
        this.html5QrCode = new Html5Qrcode('qr-reader');
    }

    ngOnDestroy(): void {
        this.stopScanning();
    }

    async startScanning(): Promise<void> {
        try {
            this.error = null;
            this.validationResult = null;
            this.qrType = null;
            this.isScanning = true;

            await this.html5QrCode!.start(
                { facingMode: 'environment' },
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 }
                },
                (decodedText) => {
                    this.onScanSuccess(decodedText);
                },
                (errorMessage) => {
                    // Scan error, ignore
                }
            );
        } catch (err: any) {
            this.error = err.message || 'Failed to start camera';
            this.isScanning = false;
        }
    }

    async stopScanning(): Promise<void> {
        if (this.html5QrCode && this.isScanning) {
            try {
                await this.html5QrCode.stop();
                this.isScanning = false;
            } catch (err) {
                console.error('Error stopping scanner:', err);
            }
        }
    }

    onScanSuccess(decodedText: string): void {
        this.stopScanning();

        try {
            const qrData: QRData = JSON.parse(decodedText);

            // Option A: Creator scans member's QR
            if (qrData.participationId && qrData.action === 'validate_attendance') {
                this.qrType = 'member';
                this.validateMemberTicket(qrData.participationId);
            }
            // Option B: Member scans creator's QR
            else if (qrData.projectId && qrData.creatorId && qrData.action === 'validate_member_payment') {
                this.qrType = 'creator';
                this.error = 'Creator QR detected. This feature requires member authentication. Please use the app to scan.';
                // In a full implementation, this would:
                // 1. Get current user's participation for this project
                // 2. Validate that participation
                // For now, show message to use proper flow
            }
            else {
                this.error = 'Invalid QR code format';
            }
        } catch (err) {
            this.error = 'Invalid QR code';
        }
    }

    validateMemberTicket(participationId: string): void {
        this.isValidating = true;
        this.error = null;

        this.participationService.validateTicket(participationId).subscribe({
            next: (result) => {
                this.validationResult = result;
                this.isValidating = false;
            },
            error: (err) => {
                this.error = err.error?.error || 'Validation failed';
                this.isValidating = false;
            }
        });
    }

    reset(): void {
        this.validationResult = null;
        this.error = null;
        this.qrType = null;
    }

    getQRTypeLabel(): string {
        if (this.qrType === 'member') {
            return 'Member Ticket';
        } else if (this.qrType === 'creator') {
            return 'Creator QR';
        }
        return '';
    }
}
