import { Component, signal } from '@angular/core';
import { RouterOutlet, RouterModule } from '@angular/router';
import { MatToolbarModule } from '@angular/material/toolbar';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatMenuModule } from '@angular/material/menu';
import { CommonModule } from '@angular/common';
import { Observable } from 'rxjs';
import { AuthService } from './auth/auth.service';
import { TranslationService } from './shared/services/translation.service';
import { TranslatePipe } from './shared/pipes/translate.pipe';
import { UiService } from './shared/services/ui.service';
import { provideHttpClient, withInterceptorsFromDi } from '@angular/common/http';

import { MatSidenavModule } from '@angular/material/sidenav';
import { MatListModule } from '@angular/material/list';

import { PaymentsComponent } from './payments/payments.component';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [
    RouterOutlet,
    RouterModule,
    MatToolbarModule,
    MatButtonModule,
    MatIconModule,
    MatMenuModule,
    MatSidenavModule,
    MatListModule,
    CommonModule,
    TranslatePipe
    , PaymentsComponent
  ],
  templateUrl: './app.html',
  styleUrls: ['./app.scss']
})
export class App {
  protected readonly title = signal('TREKLY');
  currentUser$: Observable<any>;
  currentLang$: Observable<string>;

  constructor(
    private authService: AuthService,
    public translationService: TranslationService,
    private uiService: UiService
  ) {
    this.currentUser$ = this.authService.currentUser;
    this.currentLang$ = this.translationService.currentLang$;
  }

  logout() {
    this.authService.logout();
  }

  changeLanguage(lang: string) {
    this.translationService.setLanguage(lang);
  }

  toggleSearch() {
    this.uiService.toggleSearch();
  }
}
