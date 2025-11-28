import { Component, Inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MAT_DIALOG_DATA, MatDialogModule, MatDialogRef } from '@angular/material/dialog';
import { MatButtonModule } from '@angular/material/button';

@Component({
    selector: 'app-project-success-dialog',
    standalone: true,
    imports: [CommonModule, MatDialogModule, MatButtonModule],
    template: `
        <h2 mat-dialog-title>¡Aventura Creada con Éxito!</h2>
        <mat-dialog-content>
            <p>Se ha enviado un correo con la información del evento.</p>
            
            <div *ngIf="data.price > 0" class="qr-info">
                <p><strong>Importante:</strong> Dado que tu evento tiene un costo, se ha enviado un código QR a tu correo.</p>
                <p>Debes usar este QR para registrar la llegada de los members cuando realicen el pago en el sitio del evento usando la app.</p>
            </div>
        </mat-dialog-content>
        <mat-dialog-actions align="end">
            <button mat-button color="primary" (click)="onClose()">Entendido</button>
        </mat-dialog-actions>
    `,
    styles: [`
        .qr-info {
            margin-top: 20px;
            padding: 15px;
            background-color: #e3f2fd;
            border-radius: 4px;
            border-left: 4px solid #2196f3;
        }
    `]
})
export class ProjectSuccessDialogComponent {
    constructor(
        public dialogRef: MatDialogRef<ProjectSuccessDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: { price: number }
    ) { }

    onClose(): void {
        this.dialogRef.close();
    }
}
