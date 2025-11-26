import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { ProjectService } from '../project.service';
import { AuthService } from '../../auth/auth.service';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { MatCardModule } from '@angular/material/card';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatSelectModule } from '@angular/material/select';
import { MatDatepickerModule } from '@angular/material/datepicker';
import { MatIconModule } from '@angular/material/icon';
import { MatDividerModule } from '@angular/material/divider';
import { TranslatePipe } from '../../shared/pipes/translate.pipe';
import { CurrencyService } from '../../shared/services/currency.service';
import { Currency } from '../../shared/models/currency.model';
import { OnInit } from '@angular/core';


@Component({
    selector: 'app-project-create',
    standalone: true,
    imports: [
        CommonModule,
        ReactiveFormsModule,
        RouterModule,
        MatCardModule,
        MatInputModule,
        MatButtonModule,
        MatSelectModule,
        MatSnackBarModule,
        MatDatepickerModule,
        MatIconModule,
        MatDividerModule,
        TranslatePipe
    ],
    templateUrl: './project-create.component.html',
    styleUrls: ['./project-create.component.scss']
})
export class ProjectCreateComponent implements OnInit {
    projectForm: FormGroup;
    imagePreview: string | null = null;
    selectedImage: string | null = null;
    currencies: Currency[] = [];


    constructor(
        private fb: FormBuilder,
        private projectService: ProjectService,
        private router: Router,
        private snackBar: MatSnackBar,
        private authService: AuthService,
        private currencyService: CurrencyService
    ) {
        this.projectForm = this.fb.group({
            title: ['', Validators.required],
            description: ['', Validators.required],
            activityType: ['HIKING', Validators.required],
            startDate: ['', Validators.required],
            startTime: ['', Validators.required],
            meetingPoint: ['', Validators.required],
            price: [0, [Validators.required, Validators.min(0)]],
            currency: ['COP', Validators.required]
        });
    }

    ngOnInit(): void {
        // Load currencies
        this.currencyService.getCurrencies().subscribe({
            next: (currencies) => {
                this.currencies = currencies;
            },
            error: (error) => {
                console.error('Error loading currencies:', error);
                // Fallback to default currencies if API fails
                this.currencies = [
                    { id: '1', code: 'USD', name: 'US Dollar', symbol: '$', isActive: true },
                    { id: '2', code: 'COP', name: 'Colombian Peso', symbol: '$', isActive: true },
                    { id: '3', code: 'EUR', name: 'Euro', symbol: '€', isActive: true }
                ];
            }
        });

        const user = this.authService.currentUserValue;
        if (user && user.role === 'MEMBER') {
            this.snackBar.open('You must be a Creator to create adventures.', 'Close', { duration: 3000 });
            this.router.navigate(['/projects']);
        }
    }

    onFileSelected(event: any) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e: any) => {
                this.imagePreview = e.target.result;
                this.selectedImage = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }

    onSubmit() {
        if (this.projectForm.valid) {
            const formValue = this.projectForm.value;

            // Combine Date and Time
            const date = new Date(formValue.startDate);
            const timeParts = formValue.startTime.split(':');
            date.setHours(parseInt(timeParts[0], 10));
            date.setMinutes(parseInt(timeParts[1], 10));

            const payload = {
                ...formValue,
                startDateTime: date.toISOString(),
                imageUrl: this.selectedImage
            };

            this.projectService.createProject(payload).subscribe({
                next: () => {
                    this.snackBar.open('Project created successfully!', 'Close', { duration: 3000 });
                    this.router.navigate(['/projects']);
                },
                error: (error: any) => {
                    this.snackBar.open('Failed to create project', 'Close', { duration: 3000 });
                }
            });
        }
    }
}
